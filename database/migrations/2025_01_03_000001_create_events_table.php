<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('venue');
            $table->timestamp('registration_deadline')->nullable();
            $table->unsignedInteger('participant_limit')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('draft');
            // draft | submitted | under_review | returned | approved | published | ongoing | completed | cancelled
            $table->timestamp('published_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'event_date']);
        });

        // Now that events table exists, add the FK on posts.event_id
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
        });
        Schema::dropIfExists('events');
    }
};
