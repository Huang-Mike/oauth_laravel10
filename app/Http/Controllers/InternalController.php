<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\OperatorFromRequest;

class InternalController extends Controller
{
    public function form(Request $request)
    {
        $routeName = $request->route()->getName();
        $actionArr = explode('.', $routeName);
        $action = in_array('login', $actionArr) ? 'login' : 'register';
        $ajaxUrl = route("admin.$action");

        return view("auth.$action", [
            'action' => $action,
            'ajaxUrl' => $ajaxUrl,
        ]);
    }

    public function login(OperatorFromRequest $request)
    {
        $data = $request->only(['email', 'password']);
        if (Auth::attempt($data)) {
            return redirect()->route('admin.dashboard');
        }
        return response()->json(['message' => '帳號密碼錯誤'], 400);
    }

    public function register(OperatorFromRequest $request)
    {
        $data = $request->only(['name', 'email', 'password']);
        $data['password'] = Hash::make($data['password']);
        if (Operator::create($data)) {
            return redirect()->route('admin.dashboard');
        }
        return response()->json(['message' => '註冊異常'], 400);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('admin.login.view');
    }

    public function index(Request $request)
    {
        return view("dashboard");
    }

}
