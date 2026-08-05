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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('reverse_scored')->default(false);
            $table->boolean('is_active')->default(true);
            // Soal inti PSS-10 ter-seed & read-only di UI (PRD §9.1) — integritas
            // psikometri (redaksi & reverse-scoring) tidak boleh diubah guru BK.
            $table->boolean('is_core')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
