<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Search_PaymentRequest extends FormRequest
{
    // form入力により、yearとmonthを別々に入力させている。
    // 一つの日付としてバリデーションしたいので、データをオーバライドするメソッドを書いていく
    protected function validationData() {
        $this->add_first_date();
        $this->add_last_date();
        return parent::validationData();
    }

    private function add_first_date() {
        $first_year = (string) $this->request->get('first_year');
        if ($first_year === '') {
            return;
        }
        $first_month = (string) $this->request->get('first_month');
        if ($first_month === '') {
            return;
        }
        $first_date = $first_year . $first_month;
        $this->request->add(['first_date' => $first_date]);
    }

    private function add_last_date() {
      $last_year = (string) $this->request->get('last_year');
      if ($last_year === '') {
          return;
      }
      $last_month = (string) $this->request->get('last_month');
      if ($last_month === '') {
          return;
      }
      $last_date = $last_year . $last_month;
      $this->request->add(['last_date' => $last_date]);
    }


    // 許可するパスを設定
    public function authorize()
    {
        return true;
    }


    // バリデーションルールを記載
    public function rules()
    {
        return [
            'first_year' => 'bail|required|date_format:"Y"',
            'first_month' => 'bail|required|date_format:"m"',
            'last_year' => 'bail|required|date_format:"Y"',
            'last_month' => 'bail|required|date_format:"m"',
            'first_date' => 'bail|required|date_format:"Ym"|before:last_date',
            'last_date' => 'bail|required|date_format:"Ym"|after:first_date',
            'search_id' => 'array|required',
            'search_id.*' => 'integer'
        ];
    }

    public function messages() {
        return [
            'first_year.required' => '検索する年を入力してください。',
            'first_year.date_format' => '不具合が発生しました。再度お試しください。',
            'first_month.required' => '検索する月を入力してください。',
            'first_month.date_format' => '不具合が発生しました。再度お試しください。',
            'last_year.required' => '検索する年を入力してください。',
            'last_year.date_format' => '不具合が発生しました。再度お試しください。',
            'last_month.required' => '検索する年を入力してください。',
            'last_month.date_format' => '不具合が発生しました。再度お試しください。',
            'first_date.require' => '不具合が発生しました。再度お試しください。',
            'first_date.date_format' => '不具合が発生しました。再度お試しください。',
            'first_date.before' => '日付の入力は検索開始日が検索終了日より前の日付になるようにしてください。',
            'last_date.after' => '日付の入力は検索終了日が検索開始日より後の日付になるようにしてください。',
            'last_date.require' => '不具合が発生しました。再度お試しください。',
            'last_date.date_format' => '不具合が発生しました。再度お試しください。',
            'search_id.array' => '不具合が発生しました。再度お試しください。',
            'search_id.required' => '最低一つでも検索する費用項目をお選びください。',
            'search_id.integer' => '不具合が発生しました。再度お試しください。'
        ];
    }
}
