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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->unique()->constrained('assessments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('total_weight_score', 8, 2);
            $table->decimal('wwl_score', 5, 2);
            $table->string('wl_category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
