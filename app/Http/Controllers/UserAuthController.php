<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Models\UserFcmToken;
use App\Models\UserField;
use App\Models\UserLogin;
use Stevebauman\Location\Facades\Location;

class UserAuthController extends Controller
{

    public function login(LoginRequest $req){

        $user = User::where('id',$req->id)->first();

        if(!$user || !$user->activated | !Hash::check($req->password,$user->password)){

            return response()->json(['errors'=>['Either the id or password is incorrect']],422);

        }

        $user->tokens()->delete();


        $photo = UserField::query()
        ->where('user_id',$req->id)
        ->where('field_id',27)
        ->first(['info']);

        if($photo)
        $photo=$photo->info;

        $token = $user->createToken('API TOKEN')->plainTextToken;

        UserFcmToken::query()
        ->where('user_id',$req->id)
        ->delete();

        UserFcmToken::query()
        ->create(['token'=>$req['fcmToken'],
                'user_id'=>$req->id]);

        $position = Location::get($req->ip());

        User::where('id',$req->id)->update(['country'=>$position->countryName,'country_code'=>$position->countryCode]);

        UserLogin::query()->create(['user_id'=>$req->id]);

        return response()->json(['message'=>'login successfully.'
            ,'token'=>$token,'user_id'=>$req->id
            ,'country'=>$user->country
            ,'country_code'=>$user->country_code
            ,'photo'=>$photo]);
    }

    public function changePassword(ChangePasswordRequest $request){

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['errors' => ['The old password is incorrect']], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'logout successfully']);
    }
}
