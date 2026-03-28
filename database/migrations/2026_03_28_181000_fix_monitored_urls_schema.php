<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('monitored_urls')) {
            Schema::table('monitored_urls', function (Blueprint $table) {
                if (!Schema::hasColumn('monitored_urls', 'last_response_time')) {
                    $table->integer('last_response_time')->nullable()->after('last_safety_status');
                }
                if (!Schema::hasColumn('monitored_urls', 'last_checked')) {
                    $table->timestamp('last_checked')->nullable()->after('last_response_time');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('monitored_urls')) {
            Schema::table('monitored_urls', function (Blueprint $table) {
                $table->dropColumn(['last_response_time', 'last_checked']);
            });
        }
    }
};
