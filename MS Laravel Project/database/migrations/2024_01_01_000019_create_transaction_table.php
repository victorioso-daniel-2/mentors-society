<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('receipt_photo', 255)->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->string('student_number', 20);
            $table->foreign('student_number')->references('student_number')->on('student');
            $table->string('verified_by', 20)->nullable();
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('set null');
            $table->foreign('type_id')->references('type_id')->on('transaction_type')->onDelete('restrict');
            $table->foreign('verified_by')->references('student_number')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
}; 