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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['1'])->default('1')->comment('1 - Admin');
            $table->string('first_name')->length(100)->nullable();
            $table->string('last_name')->length(100)->nullable();
            $table->string('email')->length(250)->nullable();
            $table->integer('phone_code')->length(3)->nullable();
            $table->string('phone_number')->length(25)->nullable();
            $table->string('username')->length(100)->nullable();
            $table->string('password')->length(100)->nullable();
            $table->enum('status', ['1','2'])->default('1')->comment('1 - Active, 2 - In-Active');
            $table->text('polling_url')->nullable();
            $table->timestamp('password_changed_at')->useCurrent();
            $table->rememberToken();
            $table->string('time_zone')->length(250)->nullable();
            $table->string('last_login_ip')->length(250)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
