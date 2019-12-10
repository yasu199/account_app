<?php

namespace App\Http\Validators;

use Illuminate\Validation\Validator;

class CustomValidator extends Validator {
    public function validateInputMemo($attribute, $value, $parameters) {
        // paymentの値を取得
        $payment = $this->getValue('payment');
        // memoのkey部分を取得する
        $memo_key = explode('.', $attribute);
        $memo_key = (int) $memo_key[1];
        // memoのkeyと同じkeyを持つpaymentについて値を比較する
        if (isset($value) && $value !== '') {
            if ($payment[$memo_key] === '0' || $payment[$memo_key] === '' || $payment[$memo_key] === null) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}



 ?>
