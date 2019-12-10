<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Budget_monthRequest extends FormRequest
{
// 許可するパスを指定
    public function authorize()
    {
        return true;
    }
// バリデーションルールを記載
    public function rules()
    {
        return [
            'selected_date' => 'required',
            'selected_date' => 'integer|between:200000,400000'
        ];
    }
}
