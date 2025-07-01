<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_log', function (Blueprint $table) {
            $table->id('log_id');
            $table->string('student_number', 20)->nullable();
            $table->string('action', 100);
            $table->string('entity_type', 50);
            $table->integer('entity_id');
            $table->text('before_state')->nullable();
            $table->text('after_state')->nullable();
            $table->timestamp('action_date')->useCurrent();
            $table->foreign('student_number')->references('student_number')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_log');
    }
}; 