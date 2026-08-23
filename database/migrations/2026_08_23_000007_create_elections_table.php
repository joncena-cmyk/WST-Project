<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('academic_year', 20)->default('2025-2026');
            $table->dateTime('filing_start_at')->nullable();
            $table->dateTime('filing_end_at')->nullable();
            $table->dateTime('voting_start_at')->nullable();
            $table->dateTime('voting_end_at')->nullable();
            $table->enum('status', ['draft', 'filing_open', 'voting_open', 'closed', 'results_published'])
                  ->default('draft')
                  ->index();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
