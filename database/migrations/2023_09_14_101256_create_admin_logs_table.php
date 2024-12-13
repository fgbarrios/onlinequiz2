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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->length(11)->nullable();
            $table->string('browser_agent')->length(250)->nullable();
            $table->string('ip_address')->length(250)->nullable();
            $table->string('remember_token')->length(250)->nullable();
            $table->timestamp('logged_at')->length(250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
