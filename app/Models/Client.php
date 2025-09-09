<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'web'; // single login

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'role', // default = 'worker', admin may assign 'client'
    ];

    protected $hidden = ['password'];

    public function jobs() {
        return $this->hasMany(Job::class);
    }
}
