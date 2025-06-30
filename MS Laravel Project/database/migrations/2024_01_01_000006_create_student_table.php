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
            $table->id('student_id');
            $table->string('student_number', 20)->unique();
            $table->foreignId('user_id')->constrained('user', 'user_id')->onDelete('cascade');
            $table->string('course', 100)->nullable();
            $table->string('year_level', 10)->nullable();
            $table->string('section', 10)->nullable();
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