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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('contests')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('vacancies')->nullable();
            $table->decimal('salary', 10, 2)->nullable(); // Ex: 10 dígitos no total, 2 após a vírgula
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};