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
        Schema::create('class_professor', function (Blueprint $table) {
            $table->id('class_professor_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('professor_name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->foreign('class_id')->references('class_id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('class_subject_id')->on('class_subject')->onDelete('set null');
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_year')->onDelete('set null');
            $table->unique(['class_id', 'professor_name', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_professor');
    }
};
