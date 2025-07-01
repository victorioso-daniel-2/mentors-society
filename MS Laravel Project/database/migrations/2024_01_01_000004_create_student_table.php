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
        Schema::create('student', function (Blueprint $table) {
            $table->string('student_number', 20)->primary();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('middle_initial', 5)->nullable();
            $table->string('course', 100)->nullable();
            $table->string('year_level', 50)->nullable();
            $table->string('section', 10)->nullable();
            $table->enum('academic_status', ['active', 'dropped', 'shifted', 'graduated'])->default('active'); // Academic/enrollment status
            $table->string('email', 100)->unique(); // Student email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
    }
}; 