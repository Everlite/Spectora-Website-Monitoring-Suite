<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class AnalyticsVisit extends Model
{
    use Prunable;

    // Disable updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'domain_id',
        'visitor_hash',
        'url',
        'path',
        'referrer',
        'referrer_domain',
        'browser',
        'os',
        'device',
        'event_type',
        'event_name',
        'country',
        'region',
        'city',
    ];

    /**
     * Define the prunable model query.
     */
    public function prunable()
    {
        // Automatically prune tracking visits older than 180 days (6 months)
        return static::where('created_at', '<=', now()->subDays(180));
    }
}
