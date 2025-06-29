<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_evaluation', function (Blueprint $table) {
            $table->id('evaluation_id');
            $table->unsignedBigInteger('participation_id');
            $table->string('category', 50);
            $table->text('question_text');
            $table->string('response', 50)->nullable();
            $table->integer('numerical_rating')->nullable();
            $table->foreign('participation_id')->references('participation_id')->on('event_participation')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_evaluation');
    }
}; 