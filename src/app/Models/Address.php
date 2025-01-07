<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'address',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
