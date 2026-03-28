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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'logo_path') && !Schema::hasColumn('users', 'agency_logo_path')) {
                    $table->renameColumn('logo_path', 'agency_logo_path');
                } elseif (!Schema::hasColumn('users', 'agency_logo_path')) {
                    $table->string('agency_logo_path')->nullable()->after('email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'agency_logo_path')) {
                    $table->renameColumn('agency_logo_path', 'logo_path');
                }
            });
        }
    }
};
