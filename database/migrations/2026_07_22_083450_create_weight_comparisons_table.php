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
        Schema::create('weight_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('pair_number');
            $table->enum('dimension_choosen', ['MD', 'PD', 'TD', 'OP', 'EF', 'FR']);
            $table->timestamps();

            $table->unique(['assessment_id', 'pair_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weight_comparisons');
    }
};
