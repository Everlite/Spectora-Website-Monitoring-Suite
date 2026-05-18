<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsVisit extends Model
{
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
        'country',
    ];
}
