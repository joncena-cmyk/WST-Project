<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id')->nullable()->unique()->after('name');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete()->after('student_id');
            $table->string('role')->default('student')->after('program_id'); // student|member|program_rep|officer|president|adviser|admin
            $table->string('profile_picture')->nullable()->after('role');
            $table->string('account_status')->default('active')->after('profile_picture'); // active|suspended|inactive
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn(['student_id', 'program_id', 'role', 'profile_picture', 'account_status']);
        });
    }
};
