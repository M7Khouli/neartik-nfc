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
    public function store(CreateProfileEditRequest $req){

        $userId = $req->user()->id;
        if( count($req->validated()) == 0 )
        return response()->json(['message'=>'please enter a valid request'],422);

            $pendingEdits=User::find($userId)->profileEdits()->where('status', 'pending')->count();

            if($pendingEdits){
                return response()->json([
                'message'=>'you already sent a edit request, please wait until the admin accept or reject them.'
            ],400);
        }

            $req = $req->validated();
            $adminfcmtokens = AdminFcmToken::all();

            $target = array();

            foreach($adminfcmtokens as $admin){
                array_push($target,$admin->token);
        }
        foreach($req as $key => $value){

                ProfileEdit::query()
                ->create([
                'new_value'=>$value,
                'field'=>$key,
                'user_id'=>$userId,
                'status'=>'pending'
                ]);
            }

            $title = 'A new user request an edit';

            $body = $userId;

            $image = User::find($userId)->userFields()->where('field_id',27)->first();

            if($image)
            $image = $image->info;

            AdminNotification::query()
            ->create(['title'=>$title,'body'=>$body,'image'=>$image,'type'=>'new message']);

            FcmService::notify($title,$body,$target,$image);


            return response()->json(['message'=>'edit successfully submited, please wait until the admin accept them.']);
        }

    public function getNotifications(Request $req){

        $notifications = UserNotification::query()
        ->where('user_id',$req->user()->id)->get();

        return response()->json(['data'=>$notifications]);
    }

    public function getUserEdits(Request $req){

        $pending = User::query()
        ->find($req->user()->id)
        ->profileEdits()
        ->get();

        return response()->json(['data'=>$pending],200);
    }

}
