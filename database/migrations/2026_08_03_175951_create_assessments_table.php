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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->unsignedTinyInteger('depression_raw')->nullable();
            $table->unsignedTinyInteger('anxiety_raw')->nullable();
            $table->unsignedTinyInteger('stress_raw')->nullable();

            $table->unsignedTinyInteger('depression_score')->nullable();
            $table->unsignedTinyInteger('anxiety_score')->nullable();
            $table->unsignedTinyInteger('stress_score')->nullable();

            $table->enum('depression_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe'])->nullable();
            $table->enum('anxiety_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe'])->nullable();
            $table->enum('stress_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe'])->nullable();
            $table->enum('overall_severity', ['normal', 'mild', 'moderate', 'severe', 'extremely_severe'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
