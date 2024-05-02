<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'body',
        'type',
        'image',
        'user_id'
    ];

    public function admin(): BelongsTo {

        return $this->belongsTo(User::class);

    }

}
