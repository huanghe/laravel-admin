<?php

namespace Encore\Admin\Requests;

use Dingo\Api\Http\FormRequest;

class AuthorizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //用户名或者email
            'username' => 'required|string|between:5,60',
            'password' => 'required|string|between:5,20',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => '用户名不能为空。',
            'username.between' => '用户名必须介于 5 - 60 个字符之间。',
            'password.required' => '密码不能为空。',
            'password.between' => '密码必须介于 5 - 20 个字符之间。',
        ];
    }
}
