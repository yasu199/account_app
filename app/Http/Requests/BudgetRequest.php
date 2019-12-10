<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
{
// 許可するパスを指定
    public function authorize()
    {
        return true;
        // if ($this->path() == 'budget.budget') {
        //     return true;
        // } else {
        //     return false;
        // }
    }
// バリデーションルールを記載
    public function rules()
    {
        return [
            'selected_date' => 'required|integer|between: 200001, 299912',
            'budget.*' => 'required|integer|between: 0, 100000000',
            'flag' => 'required|in:1,2'
        ];

    }

    public function messages() {
        return [
            'selected_date.required' => '不具合が発生しました。再度お試しください。',
            'selected_date.integer' => '不具合が発生しました。再度お試しください。',
            'selected_date.between' => '不具合が発生しました。再度お試しください。',
            'budget.*.required' => '予算入力欄は空欄にできません。0円～の金額を入力ください。',
            'budget.*.integer' => '予算入力は、半角数字のみで入力してください',
            'budget.*.between' => '予算入力は0円～100,000,000円で入力してください。',
            'flag.required' => '不具合が発生しました。再度お試しください。',
            'flag.in' => '不具合が発生しました。再度お試しください。'
        ];
    }
}
