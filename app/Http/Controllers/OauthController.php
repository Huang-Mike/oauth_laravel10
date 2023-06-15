<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\GetOtpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\GetTokenRequest;
use App\Http\Controllers\OtpController;
use App\Http\Requests\StoreCodeRequest;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\ClientsController;
use App\Http\Requests\AuthenticationRequest;
use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\TokenExchangeController;

class OauthController extends Controller
{
    protected OtpController $otpCon;
    protected CodeController $codeCon;
    protected ClientsController $clientsCon;
    protected AccessTokenController $accessTokenCon;
    protected UserPasswordController $userPasswordCont;
    protected TokenExchangeController $tokenExchangeCon;

    public function __construct (
        OtpController $otpCon,
        CodeController $codeCon,
        ClientsController $clientsCon,
        AccessTokenController $accessTokenCon,
        UserPasswordController $userPasswordCont,
        TokenExchangeController $tokenExchangeCon
    )
    {
        $this->otpCon = $otpCon;
        $this->codeCon = $codeCon;
        $this->clientsCon = $clientsCon;
        $this->accessTokenCon = $accessTokenCon;
        $this->userPasswordCont = $userPasswordCont;
        $this->tokenExchangeCon = $tokenExchangeCon;
    }

    /**
     * Create a 10 minutes code,
     */
    public function getCode(StoreCodeRequest $request)
    {
        try {
            $client = $this->clientsCon->isAvailable($request->client_id, $request->client_secret);
            if (empty($client)) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            DB::beginTransaction();
            $code = $this->codeCon->store($client->id);
            DB::commit();

            return response()->json(['code' => $code->id], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 404);
        }
    }

    /**
     * Login, register, bind view
     *
     * @param Request $request
     * @return View
     */
    public function form(Request $request)
    {
        if (empty($error = $this->codeCon->check($request->code ?? ''))) {
            $code = $this->codeCon->isAvailable($request->code);
            $client = $code->client;
            $config = json_decode($client->config, true);
            $routeName = $request->route()->getName();
            $exchangeTypeUrl = ($routeName == 'login') ? route('register', ['code' => $code->id]) : route('login', ['code' => $code->id]);
            
            if ($config['auth']['verify_type'] == 'password') {
                $formUrl = ($routeName == 'login') ? route('password.login', ['code' => $code->id]) : route('password.register', ['code' => $code->id]);
            } else if ($config['auth']['verify_type'] == 'otp') {
                $formUrl = route('otp.get', ['code' => $code->id]);
                $verifyOtpUrl = ($routeName == 'login') ? route('otp.login', ['code' => $code->id]) : route('otp.register', ['code' => $code->id]);
            }
        }

        return view('client-form', [
            'client' => $client ?? null,
            'config' => empty($error) ? $config : null,
            'error' => isset($error) ? json_encode($error) : null,
            'formUrl' => $formUrl ?? null,
            'verifyOtpUrl' => $verifyOtpUrl ?? null,
            'routeName' => $routeName ?? null,
            'exchangeTypeUrl' => $exchangeTypeUrl ?? null,
        ]);
    }

    /**
     * Get OTP Code.
     */
    public function otpGet(GetOtpRequest $request)
    {
        try {
            if ($error = $this->codeCon->check($request->code ?? '')) {
                return response()->json(['message' => $error[0]['message']], $error[0]['statusCode']);
            }
            
            DB::beginTransaction();
            $code = $this->codeCon->isAvailable($request->code);
            $client = $code->client;
            $config = json_decode($client->config, true);
            $primaryKey = $config['auth']['primary_key'];

            /* Revoke existing */
            if ($otpCheck = $this->otpCon->isExist($request->code)) {
                $this->otpCon->revoke($otpCheck->id);
            }

            $params = [
                'code_id' => $request->code,
                'primary_key' => $request->$primaryKey
            ];

            $otp = $this->otpCon->create($params);

            /* If User exists, set the user's OTP password in the table UserPassword. */
            $user = User::where('client_id', $client->id)->where($primaryKey, $request->$primaryKey)->first();
            if ($user) {
                $user->verifyType->update([
                    'otp' => Hash::make($otp->otp_code)
                ]);
            }

            if ($primaryKey == 'phone') {
                $send = $this->otpCon->sms($client->name, $request->$primaryKey, $otp->otp_code);
            } else {
                $send = $this->otpCon->email($client->name, $request->$primaryKey, $otp->otp_code);
            }

            DB::commit();
            return response()->json([
                'message' => 'success',
                'expired_at' => $otp->expired_at
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 404);
        }
    }

    /**
     *
     * @param AuthenticationRequest $request
     * @return Json
     */
    public function register(AuthenticationRequest $request)
    {
        try {
            if ($error = $this->codeCon->check($request->code ?? '')) {
                return response()->json(['message' => $error[0]['message']], $error[0]['statusCode']);
            }
            $routeName = $request->route()->getName();
            $routeArray = explode('.', $routeName);
            $type = (in_array('register', $routeArray)) ? 'register' : 'bind';
            $code = $this->codeCon->isAvailable($request->code);
            $client = $code->client;
            $config = json_decode($client->config, true);
            $verifyType = $config['auth']['verify_type'];

            $params = [
                'client_id' => $client->id,
                'name' => $request->name,
                'email' => $request->email,
                'isd_code' => $request->isdCode,
                'phone' => $request->phone,
                'password' => Hash::make($request->$verifyType)
            ];
    
            DB::beginTransaction();
            $user = User::create($params);

            $this->userPasswordCont->create([
                'user_id' => $user->id,
                $verifyType => Hash::make($request->$verifyType)
            ]);

            $this->codeCon->revoke($code->id);
            if ($verifyType == 'otp') {
                $otp = $this->otpCon->isExist($code->id);
                $this->otpCon->revoke($otp->id);
            }
            DB::commit();

            $tokenParams = [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $user->email,
                'password' => $request->$verifyType
            ];
            
            $grant = $this->tokenExchangeCon->createTokenGrant($user->id, $tokenParams);

            if (empty($grant)) {
                DB::rollBack();
                $statusCode = 500;
                $response['message'] = 'Failed to create Token, please contact the engineer.';
            } else {
                $statusCode = 200;
                $response['message'] = __('client-form.success', ['attribute' => __("client-form.$type")]);
                $response['redirect_url'] = $client->redirect . "?grant=" . $grant;
            }

            return response()->json($response, $statusCode);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 404);
        }
    }

    /**
     *
     * @param AuthenticationRequest $request
     * @return Json
     */
    public function login(AuthenticationRequest $request)
    {
        if ($error = $this->codeCon->check($request->code ?? '')) {
            return response()->json(['message' => $error[0]['message']], $error[0]['statusCode']);
        }
        $code = $this->codeCon->isAvailable($request->code);
        $client = $code->client;
        $config = json_decode($client->config, true);
        $key = $request->password ? 'password' : 'otp';

        /* Check user exist. */
        $user = User::where('client_id', $client->id)->where($config['auth']['primary_key'], $_POST[$config['auth']['primary_key']])->first();

        /* Update user's password as table UserPassword's otp column. */
        $user->update([
            'password' => $user->verifyType->$key
        ]);

        /* Check password by attempt */
        $checkLogin = Auth::guard('passport-login')->attempt([
            'email' => $user->email ?? '',
            'password' => $request->$key
        ]);

        if ($checkLogin) {
            DB::beginTransaction();
            /* Clear redundant tokens */
            if ($accessToken = $this->accessTokenCon->findExist($client->id, $user->id)) {
                $this->accessTokenCon->revoke($accessToken->id);
                $refreshToken = $accessToken->refreshToken;
                $refreshToken->update(['revoked' => 1]);
            }

            $this->codeCon->revoke($code->id);
            DB::commit();

            $tokenParams = [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $user->email,
                'password' => $request->$key
            ];

            $grant = $this->tokenExchangeCon->createTokenGrant($user->id, $tokenParams);

            if (empty($grant)) {
                DB::rollBack();
                $statusCode = 500;
                $response['message'] = __('client-form.error');
            } else {
                $statusCode = 200;
                $response['message'] = __('client-form.login_success', ['attribute' => __("client-form.login")]);
                $response['redirect_url'] = $client->redirect . "?grant=" . $grant;
            }

            return response()->json($response, $statusCode);
        }
        return response()->json(['message' => __('client-form.wrong_password')], 400);
    }

    /**
     * Use grant to exchange tokens.
     *
     * @param GetTokenRequest $request
     * @return Json
     */
    public function getTokens(GetTokenRequest $request)
    {
        try {
            $client = $this->clientsCon->isAvailable($request->client_id, $request->client_secret);
            if (empty($client)) {
                return response()->json(['message' => 'Client not found'], 404);
            }

            $exchange = $this->tokenExchangeCon->exchange($request->grant);

            $statusCode = $exchange ? 200 : 401;
            $message = $exchange ? json_decode($exchange->tokens) : ['message' => 'This grant is invalid'];

            return response()->json($message, $statusCode);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 404);
        }
    }

}
