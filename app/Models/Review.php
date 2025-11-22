<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'client_id',
        'worker_id',
        'rating',
        'comment'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
