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
        Schema::create('class_schedule', function (Blueprint $table) {
            $table->id('class_schedule_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('day_of_week', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 100)->nullable();
            $table->foreign('class_id')->references('class_id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('class_subject_id')->on('class_subject')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_year')->onDelete('set null');
            $table->unique(['class_id', 'subject_id', 'day_of_week', 'start_time', 'academic_year_id'], 'class_schedule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedule');
    }
};
