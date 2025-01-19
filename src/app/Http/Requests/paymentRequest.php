<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'purchase_id' => ['required', 'string'],
      'payment_intent_id' => ['required', 'string'],
      'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
      'currency' => ['required', 'string', 'in:jpy'],
    ];
  }

  public function messages()
  {
    return [
      'amount.required' => '金額を入力してください',
      'amount.integer' => '金額は半角数字で入力してください',
      'amount.min' => '金額は1円以上で入力してください',
      'amount.max' => '金額は100万円以下で入力してください',
      'currency.in' => '通貨が不正です',

    ];
  }
}
