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
        Schema::create('activity_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('sub_activity_id')->nullable();
            $table->integer('sort_order')->length(11)->nullable();
            $table->enum('is_correct', ['1','2'])->default('2')->comment('1 - Yes, 2 - No');
            $table->integer('score')->length(11)->default('0')->nullable();
            $table->string('option')->length(250)->nullable();
            $table->text('option_image')->nullable();
            $table->string('option_name')->length(10)->nullable();
            $table->enum('answer_type', ['1','2'])->default('1')->comment('1 - Text, 2 - Image');
            $table->bigInteger('select_count')->default('0')->length(11)->nullable();
            $table->string('ip_address')->length(250)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->softDeletes(); 
            $table->index(['activity_id']);
            $table->index(['sub_activity_id']);
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('sub_activity_id')->references('id')->on('activity_multiples')->onDelete('cascade');
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_options');
    }
};
