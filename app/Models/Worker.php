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
   public function reviews()
{
    return $this->hasMany(Review::class, 'worker_id');
}

public function averageRating()
{
    return $this->reviews()->avg('rating');
}


}
