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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('set null');
            $table->foreignId('template_version_id')->nullable()->constrained('template_versions')->onDelete('set null');
            $table->json('settings')->nullable(); // Anket ayarları (anonim, çoklu yanıt, vb.)
            $table->timestamp('expires_at')->nullable(); // Bitiş tarihi
            $table->integer('max_responses')->nullable(); // Maksimum yanıt sayısı
            $table->timestamps();
            
            // Indexes
            $table->index(['created_by', 'status']);
            $table->index(['template_id', 'template_version_id']);
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
