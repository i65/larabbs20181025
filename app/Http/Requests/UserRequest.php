<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UserRequest extends FormRequest
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
            //
            'name' => 'required|between:3,25|regex:/^[A-za-z0-9\-\_]+$/|unique:users,name,'. Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
            'avatar' => 'mimes:jpeg, bmp, gif, jpg, png|dimensions:min_width=200,min_height=200',
        ];
    }

    public function messages()
    {
        return [
            'avatar.mimes' => '头像必须是 jpeg, bmp, gif, jpg, png 格式',
            'avatar.dimensions' => '图片清晰度不够，宽和高需要 200px 以上',
            'name.unique' => '用户名 已被占用，请重新填写',
            'name.regex' => '用户名 必须支持英文、数字、横杠和下划线。',
            'name.between' => '用户名 必须介于 3 - 25 个字符之间',
            'name.required' => '用户名 不能为空',
        ];
    }
}
