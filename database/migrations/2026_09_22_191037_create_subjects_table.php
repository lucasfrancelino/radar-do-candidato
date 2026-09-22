<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2)->default(1.00); // Peso da disciplina
            $table->integer('question_count')->default(0); // Quantidade de questões
            $table->text('elimination_criteria')->nullable(); // Critérios eliminatórios
            $table->decimal('minimum_score', 5, 2)->nullable(); // Nota mínima
            $table->text('historical_incidence')->nullable(); // Incidência histórica
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};