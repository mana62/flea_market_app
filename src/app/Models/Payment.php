<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'payment_intent_id',
        'amount',
        'status',
        'currency'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
