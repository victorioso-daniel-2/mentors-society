<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_record', function (Blueprint $table) {
            $table->id('financial_record_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('transaction_id');
            $table->text('description')->nullable();
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('set null');
            $table->foreign('transaction_id')->references('transaction_id')->on('transaction')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_record');
    }
}; 