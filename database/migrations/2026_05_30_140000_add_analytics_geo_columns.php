<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('analytics_visits')) {
            Schema::table('analytics_visits', function (Blueprint $table) {
                if (! Schema::hasColumn('analytics_visits', 'region')) {
                    $table->string('region')->nullable()->after('country');
                }
                if (! Schema::hasColumn('analytics_visits', 'city')) {
                    $table->string('city')->nullable()->after('region');
                }
            });
        }

        if (Schema::hasTable('domains')) {
            Schema::table('domains', function (Blueprint $table) {
                if (! Schema::hasColumn('domains', 'analytics_geo_precision')) {
                    $table->string('analytics_geo_precision', 16)->default('city')->after('included_sitemaps');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('analytics_visits')) {
            Schema::table('analytics_visits', function (Blueprint $table) {
                if (Schema::hasColumn('analytics_visits', 'city')) {
                    $table->dropColumn('city');
                }
                if (Schema::hasColumn('analytics_visits', 'region')) {
                    $table->dropColumn('region');
                }
            });
        }

        if (Schema::hasTable('domains')) {
            Schema::table('domains', function (Blueprint $table) {
                if (Schema::hasColumn('domains', 'analytics_geo_precision')) {
                    $table->dropColumn('analytics_geo_precision');
                }
            });
        }
    }
};
