<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->decimal('payment_amount', 8, 2)->default(50.00)->after('payment_reference');
            $table->string('proof_of_payment_path')->nullable()->after('payment_amount');
            $table->text('rejection_reason')->nullable()->after('proof_of_payment_path');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_reference',
                'payment_amount',
                'proof_of_payment_path',
                'rejection_reason',
            ]);
        });
    }
};
