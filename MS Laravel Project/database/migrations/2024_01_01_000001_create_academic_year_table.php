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
        Schema::create('academic_year', function (Blueprint $table) {
            $table->id('academic_year_id');
            $table->date('start_date');
            $table->date('end_date')->nullable(); // End date is set when academic year is changed
            $table->string('description', 32); // Format: 'YYYY-YYYY' or longer descriptions
            $table->unique(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year');
    }
}; 