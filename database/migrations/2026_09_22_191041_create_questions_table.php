<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('notice_id')->constrained('notices')->restrictOnDelete();
            $table->text('statement'); // Enunciado da questão
            $table->json('options'); // Opções (ex: ["A", "B", "C", "D", "E"])
            $table->string('correct_answer'); // Resposta correta
            $table->text('explanation')->nullable(); // Explicação
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};