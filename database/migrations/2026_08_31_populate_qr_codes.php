<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\EventRegistration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find all registrations without QR codes
        $registrations = DB::table('event_registrations')
            ->whereNull('qr_code')
            ->get();

        foreach ($registrations as $reg) {
            // Generate a unique QR code
            $qrCode = Str::random(32);
            while (DB::table('event_registrations')->where('qr_code', $qrCode)->exists()) {
                $qrCode = Str::random(32);
            }

            DB::table('event_registrations')
                ->where('id', $reg->id)
                ->update(['qr_code' => $qrCode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed
    }
};
