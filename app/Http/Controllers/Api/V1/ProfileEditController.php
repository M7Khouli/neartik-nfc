<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProfileEditRequest;
use App\Models\AdminFcmToken;
use App\Models\AdminNotification;
use App\Models\ProfileEdit;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;

class ProfileEditController extends Controller
{
    //

        public function store(CreateProfileEditRequest $req){

        $userId = $req->user()->id;
        if( count($req->validated()) == 0 )
        return response()->json(['errors'=>['please enter a valid request']],422);

        $pendingEdits=User::where('id',$userId)->first();
        $pendingEdits = $pendingEdits->profileEdits()->where('status', 'pending')->count();

            if($pendingEdits){
                return response()->json([
                'errors'=>['you already sent a edit request, please wait until the admin accept or reject them.']
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

        $body = User::where('id',$userId)->first();
        $body = $body->card_id;

        $image = User::where('id',$userId)->first();
        $image = $image->userFields()->where('field_id',27)->first();

            if($image)
            $image = $image->info;

            AdminNotification::query()
            ->create(['title'=>$title,'body'=>$body,'image'=>$image,'user_id'=>$userId,'type'=>'new message']);

            FcmService::notify($title,$body,$target,$image);


            return response()->json(['message'=>'edit successfully submited, please wait until the admin accept them.']);
        }


}
