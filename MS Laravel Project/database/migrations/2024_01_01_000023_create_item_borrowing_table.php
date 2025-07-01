<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_borrowing', function (Blueprint $table) {
            $table->id('borrowing_id');
            $table->unsignedBigInteger('item_id');
            $table->string('student_number', 20);
            $table->timestamp('borrow_date')->useCurrent();
            $table->timestamp('return_date')->nullable();
            $table->unsignedBigInteger('condition_id_borrow')->nullable();
            $table->unsignedBigInteger('condition_id_return')->nullable();
            $table->foreign('item_id')->references('item_id')->on('inventory_item')->onDelete('cascade');
            $table->foreign('student_number')->references('student_number')->on('student')->onDelete('cascade');
            $table->foreign('condition_id_borrow')->references('condition_id')->on('item_condition')->onDelete('set null');
            $table->foreign('condition_id_return')->references('condition_id')->on('item_condition')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_borrowing');
    }
}; 