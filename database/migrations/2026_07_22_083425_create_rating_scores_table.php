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
        Schema::create('rating_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('dimension', ['MD', 'PD', 'TD', 'OP', 'EF', 'FR']);
            $table->string('raw_score');
            $table->timestamps();

            $table->unique(['assessment_id', 'dimension']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_scores');
    }
};
