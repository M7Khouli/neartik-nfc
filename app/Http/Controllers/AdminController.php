<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRegRequest;
use App\Http\Requests\UploadExcelRequest;
use App\Http\Resources\GetUserResource;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Field;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{
    //

    public function getUsers(Request $req){

        $users = User::query()
        ->when($req->card_id,function($query){
            return $query->where('card_id','like',request('card_id').'%');
        })
        ->latest()
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
        ->where('id',$req->id)->first();


        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']]);
        }

        $excel = $user->makeVisible(['excel']);

        $excel = $excel->excel;

        $user = $user->fields;

        return response()->json(['data'=>GetUserResource::collection($user),'excel'=>$excel]);

    }

    public function deleteUser(Request $req){


        $user = User::where('id',$req->id)->first();
        if(!$user){
            return response()->json(['errors'=>['please enter a vaild user id']],400);
        }


        $admin = Admin::where('id',$req->user()->id)->first();

        activity()
        ->performedOn($user)
        ->causedBy($admin)
        ->withProperties(['admin_email'=>$admin->email,'card_id'=>$user->card_id])
        ->log('Admin deleted user');

        $user = User::destroy($req->id);
        return response()->json(['message'=>'user successfully deleted'],200);

    }

    public function deactiveUser(Request $req){

        $user = User::query()
        ->where('id',$req->id)->first();
        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']]);
        }


        $user = User::where('id',$req->id)->first();
        $admin = Admin::where('id',$req->user()->id)->first();

        activity()
        ->performedOn($user)
        ->causedBy($admin)
        ->withProperties(['admin_email'=>$admin->email,'card_id'=>$user->card_id])
        ->log('Admin deactivated user');

        $user->tokens()->delete();

        $user->update(['activated'=>0]);

        return response()->json(['message'=>'user deactivated successfully.'],200);
    }

    public function activeUser(Request $req){
        $user = User::query()
        ->where('id',$req->id)->first();
        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']]);
        }

        $user->update(['activated'=>1]);

        $user = User::where('id',$req->id)->first();
        $admin = Admin::where('id',$req->user()->id)->first();

        activity()
        ->performedOn($user)
        ->causedBy($admin)
        ->withProperties(['admin_email'=>$admin->email,'card_id'=>$user->card_id])
        ->log('Admin deactivated user');
        return response()->json(['message'=>'user activated successfully.'],200);


    }
    public function addUser(Request $req){

        $validated = Validator::make($req->all(),['card_id'=>'required|string|min:1|max:7|regex:/^\d+$/|unique:users,card_id']);

        if($validated->fails()){
            return response()->json(['errors'=>$validated->errors()->all()],422);
        }

        $user = User::query()->create(['card_id'=>$req->card_id]);

        return response()->json(['message'=>'user successfully created','id'=>$user->id]);
    }

    public function getPendingUsers(){

        $pending =  User::whereHas('profileEdits',function ($query){
            $query->where('status','pending');
        })->latest()->paginate(10);

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
        ->where('id',$req->id)->first();

        $pending = $pending->profileEdits()
        ->when($req->status, function ($query) use ($req) {
                $query->where('status', $req->status);
            })->latest()->get();


        return response()->json(['data'=>$pending],200);
    }

    public function getNotifications(){

        $notifications = AdminNotification::query()->latest()->get();

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

        $doesnotHaveExcel = User::query()->where('excel',null)->get()->count();


        $totalVisitors = User::all()->sum('visitors');

        $mostVisited = User::orderByRaw('CONVERT(VISITORS,SIGNED) desc')->get(['id','card_id','visitors']);

        $signedInLast24Hours = UserLogin::query()
        ->where('created_at','>=',Carbon::now()->subHours(24))
        ->distinct('user_id')
        ->count();

        $signedInLastWeek = UserLogin::query()
        ->where('created_at','>=',Carbon::now()->subWeek())
        ->distinct('user_id')
        ->count();

        $signedInLastMonth = UserLogin::query()
        ->where('created_at','>=',Carbon::now()->subMonth())
        ->distinct('user_id')
        ->count();

        return response()->json(['data'=>[
            'user_count'=>$users,
            'fields_count'=>$fields,
            'pending_count'=>$pending,
            'activateCount'=>[
                'acitvated_count'=>$users-$unacitvated,
                'unactivated_count'=>$unacitvated],
            'fields'=>$fieldsCount,
            'verified_accounts'=>$users-$doesnotHaveExcel,
            'unverified_accounts'=>$doesnotHaveExcel,
            'total_visitors'=>$totalVisitors,
            'most_visited'=>$mostVisited,
            'signed_in_last_24_hours'=>$signedInLast24Hours,
            'signed_in_last_week'=>$signedInLastWeek,
            'signed_in_last_month'=>$signedInLastMonth
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
        ->where('id',$req->id)->first();

        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']]);
        }

        $user->update(['password'=>Hash::make($user->id)]);

        return response()->json([
            'message'=>'password reseted successfully'
        ]);

    }

    public function getActivities(){

        $activities = Activity::query()->latest()->paginate();

        return response()->json(['data'=>$activities->items(),
            'count'=>$activities->count()
            ,'currentPage'=>$activities->currentPage()
            ,'perPage'=>$activities->perPage()
            ,'total'=>$activities->total()
            ,'lastPage'=>$activities->lastPage()]);

    }



    public function addExcel(Request $req){

        $user = User::where('id',$req->id)->first();

        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']],401);
        }

        User::query()
        ->where('id',$req->id)
        ->update(['excel'=>$req->excel]);

        return response()->json(['messaage'=>'excel link added successfully']);
    }


    /* public function uploadExcel(UploadExcelRequest $req){

        $user = User::where('id',$req->id)->first();

        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']],401);
        }

        $path = $req->file('excel')->store('private');


        if($user->excel){
            Storage::delete($user->excel);
        }

        User::query()
        ->where('id',$req['id'])
        ->update(['excel'=>$path]);

        return response()->json(['messaage'=>'file uploaded successfully']);
    }

    */
    /* public function getExcel(Request $req){

        $user = User::where('id',$req->id)->first();

        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']],401);
        }

        $user = $user->makeVisible(['excel']);

        if(!$user->excel){
            return response()->json(['errors'=>['user does not have an excel file']],401);
        }


        return Storage::download($user->excel);
    }

     public function deleteExcel(Request $req){

        $user = User::where('id',$req->id)->first();

        if(!$user){
            return response()->json(['errors'=>['please enter a valid user id']],401);
        }

        $user = $user->makeVisible(['excel']);

        if(!$user->excel){
            return response()->json(['errors'=>['user does not have an excel file']],401);
        }

        Storage::delete($user->excel);

        return response()->json(['messaage'=>'file deleted successfully']);

    }

    */
    public function getAdmins(){

        $admins = Admin::query()->latest()->get();

        return response()->json(['data'=>$admins]);

    }

    public function getVisitors(){


        $countries = DB::table('users')
        ->where('country','!=',null)
        ->select(['country',DB::raw('count(*) as total'),DB::raw('country_code')])
        ->groupBy(['country','country_code'])
        ->get();

        return response()->json(['data'=>$countries]);


    }

}
