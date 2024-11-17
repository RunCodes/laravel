<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dev');
    }
    return redirect()->route('login');
});

//不需要登录授权的路由
Route::middleware('guest')->prefix('admin')->group(function () {  

    Route::controller(App\Http\Controllers\UserController::class)->group(function () {

        Route::view('/login', 'user/login')->name('login'); 
        Route::view('/register', 'user/register');

        Route::post('/login', 'login');
        Route::post('/register', 'register');
    });
});

