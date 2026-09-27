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
            $table->string('response_token', 64)->nullable()->unique()->after('status');
            $table->string('partner_response_status', 30)->nullable()->after('response_token'); // accepted, changes_requested, declined
            $table->string('partner_responder_name')->nullable()->after('partner_response_status');
            $table->string('partner_responder_title')->nullable()->after('partner_responder_name');
            $table->text('partner_response_remarks')->nullable()->after('partner_responder_title');
            $table->dateTime('partner_responded_at')->nullable()->after('partner_response_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnerships', function (Blueprint $table) {
            $table->dropColumn([
                'response_token',
                'partner_response_status',
                'partner_responder_name',
                'partner_responder_title',
                'partner_response_remarks',
                'partner_responded_at',
            ]);
        });
    }
};
