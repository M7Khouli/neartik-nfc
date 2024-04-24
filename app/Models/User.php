<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserField;
use App\Models\ProfileEdit;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable,HasUuids,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'card_id',
        'password',
        'activated',
        'approved'
    ];

    public function userFields(): HasMany
    {
        return $this->hasMany(UserField::class);
    }
    public function profileEdits(): HasMany
    {
        return $this->hasMany(ProfileEdit::class);
    }

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'user_fields')->withPivot('info')->as('field');
    }

    public function fcmtoken()
    {
        return $this->hasOne(UserFcmToken::class);
    }

    public function notification(): HasMany {

        return $this->hasMany(UserNotification::class);
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
