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
        Schema::create('contest_student', function (Blueprint $table) {
            $table->foreignId('contest_id')->constrained('contests')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->string('status')->default('enrolled'); // Ex: 'enrolled', 'completed', 'dropped'
            $table->timestamp('enrollment_date')->useCurrent(); // Usa o timestamp atual como padrão
            $table->primary(['contest_id', 'student_id']); // Chave primária composta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contest_student');
    }
};