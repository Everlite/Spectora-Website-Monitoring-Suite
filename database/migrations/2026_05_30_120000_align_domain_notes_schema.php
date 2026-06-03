<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('domain_notes')) {
            return;
        }

        if (Schema::hasColumn('domain_notes', 'note_content') && ! Schema::hasColumn('domain_notes', 'content')) {
            Schema::table('domain_notes', function (Blueprint $table) {
                $table->renameColumn('note_content', 'content');
            });
        }

        if (! Schema::hasColumn('domain_notes', 'user_id')) {
            Schema::table('domain_notes', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('domain_id')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('domain_notes', 'updated_at')) {
            Schema::table('domain_notes', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Legacy repair migration — no rollback.
    }
};
