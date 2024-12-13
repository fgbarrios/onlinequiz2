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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->enum('title_type', ['1','2'])->default('1')->comment('1 - Text, 2 - Image');
            $table->text('title')->nullable();
            $table->text('title_image')->nullable();
            $table->string('response_type')->length(50)->nullable();
            $table->enum('may_respond', ['1','2'])->default('1')->comment('1 - Upto, 2 - Unlimited');
            $table->enum('may_select', ['1','2'])->default('1')->comment('1 - Upto, 2 - Unlimited');
            $table->integer('may_respond_count')->length(11)->default('1')->nullable();
            $table->integer('may_select_count')->length(11)->default('1')->nullable();
            $table->enum('is_had_score', ['1','2'])->default('2')->comment('1 - Yes, 2 - No');
            $table->enum('is_multiple_type', ['1','2'])->default('2')->comment('1 - Yes, 2 - No');
            $table->integer('sort_order')->length(11)->nullable();
            $table->enum('status', ['1','2'])->default('2')->comment('1 - Active, 2 - In-Active');
            $table->enum('option_text_type', ['1','2'])->default('1')->comment('1 - ABC, 2 - 123');
            $table->integer('total_responses')->length(11)->default('0')->nullable();
            $table->string('ip_address')->length(250)->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->integer('created_by')->length(11)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->string('last_change')->length(250)->nullable();
            $table->softDeletes(); 
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
