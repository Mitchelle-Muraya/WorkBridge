<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'worker_id',
        'job_id',
        'applied_at',
    ];
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    // ✅ An application belongs to a worker
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // ✅ An application belongs to a job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
