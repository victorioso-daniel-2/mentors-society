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
        Schema::create('user_role', function (Blueprint $table) {
            $table->id('user_role_id');
            $table->foreignId('user_id')->constrained('user', 'user_id')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('role', 'role_id')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_year', 'academic_year_id')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unique(['user_id', 'role_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
    }
}; 