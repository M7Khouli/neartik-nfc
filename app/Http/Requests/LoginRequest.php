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
            'password'=>'required|min:8|max:32',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message'=>'Please enter a id or password'],422));

    }
}
