<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    //


    public function index(){

        $fields = Field::all();

        return response()->json(['data'=>$fields]);
    }
}
