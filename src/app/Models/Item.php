<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'brand',
        'price',
        'description',
        'category',
        'condition',
        'like_count',
        'image',
        'is_sold',
    ];

    protected $casts = [
        'category' => 'array',
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
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function likesCount()
    {
        return $this->likedBy()->count();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
