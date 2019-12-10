<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Fixed_MonthRequest extends FormRequest
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
              'year' => 'date_format:Y|required',
              'month' => 'date_format:m|required',
          ];
      }

      public function messages() {
          return [
              'year.date_format' => '不具合が発生しました。再度お試しください。',
              'year.required' => '西暦を選択してください。',
              'month.date_format' => '不具合が発生しました。再度お試しください。',
              'month.required' => '月を選択してください。',
          ];
      }
}
