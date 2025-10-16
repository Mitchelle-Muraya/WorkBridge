<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'client_id',
        'title',
        'description',
    ];

    // ✅ A job belongs to a client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // ✅ A job can have many applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
    public function reviews()
{
    return $this->hasMany(\App\Models\Review::class);
}

}
