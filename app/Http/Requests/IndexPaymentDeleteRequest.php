<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPaymentDeleteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_id'     => 'required|integer',
            'selected_year'  => 'required|date_format:"Y"',
            'selected_month' => 'required|date_format:"m"',
            'sort_flag'      => 'required|in:1,2',
        ];
    }
    public function messages() {
        return [
            'payment_id.required'        => '不具合が発生しました。再度お試しください。',
            'payment_id.integer'         => '不具合が発生しました。再度お試しください。', 
            'selected_year.required'     => '不具合が発生しました。再度お試しください。',
            'selected_year.date_format'  => '不具合が発生しました。再度お試しください。',
            'selected_month.required'    => '不具合が発生しました。再度お試しください。',
            'selected_month.date_format' => '不具合が発生しました。再度お試しください。',
            'sort_flag.required'         => '不具合が発生しました。再度お試しください。',
            'sort_flag.in'               => '不具合が発生しました。再度お試しください。',
        ];
    }
}
