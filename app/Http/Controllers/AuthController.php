<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\AdminLoginRequest;
use App\Models\AdminFcmToken;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function adminLogin(AdminLoginRequest $req){

        $admin = Admin::where('email',$req['email'])->first();

        if(!$admin || !Hash::check($req['password'],$admin->password)){

        return response()->json(['errors'=>['Incorrect username or password']],400);

        }


        $admin->tokens()->delete();

        $token = $admin->createToken('Bearer')->plainTextToken;


        AdminFcmToken::query()
        ->where(['admin_id'=>$admin->id])
        ->delete();
        AdminFcmToken::query()->create(['token'=>$req['fcmToken'],'admin_id'=>$admin->id]);

        return response()->json(['message'=>'login successfully !','token'=>$token],200);

    }

}
