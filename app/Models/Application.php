<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function job(){ return $this->belongsTo(Job::class); }
public function worker(){ return $this->belongsTo(User::class, 'worker_id'); }
//
}
