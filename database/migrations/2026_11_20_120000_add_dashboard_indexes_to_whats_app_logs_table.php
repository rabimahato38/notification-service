<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for the dashboard and message log filters (time range, result, template).
     */
    public function up(): void
    {
        Schema::table('whats_app_logs', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index('template');
        });
    }

    public function down(): void
    {
        Schema::table('whats_app_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['template']);
        });
    }
};
