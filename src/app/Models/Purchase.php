<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'profile_id',
        'payment_method',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

