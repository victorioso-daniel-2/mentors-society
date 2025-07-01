<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_role_permission', function (Blueprint $table) {
            $table->id('user_role_permission_id');
            $table->foreignId('user_role_id')->constrained('user_role', 'user_role_id')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permission', 'permission_id')->onDelete('cascade');
            $table->boolean('is_granted')->default(true);
            $table->timestamp('date')->useCurrent();
            $table->unique(['user_role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role_permission');
    }
}; 