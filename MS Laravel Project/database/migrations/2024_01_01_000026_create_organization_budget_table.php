<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organization_budget', function (Blueprint $table) {
            $table->id('org_budget_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->decimal('amount', 12, 2);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_year')->onDelete('cascade');
            $table->unique('academic_year_id'); // One budget per academic year
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_budget');
    }
}; 