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
        Schema::create('whats_app_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->index();
            $table->string('to');
            $table->string('template');
            $table->string('language')->default('en');
            $table->json('parameters')->nullable();
            $table->json('response')->nullable();
            $table->integer('status_code');
            $table->string('status')->default('success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_logs');
    }
};
