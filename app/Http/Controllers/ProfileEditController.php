<?php

namespace App\Http\Controllers;

use App\Http\Resources\GetUserResource;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Field;
use App\Models\ProfileEdit;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;

class ProfileEditController extends Controller
{
    //

    public function approve(Request $req){


        $pendingProfileEdits = ProfileEdit::query()
        ->where('user_id',$req->id)
        ->where('status','pending')
        ->get();


        if(!$pendingProfileEdits){
            return response()->json([
                'error'=>'there is no edit request for this user or the user id is invalid.'
            ],400);
        }

        foreach($pendingProfileEdits as $edit){

            $field = Field::query()
            ->where('name',$edit->field)->first();

            $hasField = UserField::where('user_id',$edit->user_id)
            ->where('field_id',$field->id)->first();

            if(!$hasField){
                 UserField::query()
                ->create([
                    'info'=>$edit->new_value,
                    'user_id'=>$edit->user_id,
                    'field_id'=>$field->id
                ]);
            }
            else {
                $field = UserField::query()
                ->where('user_id',$edit->user_id)
                ->where('field_id',$field->id)
                ->update(['info'=>$edit->new_value]);
            }

            $edit->update(['status' => 'approved']);
        }

        $title = 'Your order get approved !';
        $body = 'Thanks for using our service';

        FcmService::notify($title,$body,[$req->id]);

        $user = User::find($req->id)->first();
        $admin = Admin::find($req->user()->id)->first();

        activity()
        ->performedOn($user)
        ->causedBy($admin)
        ->log('Admin accepted user edits');

        UserNotification::query()
        ->create(['title'=>$title
            ,'body'=>$body
            ,'user_id'=>$req->id
        ]);
        AdminNotification::query()->where('body',$req->id)->delete();
        User::where('id',$req->id)->update(['approved'=>1]);
        return response()->json(['message'=>'edits successfully approved'],200);

    }

    public function decline(Request $req){

        if(!$req->reason){
            return response()->json(['message'=>'please enter a valid reason for declining'],422);
        }

        User::findOrFail($req->id)->first();

        $pendingProfileEdits = ProfileEdit::query()
        ->where('user_id',$req->id)
        ->where('status','pending')
        ->get();

        if(!$pendingProfileEdits){
            return response()->json([
                'error'=>'there is no edit request for this user or the user id is invalid.'
            ],400);
        }

        foreach($pendingProfileEdits as $edit){

            $edit->update(['status' => 'declined']);

        }

        $title = 'Sorry your order got declined.';
        $body = $req->reason;

        FcmService::notify($title,$body,[$req->id]);


        $user = User::find($req->id)->first();
        $admin = Admin::find($req->user()->id)->first();
        activity()
        ->performedOn($user)
        ->causedBy($admin)
        ->log('Admin declined user edits');


        UserNotification::query()
        ->create(['title'=>$title
            ,'body'=>$body
            ,'user_id'=>$req->id
        ]);

        AdminNotification::query()->where('body',$req->id)->delete();
        return response()->json(['message'=>'edits successfully declined'],200);
    }


}
