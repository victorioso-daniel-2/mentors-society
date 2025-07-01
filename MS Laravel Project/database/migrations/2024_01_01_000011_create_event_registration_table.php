<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registration', function (Blueprint $table) {
            $table->id('registration_id');
            $table->unsignedBigInteger('event_id');
            $table->string('student_number', 20);
            $table->timestamp('registration_date')->useCurrent();
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('cascade');
            $table->foreign('student_number')->references('student_number')->on('student')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registration');
    }
}; 