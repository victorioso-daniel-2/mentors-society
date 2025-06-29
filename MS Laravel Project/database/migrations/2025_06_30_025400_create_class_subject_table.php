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
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id('class_subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('subject_name', 100);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('class_id')->references('class_id')->on('class')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_year')->onDelete('set null');
            $table->unique(['class_id', 'subject_name', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};
