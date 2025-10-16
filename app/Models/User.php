<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Relationships
    public function worker()
    {
        return $this->hasOne(Worker::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    // ✅ New relationship
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'user_id');
    }

    public function reviews()
{
    return $this->hasMany(\App\Models\Review::class, 'worker_id');
}

}

