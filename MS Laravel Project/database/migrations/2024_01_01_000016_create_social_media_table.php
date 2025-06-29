<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media', function (Blueprint $table) {
            $table->id('social_media_id');
            $table->string('platform', 50);
            $table->string('url', 255)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
}; 