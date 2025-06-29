<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id('task_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('task_name', 100);
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->date('deadline')->nullable();
            $table->date('date_posted')->nullable();
            $table->time('time_posted')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('category', 50)->nullable();
            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('set null');
            $table->foreign('officer_id')->references('user_id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task');
    }
}; 