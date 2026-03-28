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
        Schema::table('checks_history', function (Blueprint $table) {
            $table->foreignId('monitored_url_id')->nullable()->after('domain_id')->constrained('monitored_urls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checks_history', function (Blueprint $table) {
            $table->dropColumn('monitored_url_id');
        });
    }
};
