<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_type', function (Blueprint $table) {
            $table->id('type_id');
            $table->string('type_name', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_type');
    }
}; 