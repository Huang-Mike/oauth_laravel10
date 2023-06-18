<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/**
 * Get User data and verify login status through access_token.
 */
Route::middleware(['auth:api'])->get('/user', function (Request $request) {
    $data = $request->user();
    return response()->json([
        'id' => $data->id,
        'name' => $data->name,
        'email' => $data->email,
        'phone' => $data->phone
    ], 200);
});