<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Domains table enhancements
        if (Schema::hasTable('domains')) {
            Schema::table('domains', function (Blueprint $table) {
                if (! Schema::hasColumn('domains', 'webhook_url')) {
                    $table->string('webhook_url', 1024)->nullable()->after('analytics_geo_precision');
                }
            });
        }

        // 2. Users table enhancements
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'webhook_url')) {
                    $table->string('webhook_url', 1024)->nullable()->after('agency_logo_path');
                }
            });
        }

        // 3. Analytics visits table enhancements
        if (Schema::hasTable('analytics_visits')) {
            Schema::table('analytics_visits', function (Blueprint $table) {
                if (! Schema::hasColumn('analytics_visits', 'event_type')) {
                    $table->string('event_type', 32)->default('pageview')->after('device');
                }
                if (! Schema::hasColumn('analytics_visits', 'event_name')) {
                    $table->string('event_name', 64)->nullable()->after('event_type');
                }
            });
        }

        // 4. Composite indexes for high-speed queries
        try {
            Schema::table('checks_history', function (Blueprint $table) {
                $table->index(['domain_id', 'created_at'], 'checks_hist_dom_created_idx');
                $table->index(['domain_id', 'response_time', 'created_at'], 'checks_hist_dom_rt_idx');
            });
        } catch (\Throwable) {
            // Index might already exist
        }

        try {
            Schema::table('analytics_visits', function (Blueprint $table) {
                $table->index(['domain_id', 'created_at'], 'analytics_dom_created_idx');
            });
        } catch (\Throwable) {
            // Index might already exist
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('domains')) {
            Schema::table('domains', function (Blueprint $table) {
                if (Schema::hasColumn('domains', 'webhook_url')) {
                    $table->dropColumn('webhook_url');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'webhook_url')) {
                    $table->dropColumn('webhook_url');
                }
            });
        }

        if (Schema::hasTable('analytics_visits')) {
            Schema::table('analytics_visits', function (Blueprint $table) {
                if (Schema::hasColumn('analytics_visits', 'event_name')) {
                    $table->dropColumn('event_name');
                }
                if (Schema::hasColumn('analytics_visits', 'event_type')) {
                    $table->dropColumn('event_type');
                }
            });
        }
    }
};
