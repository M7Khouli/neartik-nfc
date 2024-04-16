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
    ];

    public function admin(): BelongsTo {

        return $this->belongsTo(User::class);

    }

}
