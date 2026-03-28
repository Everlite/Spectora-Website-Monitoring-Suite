<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'url',
        'is_active',
        'last_status_code',
        'last_safety_status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function history()
    {
        return $this->hasMany(ChecksHistory::class, 'monitored_url_id');
    }
}
