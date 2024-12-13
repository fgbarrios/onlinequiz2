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
        Schema::create('activity_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount_per_score',20,2)->default(0);
            $table->enum('is_visitor_change_answer', ['1','2'])->default('1')->comment('1 - Yes, 2 - No');
            $table->enum('is_text_message', ['1','2'])->default('1')->comment('1 - Allow, 2 - Don\'t Allow');
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
        Schema::dropIfExists('activity_settings');
    }
};
