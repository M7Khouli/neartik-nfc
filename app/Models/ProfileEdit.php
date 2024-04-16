<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;





class ProfileEdit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'old_value',
        'new_value',
        'status',
        'field',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
