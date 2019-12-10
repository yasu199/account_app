<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Fixed_PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment.*' => 'integer|between: 0, 100000000',
            'memo.*' => 'max:10|inputMemo',
        ];
    }
    public function messages() {
        return [
            'payment.*.integer' => '支払金額は、半角数字のみで入力してください。',
            'payment.*.between' => '支払金額は0円～100,000,000円で入力してください。',
            'memo.*.max' => 'メモの入力は10文字以内でお願いします。',
            'memo.*.input_memo' => '1円以上の金額入力があった個所のみ、メモが入力できます。'
        ];
    }
}
