<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_condition', function (Blueprint $table) {
            $table->id('condition_id');
            $table->unsignedBigInteger('item_id');
            $table->text('condition_description')->nullable();
            $table->timestamp('recorded_date')->useCurrent();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->foreign('item_id')->references('item_id')->on('inventory_item')->onDelete('cascade');
            $table->foreign('recorded_by')->references('user_id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_condition');
    }
}; 