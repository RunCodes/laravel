<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserLoginVerify;
use App\Http\Requests\UserRegisterVerify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class UserController extends Controller
{
	public function login(UserLoginVerify $request){

		$credentials = $request->validated();

        $remember = $request->input('remember');
        $remember = $remember  == 'on' ? true : false;

		if (Auth::attempt($credentials, $remember)){
            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'redirect' => $user->name == 'admin' ? route('dev') : route('limited'),
            ]);
        }
        return response()->json([
            'message' => 'name or password is incorrect',
        ],500);
	}


    public function register(UserRegisterVerify $request) {

        $credentials = $request->validated();

        $user = User::create([
            'name' => $credentials['name'],
            'password' => Hash::make($credentials['password']),
        ]);

        Auth::login($user);

        return response()->json([
            'message' => 'register success',
            'redirect' => route('dev')
        ]);
    }


    /**
     * 退出登录
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'logout success',
            'redirect' => '/'
        ]);
    }



}
