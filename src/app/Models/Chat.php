<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $attributes = [
        'read_status' => 'unread',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function item()
    {
        return $this->chatRoom->belongsTo(Item::class);
    }

}
