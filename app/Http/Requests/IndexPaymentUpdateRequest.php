<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPaymentUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_id'    => 'required|integer',
            'payment_year'  => 'required|date_format:"Y"',
            'payment_month' => 'required|date_format:"m"',
            'payment_day'   => 'required|date_format:"d"',
            'genre'         => 'required|integer',
            'payment'       => 'integer|between: 0, 100000000',
            'memo'          => 'max:10',
            'flag'          => 'required|integer|in:0,1',
            'sort_flag'     => 'required|in:1,2',
        ];
    }

    public function messages() {
        return [
            'payment_id.required'       => '不具合が発生しました。再度お試しください。',
            'payment_id.integer'        => '不具合が発生しました。再度お試しください。',
            'payment_year.required'     => '不具合が発生しました。再度お試しください。',
            'payment_year.date_format'  => '不具合が発生しました。再度お試しください。',
            'payment_month.required'    => '不具合が発生しました。再度お試しください。',
            'payment_month.date_format' => '不具合が発生しました。再度お試しください。',
            'payment_day.required'      => '不具合が発生しました。再度お試しください。',
            'payment_day.date_format'   => '不具合が発生しました。再度お試しください。',
            'genre.required'            => '不具合が発生しました。再度お試しください。',
            'genre.integer'             => '不具合が発生しました。再度お試しください。',
            'payment.integer'           => '支払金額は、半角数字のみで入力してください。',
            'payment.between'           => '支払金額は0円～100,000,000円で入力してください。',
            'memo.max'                  => 'メモの入力は10文字以内でお願いします。',
            'flag.required'             => '不具合が発生しました。再度お試しください。',
            'flag.integer'              => '不具合が発生しました。再度お試しください。',
            'flag.in'                   => '不具合が発生しました。再度お試しください。',
            'sort_flag.required'        => '不具合が発生しました。再度お試しください。',
            'sort_flag.in'              => '不具合が発生しました。再度お試しください。',
        ];
    }
}
