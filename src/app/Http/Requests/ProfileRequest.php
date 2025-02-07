<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png', 'max:2048'],
            'post_number' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は50文字以内で入力してください',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
            'image.max' => '画像サイズは2MB以内にしてください',
            'post_number.required' => '郵便番号を入力してください',
            'post_number.regex' => '郵便番号は000-0000の形式の半角数字で入力してください',
            'address.required' => '住所を入力してください',
            'address.max' => '住所は255文字以内で入力してください',
            'building.max' => '建物名は255文字以内で入力してください',
        ];
    }
}
