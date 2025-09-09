<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Worker extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'web'; // single login uses default guard

    protected $fillable = [
        'name',
        'email',
        'password',
        'resume_path',
        'skills', // store as JSON or comma-separated
        'role',   // default = 'worker', admin can change later
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'skills' => 'array',
    ];

    public function applications() {
        return $this->hasMany(Application::class);
    }
}
