<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function readers()
    {
        return $this->belongsToMany(User::class, 'log_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
    public function isReadBy($userId)
    {
        return $this->readers()->where('user_id', $userId)->exists();
    }
}

class LogRead extends Model
{
    protected $guarded = ['id'];

    public function log()
    {
        return $this->belongsTo(Log::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
