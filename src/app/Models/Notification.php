<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'chat_id',
        'notification_status',
    ];

    const NOTIFICATION_UNREAD = 'unread';
    const NOTIFICATION_READ = 'read';

    protected $attributes = [
        'notification_status' => self::NOTIFICATION_UNREAD,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class)->nullable();
    }
}
