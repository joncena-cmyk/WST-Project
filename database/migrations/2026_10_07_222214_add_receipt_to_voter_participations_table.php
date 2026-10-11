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
        Schema::table('voter_participations', function (Blueprint $table) {
            $table->string('receipt_code', 32)->nullable()->unique()->after('user_id');
            $table->string('verification_hash', 64)->nullable()->after('receipt_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voter_participations', function (Blueprint $table) {
            $table->dropColumn(['receipt_code', 'verification_hash']);
        });
    }
};
