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
      'price' => ['required', 'numeric', 'min:0'],
      'description' => ['required', 'string', 'max:1000'],
      'category' => ['required', 'array'],
      'category.*' => ['string'],
      'condition' => ['required', 'string'],
      'img' => ['required', 'image', 'mimes:jpeg,png,', 'max:2048'],
    ];
  }

  public function messages()
  {
    return [
      'name.required' => '商品名を入力してください',
      'name.string' => '商品名は文字列で入力してください',
      'name.max' => '商品名は255文字以内で入力してください',
      'brand.string' => 'ブランド名は文字列で入力してください',
      'brand.max' => 'ブランド名は255文字以内で入力してください',
      'price.required' => '価格を入力してください',
      'price.numeric' => '価格は数値で入力してください',
      'price.min' => '価格は0円以上にしてください',
      'description.required' => '商品の説明を入力してください',
      'description.string' => '商品の説明は文字列で入力してください',
      'description.max' => '商品の説明は1000文字以内で入力してください',
      'category.required' => 'カテゴリーを選択してください',
      'category.string' => 'カテゴリーは文字列で入力してください',
      'condition.required' => '商品の状態を選択してください',
      'condition.string' => '商品の状態は文字列で入力してください',
      'img.required' => '画像をアップロードしてください',
      'img.image' => 'アップロードできるのは画像ファイルのみです',
      'img.mimes' => '画像はjpeg、png形式でアップロードしてください',
      'img.max' => '画像のサイズは2MB以内にしてください',
    ];
  }
}
