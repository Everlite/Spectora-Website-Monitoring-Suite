<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class ChecksHistory extends Model
{
    use HasFactory, Prunable;

    protected $table = 'checks_history';

    // WICHTIG: Wir haben timestamps (created_at, updated_at) jetzt in der DB,
    // also aktivieren wir sie hier, damit Laravel sie automatisch füllt.
    public $timestamps = true;

    protected $fillable = [
        'domain_id',
        'monitored_url_id',
        'status_code',
        'response_time',
        'safety_status',
        'ssl_days_left',
        'pagespeed_score',
        'pagespeed_score_desktop',
        'created_at',
    ];

    /**
     * Uptime checks always record response_time; audit-only rows do not.
     */
    public function scopeUptimeChecks($query)
    {
        return $query->whereNotNull('response_time');
    }

    public function scopeFailedUptime($query)
    {
        return $query->uptimeChecks()->where(function ($q) {
            $q->where('status_code', '>=', 400)
                ->orWhere('status_code', 0);
        });
    }

    // Hier sagen wir Laravel: "Behandle diese Spalten als echtes Datum"
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'response_time' => 'float', // Optional: Damit Zahlen auch Zahlen sind
    ];

    public function monitoredUrl()
    {
        return $this->belongsTo(MonitoredUrl::class, 'monitored_url_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }
}
