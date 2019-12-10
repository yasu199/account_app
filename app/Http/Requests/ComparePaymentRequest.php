<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComparePaymentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
              'selected_year' => 'required|date_format: "Y"',
              'selected_month' => 'required|date_format: "m"'
        ];
    }
    public function message() {
        return [
          'selected_year.required' => '検索する年を入力してください。',
          'selected_year.date_format' => '不具合が発生しました。再度お試しください。',
          'selected_month.required' => '検索する月を入力してください。',
          'selected_month.date_format' => '不具合が発生しました。再度お試しください。',
        ];
    }
}
