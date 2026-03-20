<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'company',
        'photo',
        'recommended_workers', // ← ADD THIS
    ];

    protected $casts = [
        'recommended_workers' => 'array', // ← JSON AUTO CONVERT
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
