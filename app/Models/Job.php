<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
  public function client(){ return $this->belongsTo(User::class, 'client_id'); }
public function hiredWorker(){ return $this->belongsTo(User::class, 'hired_worker_id'); }
public function applications(){ return $this->hasMany(Application::class); }
public function ratings(){ return $this->hasMany(Rating::class); }
  //
}
