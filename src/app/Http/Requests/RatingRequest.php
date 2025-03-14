<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
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
            'chat_room_id' => ['required', 'exists:chat_rooms,id'],
            'rater_id' => ['required', 'exists:users,id'],
            'rated_id' => ['required', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '評価をしてください',
            'rating.min' => '1以上を指定してください',
        ];
    }
}
