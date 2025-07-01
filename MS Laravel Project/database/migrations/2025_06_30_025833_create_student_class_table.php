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
        Schema::create('student_class', function (Blueprint $table) {
            $table->string('student_number', 20);
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->string('year_level', 50)->default('Other');
            $table->primary(['student_number', 'class_id', 'academic_year_id']);
            $table->foreign('student_number')->references('student_number')->on('student');
            $table->foreign('class_id')->references('class_id')->on('class')->onDelete('restrict');
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_year')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class');
    }
};
