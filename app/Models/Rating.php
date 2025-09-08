<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
   public function job(){ return $this->belongsTo(Job::class); }
public function client(){ return $this->belongsTo(User::class, 'client_id'); }
public function worker(){ return $this->belongsTo(User::class, 'worker_id'); }
 //
}
