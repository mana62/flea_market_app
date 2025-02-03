<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
      'name' => ['required', 'string', 'max:255'],
      'brand' => ['nullable', 'string', 'max:255'],
      'price' => ['required', 'string', 'max:20'],
      'description' => ['required', 'string', 'max:1000'],
      'category' => ['required', 'array'],
      'category.*' => ['required', 'string'],
      'condition' => ['required', 'string'],
      'img_base64' => ['required', 'regex:/^data:image\/(jpeg|png);base64,/'], // ✅ 画像のバリデーション追加
  ];
}

  public function messages()
  {
    return [
      'name.required' => '商品名を入力してください',
      'name.string' => '商品名は文字で入力してください',
      'name.max' => '商品名は255文字以内で入力してください',
      'brand.string' => 'ブランド名は文字で入力してください',
      'brand.max' => 'ブランド名は255文字以内で入力してください',
      'price.required' => '価格を入力してください',
      'price.numeric' => '価格は数値で入力してください',
      'price.min' => '価格は0円以上にしてください',
      'description.required' => '商品の説明を入力してください',
      'description.string' => '商品の説明は文字列で入力してください',
      'description.max' => '商品の説明は1000文字以内で入力してください',
      'category.required' => 'カテゴリーを選択してください',
      'condition.required' => '商品の状態を選択してください',
      'img_base64.required' => '画像をアップロードしてください',
      'img_base64.regex' => '画像はjpeg、png形式でアップロードしてください',
    ];
  }
}
