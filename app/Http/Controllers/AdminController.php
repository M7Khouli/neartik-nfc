<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckUserRequest;
use App\Http\Resources\GetUserResource;
use App\Http\Resources\UserResource;
use App\Models\AdminNotification;
use App\Models\Field;
use App\Models\ProfileEdit;
use App\Models\User;
use App\Models\UserField;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function getUsers(){

       $users = User::query()->paginate(10);

        return response()->json([
            'data' => $users->items()
            ,'count'=>$users->count()
            ,'currentPage'=>$users->currentPage()
            ,'perPage'=>$users->perPage()
            ,'total'=>$users->total()
            ,'lastPage'=>$users->lastPage()],200);

    }

    public function getUser(Request $req){

        $user = User::query()
        ->findOrFail($req->id)->fields;

        return GetUserResource::collection($user);
    }

    public function deleteUser(Request $req){

        $user = User::destroy($req->id);

        if(!$user){
            return response()->json(['message'=>'please enter a vaild user id'],400);
        }

        return response()->json(['message'=>'user successfully deleted'],200);

    }

    public function deactiveUser(Request $req){

        $user = User::query()
        ->findOrFail($req->id);


        $user->tokens()->delete();

        $user->update(['activated'=>0]);

        return response()->json(['message'=>'user deactivated successfully.'],200);
    }

    public function activeUser(Request $req){
        $user = User::query()
        ->findOrFail($req->id);
        $user->update(['activated'=>1]);

        return response()->json(['message'=>'user activated successfully.'],200);


    }
    public function addUser(){


        $user = User::query()->create();

        return response()->json(['message'=>'user successfully created','id'=>$user->id]);
    }

    public function getPendingUsers(){

        $pending =  User::whereHas('profileEdits',function ($query){
            $query->where('status','pending');
        })->paginate(10);

        return response()->json([
            'data' => $pending->items()
            ,'count'=>$pending->count()
            ,'currentPage'=>$pending->currentPage()
            ,'perPage'=>$pending->perPage()
            ,'total'=>$pending->total()
            ,'lastPage'=>$pending->lastPage()],200);

    }

    public function getUserEdits(Request $req){

        $pending = User::query()
        ->findOrFail($req->id)
        ->profileEdits()
        ->when($req->status, function ($query) use ($req) {
                $query->where('status', $req->status);
            })->get();


        return response()->json(['data'=>$pending],200);
    }

    public function getNotifications(){

        $notifications = AdminNotification::all();

        return response()->json(['data'=>$notifications],200);
    }

    public function charts(){

        $users = User::all()->count();

        $fields= UserField::all()->count();

        $pending =  User::whereHas('profileEdits',function ($query){
            $query->where('status','pending');
        })->count();

        $unacitvated = User::where('activated',0)->count();

        $fieldsCount = UserField::where('field_id','<=',17)->groupBy('field_id')->select('field_id', UserField::raw('count(*) as total'))->get();

        $allFields = Field::all();


        foreach($fieldsCount as $value){
            foreach ($allFields as $valueT) {
                if($value->field_id === $valueT->id){
                    $value->field_id = $valueT->name;
                }
            }
        }

        return response()->json(['data'=>[
            'user_count'=>$users,
            'fields_count'=>$fields,
            'pending_count'=>$pending,
            'activateCount'=>[
                'acitvated_count'=>$users-$unacitvated,
                'unactivated_count'=>$unacitvated],
            'fields'=>$fieldsCount
        ]],200);
    }
}
