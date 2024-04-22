<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserField;


class Field extends Model
{
    use HasFactory;
    protected $fillable =[
    'name',
    'type',
    ];

    public function userFields(): BelongsTo
    {
        return $this->belongsTo(UserField::class);
    }

}
