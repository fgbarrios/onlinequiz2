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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('text_message_status', ['1','2'])->default('2')->comment('1 - Enable, 2 - Disable');
            $table->longText('invited_email_address')->nullable();
            $table->text('invite_message')->nullable();
            $table->string('smtp_mailer')->length(50)->nullable();
            $table->string('smtp_host')->length(250)->nullable();
            $table->integer('smtp_port')->length(11)->nullable();
            $table->string('smtp_username')->length(250)->nullable();
            $table->string('smtp_password')->length(100)->nullable();
            $table->string('smtp_encryption')->length(15)->nullable();
            $table->string('twilio_secret_key')->length(250)->nullable();
            $table->string('twilio_token')->length(250)->nullable();
            $table->string('twilio_from_code')->length(10)->nullable();
            $table->string('twilio_from_number')->length(50)->nullable();
            $table->string('ip_address')->length(250)->nullable();
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
        Schema::dropIfExists('general_settings');
    }
};
