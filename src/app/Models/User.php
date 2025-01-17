<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'has_profile',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $user->profile()->create();
        });
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchasedItems()
    {
        return $this->hasManyThrough(Item::class, Purchase::class, 'profile_id', 'id', 'id', 'item_id');
    }

    public function listedItems()
    {
        return $this->hasMany(Item::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
