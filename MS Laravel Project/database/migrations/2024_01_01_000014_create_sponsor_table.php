<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor', function (Blueprint $table) {
            $table->id('sponsor_id');
            $table->string('name', 100);
            $table->string('instagram_url', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor');
    }
}; 