<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_sponsor', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('sponsor_id');
            $table->primary(['event_id', 'sponsor_id']);
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('cascade');
            $table->foreign('sponsor_id')->references('sponsor_id')->on('sponsor')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sponsor');
    }
}; 