<?php
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OauthController;

/* Get Time-Sensitive Coding */
Route::post('/authorize', [OauthController::class, 'getCode']);

/* Form page entry point */
Route::get('/login', [OauthController::class, 'form'])->name('login');
Route::get('/register', [OauthController::class, 'form'])->name('register');
Route::get('/bind', [OauthController::class, 'form'])->name('bind');

/* Get OTP verification code */
Route::post('/otp-get', [OauthController::class, 'otpGet'])->name('otp.get');
/* OTP register login */
Route::post('/otp-register', [OauthController::class, 'register'])->name('otp.register');
Route::post('/otp-login', [OauthController::class, 'login'])->name('otp.login');

/* Password register login */
Route::post('password-register', [OauthController::class, 'register'])->name('password.register');
Route::post('password-login', [OauthController::class, 'login'])->name('password.login');

/* Exchange token */
Route::post('/oauth-token', [OauthController::class, 'getTokens'])->name('oauth.token');

/* Change language */
Route::get('/change-language/{locale}', [Controller::class, 'changeLang'])->name('change.lang');

