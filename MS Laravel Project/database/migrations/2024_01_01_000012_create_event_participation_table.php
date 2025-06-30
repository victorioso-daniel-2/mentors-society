<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participation', function (Blueprint $table) {
            $table->id('participation_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('student_id');
            $table->boolean('attended');
            $table->text('feedback')->nullable();
            $table->timestamp('feedback_date')->nullable();
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('student')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participation');
    }
}; 