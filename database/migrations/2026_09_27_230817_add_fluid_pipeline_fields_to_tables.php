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
        Schema::table('partnerships', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('partner_id')->constrained('events')->nullOnDelete();
            $table->foreignId('proposal_document_id')->nullable()->after('event_id')->constrained('documents')->nullOnDelete();
            $table->decimal('sponsorship_amount', 12, 2)->default(0)->after('academic_year');
            $table->longText('letter_content')->nullable()->after('description');
            $table->foreignId('endorsed_by')->nullable()->after('reviewed_by')->constrained('users')->nullOnDelete();
            $table->dateTime('endorsed_at')->nullable()->after('reviewed_at');
            $table->text('endorsement_remarks')->nullable()->after('review_remarks');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('partnership_id')->nullable()->after('created_by')->constrained('partnerships')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->after('partnership_id')->constrained('partner_organizations')->nullOnDelete();
            $table->boolean('is_collaboration')->default(false)->after('partner_id');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('partnership_id')->nullable()->after('event_id')->constrained('partnerships')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['partnership_id']);
            $table->dropColumn('partnership_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['partnership_id']);
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['partnership_id', 'partner_id', 'is_collaboration']);
        });

        Schema::table('partnerships', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['proposal_document_id']);
            $table->dropForeign(['endorsed_by']);
            $table->dropColumn([
                'event_id',
                'proposal_document_id',
                'sponsorship_amount',
                'letter_content',
                'endorsed_by',
                'endorsed_at',
                'endorsement_remarks',
            ]);
        });
    }
};
