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
        Schema::create('notice_subject', function (Blueprint $table) {
            $table->foreignId('notice_id')->constrained('notices')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->decimal('weight', 5, 2)->nullable()->comment('Peso da disciplina no edital');
            $table->integer('question_count')->nullable()->comment('Quantidade de questões');
            $table->text('elimination_criteria')->nullable()->comment('Critérios eliminatórios');
            $table->decimal('minimum_score', 5, 2)->nullable()->comment('Nota mínima');
            $table->decimal('historical_incidence', 5, 2)->nullable()->comment('Incidência histórica (%)');
            $table->primary(['notice_id', 'subject_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_subject');
    }
};