<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRegRequest;
use App\Http\Resources\GetUserResource;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Field;
use App\Models\User;
use App\Models\UserField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //

    public function getUsers(Request $req){

        $users = User::query()
        ->when($req->card_id,function($query){
            return $query->where('card_id','like',request('card_id'));
        })
        ->paginate(10);

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
    public function addUser(Request $req){

        $validated = Validator::make($req->all(),['card_id'=>'required|integer|min:1|max:9999999|unique:users,card_id']);

        if($validated->fails()){
            return response()->json(['message'=>'please enter a valid card id'],422);
        }

        $user = User::query()->create(['card_id'=>$req->card_id]);

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


    public function addAdmins(AdminRegRequest $request){


        $adminCred = $request->validated();

        Admin::query()
        ->create($adminCred);

        return response()->json([
            "message"=>"admin added successfully !",
            "email"=>$adminCred['email'],
            "password"=>$adminCred['password']
        ]);


    }

    public function restPassword(Request $req){

       $user =User::query()
        ->findOrFail($req->id);

        $user->update(['password'=>Hash::make($user->id)]);

        return response()->json([
            'message'=>'password reseted successfully'
        ]);

    }

}
