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
}
