<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;

class UserAuthController extends Controller
{

    public function login(LoginRequest $req){

        $user = User::find($req->id);

        if(!$user || !$user->activated | !Hash::check($req->password,$user->password)){

            return response()->json(['message'=>'Either the id or password is incorrect'],422);

        }

        $user->tokens()->delete();



        $token = $user->createToken('API TOKEN')->plainTextToken;

        return response()->json(['message'=>'login successfully.','token'=>$token]);
    }

    public function changePassword(ChangePasswordRequest $request){

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'The old password is incorrect'], 400);
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
