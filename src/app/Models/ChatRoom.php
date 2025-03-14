<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'seller_id',
        'buyer_id',
        'transaction_status',
    ];

    protected $attributes = [
        'transaction_status' => 'active',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_BUYER_RATED = 'buyer_rated';
    const STATUS_COMPLETED = 'completed';

    public function isActive()
{
    return $this->transaction_status === 'active';
}

public function isBuyerRated()
{
    return $this->transaction_status === self::STATUS_BUYER_RATED;
}

public function isCompleted()
{
    return $this->transaction_status === self::STATUS_COMPLETED;
}

    protected $casts = [
        'seller_id' => 'integer',
        'buyer_id' => 'integer',
    ];

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function isSeller($user)
{
    return intval($this->seller_id) === intval($user->id);
}

public function isBuyer($user)
{
    return intval($this->buyer_id) === intval($user->id);
}

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
