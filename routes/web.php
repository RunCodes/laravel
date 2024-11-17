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

// 需要登录的路由组
Route::middleware('auth')->prefix('admin')->group(function () {    

    Route::controller(App\Http\Controllers\UserController::class)->group(function () {
        Route::get('logout','logout');
    });

    Route::controller(App\Http\Controllers\ExecuteHandleController::class)->group(function () {
        Route::post('/execute', 'execute');
    });

    Route::get('/limited', function () {
        if (Gate::allows('is-admin',Auth::user())) {  return redirect()->route('dev');}
        return view('limited');
    })->name('limited');

    Route::get('/dev', function () {
        if (Gate::allows('is-admin',Auth::user())) return view('dev'); //判断有无权限
        return redirect()->route('limited'); 
    })->name('dev');


    Route::controller(App\Http\Controllers\DownloadController::class)->group(function(){
        Route::get('/consult', 'consult');
        Route::get('/download', 'download');
    });


});