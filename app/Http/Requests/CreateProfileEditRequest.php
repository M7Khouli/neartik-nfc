<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProfileEditRequest extends FormRequest
{
    public function rules():array
    {

        return [
            'Facebook'=>'sometimes|required|string',
            'WhatsApp'=>'sometimes|required|string',
            'Instagram'=>'sometimes|required|string',
            'SnapChat'=>'sometimes|required|string',
            'Discord'=>'sometimes|required|string',
            'Telegram'=>'sometimes|required|string',
            'Messenger'=>'sometimes|required|string',
            'Youtube'=>'sometimes|required|string',
            'TikTok'=>'sometimes|required|string',
            'BeReal'=>'sometimes|required|string',
            'LinkedIn'=>'sometimes|required|string',
            'Twitter/X'=>'sometimes|required|string',
            'WeChat'=>'sometimes|required|string',
            'Pinterest'=>'sometimes|required|string',
            'Reddit'=>'sometimes|required|string',
            'Twitch'=>'sometimes|required|string',
            'Threads'=>'sometimes|required|string',
            'name'=>'sometimes|required|string',
            'company_name'=>'sometimes|required|string',
            'company_url'=>'sometimes|required|string',
            'company_position'=>'sometimes|required|string',
            'phone_number1'=>'sometimes|required|string',
            'phone_number2'=>'sometimes|required|string',
            'email1'=>'sometimes|required|string',
            'email2'=>'sometimes|required|string',
            'Bio'=>'sometimes|required|string',
            'location'=>'sometimes|required|string',
            'Youtube_video'=>'sometimes|required|string',
            'PayPal'=>'sometimes|required|string',
            'Venmo'=>'sometimes|required|string',
            'CashApp'=>'sometimes|required|string',
            'banner'=>'sometimes|required|mimes:jpg',
            'photo'=>'sometimes|required|mimes:jpg,png',
        ];
    }


    public function validated($key=null,$value=null)
    {

        $req = $this->validator->validated();
        if($this->photo || $this->banner){
            if($this->photo)
            $photoPath = $this->file('photo')->store('public/img');
            if($this->banner)
            $photoPath = $this->file('banner')->store('public/img');

            $photoPath = url('storage'.substr($photoPath,6));
            if($this->photo)
            $req['photo']=$photoPath;
            if($this->banner)
            $req['banner']=$photoPath;
        }

        return $req;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message'=>'please enter a valid info.'],422));

    }

}
