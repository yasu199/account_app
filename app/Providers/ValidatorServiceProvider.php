<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Validators\CustomValidator;

class ValidatorServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }
    public function boot()
    {
        \Validator::resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
            return new CustomValidator($translator, $data, $rules, $messages, $customAttributes);
        });
    }
}
