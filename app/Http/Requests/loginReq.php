<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'id'=>'required',
            'password'=>'required',
        ];
    }

}
