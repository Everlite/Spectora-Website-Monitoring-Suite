<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitored_urls') && ! Schema::hasColumn('monitored_urls', 'notify_sent')) {
            Schema::table('monitored_urls', function (Blueprint $table) {
                $table->boolean('notify_sent')->default(false)->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('monitored_urls') && Schema::hasColumn('monitored_urls', 'notify_sent')) {
            Schema::table('monitored_urls', function (Blueprint $table) {
                $table->dropColumn('notify_sent');
            });
        }
    }
};
