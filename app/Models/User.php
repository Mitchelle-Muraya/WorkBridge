<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',       // 'client' or 'worker'
        'google_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationships
     */

    // A user can have one client profile (if they choose client role)
    public function clientProfile()
    {
        return $this->hasOne(Client::class);
    }

    // A user can have one worker profile (if they choose worker role)
    public function workerProfile()
    {
        return $this->hasOne(Worker::class);
    }
}
