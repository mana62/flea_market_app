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
        if (app()->runningInConsole()) {
            return;
        }

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
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchasedItems()
    {
        return $this->hasManyThrough(Item::class, Purchase::class, 'user_id', 'id', 'id', 'item_id');
    }

    public function listedItems()
    {
        return $this->hasMany(Item::class, 'user_id', 'id');
    }

    public function progressPurchasedItems()
    {
        return $this->hasManyThrough(Item::class, ChatRoom::class, 'buyer_id', 'id', 'id', 'item_id')
            ->whereIn('chat_rooms.transaction_status', ['active', 'buyer_rated']);
    }

    public function progressListedItems()
    {
        return $this->hasManyThrough(Item::class, ChatRoom::class, 'seller_id', 'id', 'id', 'item_id')
            ->whereIn('chat_rooms.transaction_status', ['active', 'buyer_rated']);
    }


    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
