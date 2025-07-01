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
        Schema::create('class', function (Blueprint $table) {
            $table->id('class_id');
            $table->string('class_name', 100);
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('class_president_id', 20)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_year');
            $table->foreign('class_president_id')->references('student_number')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};
