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
            $table->foreignId('page_id')->constrained('survey_pages')->onDelete('cascade');
            $table->string('type'); // text, multiple_choice, rating, etc.
            $table->string('title');
            $table->boolean('is_required')->default(false);
            $table->text('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->json('config')->nullable(); // Validasyon, koşullu mantık, medya referansları
            $table->integer('order_index')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['page_id', 'order_index']);
            $table->index('type');
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
