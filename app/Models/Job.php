<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
    'title',
    'description',
    'category',
    'budget',
    'deadline',
    'status',
    'client_id',
    'skills_required',
    'location',
];



    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
