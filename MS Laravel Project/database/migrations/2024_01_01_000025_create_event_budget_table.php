<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_budget', function (Blueprint $table) {
            $table->id('event_budget_id');
            $table->unsignedBigInteger('event_id');
            $table->decimal('amount', 12, 2);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('event')->onDelete('cascade');
            $table->unique('event_id'); // Each event has at most one budget
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_budget');
    }
}; 