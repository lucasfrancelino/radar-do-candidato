<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('contests')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Caminho do arquivo PDF do edital
            $table->string('file_name')->nullable(); // Nome original do arquivo
            $table->date('publication_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};