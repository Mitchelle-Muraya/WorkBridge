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
