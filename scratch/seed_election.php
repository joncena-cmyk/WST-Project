<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\Candidate;

$officer = User::where('email', 'president@umak.edu.ph')->first() ?? User::first();
$student = User::where('role', 'student')->first();

if ($officer) {
    $election = Election::firstOrCreate(
        ['title' => 'COMSOC Executive Board General Elections 2026-2027'],
        [
            'description' => 'Official student elections for the executive committee and batch representatives of the Computer Society (CCIS).',
            'academic_year' => '2025-2026',
            'filing_start_at' => now()->subDays(5),
            'filing_end_at' => now()->addDays(2),
            'voting_start_at' => now()->subDay(),
            'voting_end_at' => now()->addDays(5),
            'status' => Election::STATUS_VOTING_OPEN,
            'created_by' => $officer->id,
        ]
    );

    $positions = [
        'President',
        'Vice President for Internal Affairs',
        'Vice President for External Affairs',
        'Secretary General',
        'Treasurer',
        'Auditor',
        'Public Information Officer',
    ];

    foreach ($positions as $index => $posTitle) {
        $pos = ElectionPosition::firstOrCreate(
            ['election_id' => $election->id, 'title' => $posTitle],
            ['order' => $index + 1, 'max_winners' => 1]
        );

        if ($posTitle === 'President' && $student) {
            Candidate::firstOrCreate(
                ['election_id' => $election->id, 'user_id' => $student->id],
                [
                    'position_id' => $pos->id,
                    'party_name' => 'Innovators Slate',
                    'platform' => "1. Expand technical workshops in cloud computing and AI.\n2. Strengthen industry internship pipelines for CCIS students.\n3. Transparent financial reporting and democratic governance.",
                    'status' => Candidate::STATUS_APPROVED,
                    'reviewed_by' => $officer->id,
                    'reviewed_at' => now(),
                ]
            );
        }
    }

    echo "Sample election seeded successfully: ID {$election->id}\n";
}
