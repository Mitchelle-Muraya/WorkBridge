<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skills',
        'photo',
        'resume',
        'experience'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
