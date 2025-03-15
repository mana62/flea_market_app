<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'description',
        'category',
        'condition',
        'image',
        'is_sold',
    ];

    protected $casts = [
        'category' => 'array',
        'is_sold' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id')->withTimestamps();
    }

    public function likesCount()
    {
        return $this->likedBy()->count();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
