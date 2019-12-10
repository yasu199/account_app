<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Variable_PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // バリデーションルールを記載
    public function rules()
    {
        return [
            'year' => 'date_format:Y|required',
            'month' => 'date_format:m|required',
            'day' => 'date_format:j|required',
            'payment.*' => 'integer|between: 0, 100000000',
            'memo.*' => 'max:10|inputMemo',
        ];
    }

    public function messages() {
        return [
            'year.date_format' => '不具合が発生しました。再度お試しください。',
            'year.required' => '西暦を選択してください。',
            'month.date_format' => '不具合が発生しました。再度お試しください。',
            'month.required' => '月を選択してください。',
            'day.date_format' => '不具合が発生しました。再度お試しください。',
            'day.required' => '日にちを選択してください。',
            'payment.*.integer' => '支払金額は、半角数字のみで入力してください。',
            'payment.*.between' => '支払金額は0円～100,000,000円で入力してください。',
            'memo.*.max' => 'メモの入力は10文字以内でお願いします。',
            'memo.*.input_memo' => '1円以上の金額入力があった個所のみ、メモが入力できます。'
        ];
    }
}
