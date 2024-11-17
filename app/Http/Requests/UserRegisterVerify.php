<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterVerify extends FormRequest
{

    /**
     * rules验证失败则不往下
     */
    protected $stopOnFirstFailure = true;

    /**
     * 失败后的重定向
     */
    protected $redirect = '/login';


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:10|unique:users,name',
            'password' => 'required|min:6|max:32',
        ];
    }

    /**
     * 返回错误信息
     */
    public function messages()
    {
        return [
            'name.required' => 'The user name cannot be empty',
            'name.max' => 'The user name cannot exceed 10 characters',
            'name.unique' => 'The user name already exists',
            'password.required' => 'The password cannot be empty',
            'password.min' => 'The password must contain at least 6 characters',
            'password.max' => 'The password contains a maximum of 32 characters'
        ];
    }
}
