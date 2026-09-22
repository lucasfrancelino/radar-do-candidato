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
        Schema::create('position_student', function (Blueprint $table) {
            $table->foreignId('position_id')->constrained('positions')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->string('status')->default('interested'); // Ex: 'interested', 'applied', 'rejected'
            $table->timestamp('interest_date')->useCurrent(); // Usa o timestamp atual como padrão
            $table->primary(['position_id', 'student_id']); // Chave primária composta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_student');
    }
};