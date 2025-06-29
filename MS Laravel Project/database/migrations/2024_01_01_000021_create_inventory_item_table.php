<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item_name', 100)->unique();
            $table->integer('quantity_total');
            $table->integer('quantity_used')->default(0);
            $table->integer('quantity_added')->default(0);
            $table->integer('remaining_quantity')->storedAs('quantity_total - quantity_used + quantity_added');
            $table->date('last_updated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item');
    }
}; 