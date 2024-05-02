<?php

namespace App\Http\Controllers;


use App\Models\ProfileEdit;
use App\Http\Requests\CreateProfileEditRequest;
use App\Models\AdminFcmToken;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getNotifications(Request $req){

        $notifications = UserNotification::query()
        ->where('user_id',$req->user()->id)->latest()->get();

        return response()->json(['data'=>$notifications]);
    }

    public function getUserEdits(Request $req){

        $pending = User::query()
        ->where('id',$req->user()->id)->first();

        $pending = $pending->profileEdits()
        ->get();

        return response()->json(['data'=>$pending],200);
    }

}
