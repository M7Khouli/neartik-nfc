<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFcmToken extends Model
{
    use HasFactory;
    protected $fillable = ['token','admin_id'];


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
