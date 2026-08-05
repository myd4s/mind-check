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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_schedule_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_score');
            $table->string('category'); // 'rendah' | 'sedang' | 'tinggi'
            $table->dateTime('completed_at');
            $table->timestamps();

            $table->unique(['student_id', 'assessment_schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
