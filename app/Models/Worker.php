<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'user_id',
        'skills',
        'experience',
        'availability'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
