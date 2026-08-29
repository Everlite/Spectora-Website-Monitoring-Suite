<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory, HasUuids;

    public const GEO_OFF = 'off';

    public const GEO_COUNTRY = 'country';

    public const GEO_CITY = 'city';

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected $fillable = [
        'user_id',
        'uuid',
        'url',
        'keyword_must_contain',
        'keyword_must_not_contain',
        'status_code',
        'ssl_days_left',
        'response_time',
        'safety_status',
        'safety_details',
        'visitors_today',
        'last_checked',
        'notify_sent',
        'only_check_public_pages',
        'respect_robots_txt',
        'respect_noindex',
        'exclude_patterns',
        'sitemap_urls',
        'included_sitemaps',
        'analytics_geo_precision',
        'webhook_url',
    ];

    protected $casts = [
        'last_checked' => 'datetime',
        'notify_sent' => 'boolean',
        'last_pagespeed_details' => 'array',
        'safety_details' => 'array',
        'only_check_public_pages' => 'boolean',
        'respect_robots_txt' => 'boolean',
        'respect_noindex' => 'boolean',
        'sitemap_urls' => 'array',
        'included_sitemaps' => 'array',
    ];

    public function getGradeAttribute(): string
    {
        $score = $this->pagespeed_score_desktop ?? 0;
        return match (true) {
            $score >= 95 => 'A+',
            $score >= 85 => 'A',
            $score >= 75 => 'B',
            $score >= 60 => 'C',
            $score >= 40 => 'D',
            default => 'F',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function history()
    {
        return $this->hasMany(ChecksHistory::class);
    }

    public function uptimeHistory()
    {
        return $this->history()->uptimeChecks();
    }

    public function notes()
    {
        return $this->hasMany(DomainNote::class);
    }

    public function analyticsVisits()
    {
        return $this->hasMany(AnalyticsVisit::class);
    }

    public function monitoredUrls()
    {
        return $this->hasMany(MonitoredUrl::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($domain) {
            if (empty($domain->uuid)) {
                $domain->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getRouteKey()
    {
        return $this->uuid ?: (string)$this->id;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('uuid', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    /**
     * Calculates the uptime percentage for a given number of days.
     */
    public function calculateUptime(int $days = 30): float
    {
        $startDate = now()->subDays($days);
        $query = $this->uptimeHistory()->where('created_at', '>=', $startDate);
        $totalChecks = (clone $query)->count();

        if ($totalChecks === 0) {
            return 0.0;
        }

        $failedChecks = (clone $query)->failedUptime()->count();

        return round((($totalChecks - $failedChecks) / $totalChecks) * 100, 2);
    }
}
