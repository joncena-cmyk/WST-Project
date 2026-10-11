<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\Membership;
use App\Models\Program;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoterParticipation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ElectionDemoSeeder extends Seeder
{
    private array $programs = [];
    private User  $admin;
    private string $password;

    private array $candidatePool = [
        ['name' => 'Marco Antonio Reyes',       'email' => 'marco.reyes@student.umak.edu.ph',       'program' => 'BSCS'],
        ['name' => 'Kyla Marie Santos',          'email' => 'kyla.santos@student.umak.edu.ph',        'program' => 'BSIT'],
        ['name' => 'Joshua Benedict Cruz',       'email' => 'joshua.cruz@student.umak.edu.ph',        'program' => 'BSCS'],
        ['name' => 'Angeline Dizon',             'email' => 'angeline.dizon@student.umak.edu.ph',     'program' => 'BSIT'],
        ['name' => 'Rafael Miguel Dela Cruz',    'email' => 'rafael.delacruz@student.umak.edu.ph',    'program' => 'BSCS'],
        ['name' => 'Patricia Joanne Mendoza',    'email' => 'patricia.mendoza@student.umak.edu.ph',   'program' => 'DIT'],
        ['name' => 'Andrei Kyle Villanueva',     'email' => 'andrei.villanueva@student.umak.edu.ph',  'program' => 'BSCS'],
        ['name' => 'Samantha Rose Garcia',       'email' => 'samantha.garcia@student.umak.edu.ph',    'program' => 'BSIT'],
        ['name' => 'Christian Paolo Soriano',    'email' => 'christian.soriano@student.umak.edu.ph',  'program' => 'BSIT'],
        ['name' => 'Nicole Andrea Ramos',        'email' => 'nicole.ramos@student.umak.edu.ph',       'program' => 'BSCS'],
        ['name' => 'James Lawrence Aquino',      'email' => 'james.aquino@student.umak.edu.ph',       'program' => 'BSCS'],
        ['name' => 'Camille Therese Luna',       'email' => 'camille.luna@student.umak.edu.ph',       'program' => 'BSIT'],
        ['name' => 'Dominic Angelo Torres',      'email' => 'dominic.torres@student.umak.edu.ph',     'program' => 'DIT'],
        ['name' => 'Beatrice Anne Navarro',      'email' => 'beatrice.navarro@student.umak.edu.ph',   'program' => 'BSCS'],
        ['name' => 'Kenneth Russel Flores',      'email' => 'kenneth.flores@student.umak.edu.ph',     'program' => 'BSIT'],
        ['name' => 'Alicia Joy Castillo',        'email' => 'alicia.castillo@student.umak.edu.ph',    'program' => 'BSCS'],
        ['name' => 'Bryan Miguel Morales',       'email' => 'bryan.morales@student.umak.edu.ph',      'program' => 'BSIT'],
        ['name' => 'Diana Grace Pascual',        'email' => 'diana.pascual@student.umak.edu.ph',      'program' => 'DIT'],
        ['name' => 'Aaron John Bautista',        'email' => 'aaron.bautista@student.umak.edu.ph',     'program' => 'BSCS'],
        ['name' => 'Sophia Mae Delgado',         'email' => 'sophia.delgado@student.umak.edu.ph',     'program' => 'BSIT'],
        ['name' => 'Reginald Paulo Lim',         'email' => 'reginald.lim@student.umak.edu.ph',       'program' => 'BSCS'],
        ['name' => 'Francesca Elaine Abad',      'email' => 'francesca.abad@student.umak.edu.ph',     'program' => 'BSIT'],
        ['name' => 'Gabriel Louis Guerrero',     'email' => 'gabriel.guerrero@student.umak.edu.ph',   'program' => 'DIT'],
        ['name' => 'Maricel Joy Escobar',        'email' => 'maricel.escobar@student.umak.edu.ph',    'program' => 'BSCS'],
        ['name' => 'Louie Angelo Fernandez',     'email' => 'louie.fernandez@student.umak.edu.ph',    'program' => 'BSIT'],
        ['name' => 'Tricia Anne Buenaventura',   'email' => 'tricia.buenaventura@student.umak.edu.ph','program' => 'BSCS'],
        ['name' => 'Elton Joseph Manalo',        'email' => 'elton.manalo@student.umak.edu.ph',       'program' => 'BSIT'],
        ['name' => 'Clarisse Nicole Ocampo',     'email' => 'clarisse.ocampo@student.umak.edu.ph',    'program' => 'DIT'],
        ['name' => 'Vincent Carlo Aguilar',      'email' => 'vincent.aguilar@student.umak.edu.ph',    'program' => 'BSCS'],
        ['name' => 'Jennelyn Mae Magno',         'email' => 'jennelyn.magno@student.umak.edu.ph',     'program' => 'BSIT'],
    ];

    private array $platforms = [
        'president' => [
            "My platform is centered on three pillars: Transparency, Inclusion, and Excellence. I will establish an open-door policy, publish monthly financial reports, and hold quarterly town hall meetings. My flagship project is a COMSOC Skills Lab — a free after-hours space for members to access equipment, mentors, and collaborative projects. Together, we will make COMSOC the most active and respected CS/IT organization in the University of Makati.",
            "As your President, I commit to bridging the gap between officers and the general membership. I will launch the COMSOC Buddy System, pairing senior members with freshmen, create a digital resource hub for all enrolled students, and negotiate sponsorship deals to fund free certification exam reviews. I believe in a COMSOC where every member's voice matters.",
            "I stand for a unified, purpose-driven COMSOC. My vision is a society that not only hosts technical seminars but builds real industry connections. I will introduce a COMSOC Internship Placement Drive, an Alumni Mentorship Network, and a Student Startup Incubator in partnership with local tech companies.",
            "My leadership philosophy is service above self. I will prioritize accessible membership, subsidized training materials, and transparent project management. I promise bi-weekly updates to all members and a dedicated feedback portal so we can constantly improve as an organization.",
        ],
        'vp_internal' => [
            "I will revitalize internal operations by digitizing all member records, creating an online event registration system, and launching a COMSOC Discord community server for real-time announcements and collaboration.",
            "My priority is member welfare. I will create a wellness check-in program, establish an academic distress support network, and organize monthly coffee chats between officers and members to keep communication open and genuine.",
            "I will strengthen internal communications by producing a weekly COMSOC Newsletter, creating an active social media calendar, and building a members-only portal with study materials and event recaps.",
            "Internal affairs will be my sole focus. I'll streamline committee formation, standardize event documentation processes, and ensure every member is informed, involved, and valued throughout the academic year.",
            "I will build a stronger community by hosting regular team-building events, setting clear committee KPIs, and creating a transparent task-tracking dashboard accessible to all members.",
            "My commitment is to create an inclusive environment where every member regardless of year level or program feels represented. I will form a Member Relations Committee and host monthly open forums.",
        ],
        'vp_external' => [
            "External partnerships are my specialty. I will pursue at least 10 new MOUs with tech companies, organize an annual COMSOC Tech Expo, and send delegate representatives to national computing conferences.",
            "I will aggressively expand COMSOC's external network through industry immersions, company visits, and a signature Career Connections event where HR professionals and alumni share job market insights.",
            "My vision for external affairs is a COMSOC that is known beyond the campus. I will build partnerships with Google Developer Groups, Microsoft Learn, and AWS Educate to bring certifications and resources to our members.",
            "I will establish a COMSOC Industry Board — a panel of tech professionals who advise the organization, sponsor events, and provide internship opportunities for our members.",
        ],
        'secretary' => [
            "I will modernize our records management by migrating all documents to a secure cloud-based system, ensuring transparency and accessibility for officers and members alike.",
            "Efficient documentation is the backbone of any great organization. I will create standardized templates for all COMSOC forms, establish a 24-hour turnaround for official correspondence, and maintain a comprehensive organizational archive.",
            "My goal as secretary is zero lost documents and 100% meeting attendance tracking. I will introduce digital minutes, e-signatures, and an automated reminder system for deadlines and meetings.",
        ],
        'treasurer' => [
            "Financial transparency is my non-negotiable commitment. I will publish monthly budget reports, create a digital receipts system, and establish an emergency fund for unexpected organizational expenses.",
            "I will introduce zero-based budgeting for all COMSOC projects, conduct quarterly financial audits, and partner with student-friendly payment platforms to make membership fee collection seamless.",
            "My priorities are fiscal responsibility and member value. Every peso collected from membership fees will be accounted for and allocated to programs that directly benefit our student members.",
        ],
        'auditor' => [
            "I will conduct thorough quarterly audits of all organizational finances, procurement processes, and event expenditures, publishing detailed reports accessible to all COMSOC members.",
            "Integrity and accuracy define my approach. I will implement a dual-verification system for all financial transactions and maintain an independent audit trail that can be reviewed at any time.",
        ],
        'pio' => [
            "As PIO, I will transform COMSOC's online presence with consistent branding, high-quality content, and a social media strategy that grows our community and attracts industry attention.",
            "I will create a dedicated COMSOC Media Team, produce a monthly e-magazine, and launch a YouTube channel featuring tutorials, event recaps, and member spotlights.",
            "My campaign is built on communication excellence. I will ensure every achievement, event, and announcement reaches our members through multiple channels — newsletters, SMS, and social media.",
        ],
        'rep' => [
            "I will be the direct voice of BSCS students in all COMSOC decisions, ensuring our program's unique needs — from specialized seminars to hardware access — are consistently represented.",
            "My commitment to BSIT students is clear: I will advocate for IT-specific learning resources, host program-exclusive workshops on emerging web and mobile technologies, and facilitate peer study groups.",
            "As DIT representative, I will ensure our diploma students have equal access to all COMSOC benefits and are not overlooked in resource allocation and event planning.",
            "I will serve as a constant bridge between my program and COMSOC leadership, ensuring our concerns are heard, our achievements celebrated, and our needs met.",
        ],
    ];

    public function run(): void
    {
        $this->programs = [
            'BSCS' => Program::where('code', 'BSCS')->first(),
            'BSIT' => Program::where('code', 'BSIT')->first(),
            'DIT'  => Program::where('code', 'DIT')->first(),
        ];

        $this->admin    = User::where('role', User::ROLE_ADMIN)->firstOrFail();
        $this->password = Hash::make('COMSOC2025!');

        $this->createCandidateUsers();

        $this->createElection1();  // 2023-2024 results_published (93.3% turnout)
        $this->createElection2();  // 2024-2025 closed / tie scenario (73.3% turnout, 50-50 tie in Auditor)
        $this->createElection3();  // 2025-2026 voting_open LIVE (60.0% live tally)
        $this->createElection4();  // 2025-2026 Special filing_open
        $this->createElection5();  // 2026-2027 draft

        $this->command->info('');
        $this->command->info('✅  5 demo elections seeded successfully!');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Election 1 — AY 2023-2024 General Elections | results_published
    // Big roster: 4 presidential, 6 VP Internal, 4 VP External …
    // ──────────────────────────────────────────────────────────────────────────
    private function createElection1(): void
    {
        $election = Election::updateOrCreate(
            ['title' => 'COMSOC General Elections — Academic Year 2023-2024'],
            [
                'description'     => 'The official annual general elections for the COMSOC Executive Board for Academic Year 2023-2024. Results have been officially certified and proclaimed. Congratulations to all elected officers!',
                'academic_year'   => '2023-2024',
                'filing_start_at' => now()->subMonths(14),
                'filing_end_at'   => now()->subMonths(13),
                'voting_start_at' => now()->subMonths(12),
                'voting_end_at'   => now()->subMonths(12)->addDays(3),
                'status'          => Election::STATUS_RESULTS_PUBLISHED,
                'created_by'      => $this->admin->id,
            ]
        );

        $posMap = $this->createPositions($election, [
            ['title' => 'President',                       'order' => 1, 'max_winners' => 1],
            ['title' => 'Vice President for Internal',     'order' => 2, 'max_winners' => 1],
            ['title' => 'Vice President for External',     'order' => 3, 'max_winners' => 1],
            ['title' => 'Secretary',                       'order' => 4, 'max_winners' => 1],
            ['title' => 'Treasurer',                       'order' => 5, 'max_winners' => 1],
            ['title' => 'Auditor',                         'order' => 6, 'max_winners' => 1],
            ['title' => 'Public Information Officer',      'order' => 7, 'max_winners' => 1],
            ['title' => 'BSCS Program Representative',    'order' => 8, 'max_winners' => 1],
            ['title' => 'BSIT Program Representative',    'order' => 9, 'max_winners' => 1],
        ]);

        $this->seedCandidatesWithVotes($election, $posMap, [
            // President — 4 candidates (total: 338 votes)
            [0,  'President',                       142],
            [2,  'President',                        98],
            [6,  'President',                        67],
            [10, 'President',                        31],
            // VP Internal — 6 candidates
            [1,  'Vice President for Internal',     119],
            [4,  'Vice President for Internal',      88],
            [8,  'Vice President for Internal',      72],
            [12, 'Vice President for Internal',      55],
            [16, 'Vice President for Internal',      40],
            [20, 'Vice President for Internal',      18],
            // VP External — 4 candidates
            [3,  'Vice President for External',     108],
            [7,  'Vice President for External',      94],
            [11, 'Vice President for External',      60],
            [15, 'Vice President for External',      22],
            // Secretary — 3 candidates
            [5,  'Secretary',                       127],
            [9,  'Secretary',                        89],
            [13, 'Secretary',                        52],
            // Treasurer — 3 candidates
            [14, 'Treasurer',                       115],
            [18, 'Treasurer',                        97],
            [22, 'Treasurer',                        43],
            // Auditor — 2 candidates
            [17, 'Auditor',                         130],
            [21, 'Auditor',                          88],
            // PIO — 3 candidates
            [19, 'Public Information Officer',      122],
            [23, 'Public Information Officer',       91],
            [27, 'Public Information Officer',       55],
            // Program Reps
            [24, 'BSCS Program Representative',    109],
            [28, 'BSCS Program Representative',     75],
            [25, 'BSIT Program Representative',    118],
            [29, 'BSIT Program Representative',     61],
        ], approved: true, withVotes: true, voterCount: 28);

        $this->command->info('   ↳ Election 1 (2023-2024 · results_published) seeded.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Election 2 — AY 2024-2025 Mid-Year By-Elections | closed
    // Features a dead-tie in Auditor race (showcases tie-handling UI)
    // ──────────────────────────────────────────────────────────────────────────
    private function createElection2(): void
    {
        $election = Election::updateOrCreate(
            ['title' => 'COMSOC Mid-Year By-Elections — Academic Year 2024-2025'],
            [
                'description'     => 'Special mid-year by-elections called to fill three vacated officer positions. Voting has concluded and the results are now pending official proclamation by the Election Committee. Note: The Auditor race ended in a dead tie (50.0% vs 50.0%) — the committee will deliberate or apply secondary deterministic tie-breakers.',
                'academic_year'   => '2024-2025',
                'filing_start_at' => now()->subMonths(6),
                'filing_end_at'   => now()->subMonths(5),
                'voting_start_at' => now()->subMonths(4),
                'voting_end_at'   => now()->subMonths(4)->addDays(2),
                'status'          => Election::STATUS_CLOSED,
                'created_by'      => $this->admin->id,
            ]
        );

        $posMap = $this->createPositions($election, [
            ['title' => 'Secretary', 'order' => 1, 'max_winners' => 1],
            ['title' => 'Treasurer', 'order' => 2, 'max_winners' => 1],
            ['title' => 'Auditor',   'order' => 3, 'max_winners' => 1],
        ]);

        $this->seedCandidatesWithVotes($election, $posMap, [
            // Secretary — 3 candidates, razor-thin margin (74 vs 71 vs 38)
            [26, 'Secretary', 74],
            [5,  'Secretary', 71],
            [13, 'Secretary', 38],
            // Treasurer — 3 candidates
            [14, 'Treasurer', 82],
            [18, 'Treasurer', 60],
            [22, 'Treasurer', 29],
            // Auditor — exact dead tie: 55 vs 55 (50.0% each!)
            [17, 'Auditor',   55],
            [21, 'Auditor',   55],
        ], approved: true, withVotes: true, voterCount: 22);

        $this->command->info('   ↳ Election 2 (2024-2025 · closed, exact tie scenario) seeded.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Election 3 — AY 2025-2026 General Elections | voting_open (LIVE)
    // Big roster with live partial turnout (60.0% real-time tally)
    // ──────────────────────────────────────────────────────────────────────────
    private function createElection3(): void
    {
        $election = Election::updateOrCreate(
            ['title' => 'COMSOC General Elections — Academic Year 2025-2026'],
            [
                'description'     => 'The 2025-2026 General Elections are NOW OPEN! Cast your official secret ballot for the full COMSOC Executive Board. Voting closes on ' . now()->addDays(3)->format('F d, Y \a\t g:i A') . '. All active members are eligible. Every single vote counts — make your voice heard!',
                'academic_year'   => '2025-2026',
                'filing_start_at' => now()->subDays(21),
                'filing_end_at'   => now()->subDays(14),
                'voting_start_at' => now()->subHours(6),
                'voting_end_at'   => now()->addDays(3),
                'status'          => Election::STATUS_VOTING_OPEN,
                'created_by'      => $this->admin->id,
            ]
        );

        $posMap = $this->createPositions($election, [
            ['title' => 'President',                       'order' => 1,  'max_winners' => 1],
            ['title' => 'Vice President for Internal',     'order' => 2,  'max_winners' => 1],
            ['title' => 'Vice President for External',     'order' => 3,  'max_winners' => 1],
            ['title' => 'Secretary',                       'order' => 4,  'max_winners' => 1],
            ['title' => 'Treasurer',                       'order' => 5,  'max_winners' => 1],
            ['title' => 'Auditor',                         'order' => 6,  'max_winners' => 1],
            ['title' => 'Public Information Officer',      'order' => 7,  'max_winners' => 1],
            ['title' => 'BSCS Program Representative',    'order' => 8,  'max_winners' => 1],
            ['title' => 'BSIT Program Representative',    'order' => 9,  'max_winners' => 1],
            ['title' => 'DIT Program Representative',     'order' => 10, 'max_winners' => 1],
        ]);

        // Seed live partial votes (18 voters voted so far) to showcase real-time tally & live percentage breakdown
        $this->seedCandidatesWithVotes($election, $posMap, [
            // President — 4 candidates
            [2,  'President',                      8],
            [6,  'President',                      5],
            [10, 'President',                      3],
            [19, 'President',                      2],
            // VP Internal — 6 candidates
            [1,  'Vice President for Internal',    6],
            [4,  'Vice President for Internal',    5],
            [8,  'Vice President for Internal',    3],
            [12, 'Vice President for Internal',    2],
            [16, 'Vice President for Internal',    1],
            [24, 'Vice President for Internal',    1],
            // VP External — 4 candidates
            [3,  'Vice President for External',    9],
            [7,  'Vice President for External',    5],
            [11, 'Vice President for External',    3],
            [25, 'Vice President for External',    1],
            // Secretary — 3 candidates
            [5,  'Secretary',                      10],
            [9,  'Secretary',                      5],
            [26, 'Secretary',                      3],
            // Treasurer — 3 candidates
            [14, 'Treasurer',                      9],
            [18, 'Treasurer',                      6],
            [22, 'Treasurer',                      3],
            // Auditor — 2 candidates
            [17, 'Auditor',                        11],
            [21, 'Auditor',                        7],
            // PIO — 3 candidates
            [23, 'Public Information Officer',     8],
            [27, 'Public Information Officer',     6],
            [29, 'Public Information Officer',     4],
            // Reps
            [0,  'BSCS Program Representative',   10],
            [20, 'BSCS Program Representative',   8],
            [15, 'BSIT Program Representative',   11],
            [13, 'BSIT Program Representative',   7],
            [28, 'DIT Program Representative',    18],
        ], approved: true, withVotes: true, voterCount: 18, voterOffset: 12);

        $this->command->info('   ↳ Election 3 (2025-2026 · voting_open LIVE with real-time tally) seeded.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Election 4 — AY 2025-2026 Special Election | filing_open
    // ──────────────────────────────────────────────────────────────────────────
    private function createElection4(): void
    {
        $election = Election::updateOrCreate(
            ['title' => 'COMSOC Special Election — PIO & Program Representatives (2025-2026)'],
            [
                'description'     => 'Special election to fill the vacated Public Information Officer seat and two Program Representative vacancies for the remainder of AY 2025-2026. Filing of candidacies is OPEN until ' . now()->addDays(5)->format('F d, Y') . '. Approved candidates will proceed to the voting phase.',
                'academic_year'   => '2025-2026',
                'filing_start_at' => now()->subDays(3),
                'filing_end_at'   => now()->addDays(5),
                'voting_start_at' => now()->addDays(7),
                'voting_end_at'   => now()->addDays(10),
                'status'          => Election::STATUS_FILING_OPEN,
                'created_by'      => $this->admin->id,
            ]
        );

        $posMap = $this->createPositions($election, [
            ['title' => 'Public Information Officer',   'order' => 1, 'max_winners' => 1],
            ['title' => 'BSCS Program Representative', 'order' => 2, 'max_winners' => 1],
            ['title' => 'DIT Program Representative',  'order' => 3, 'max_winners' => 1],
        ]);

        $reviewer = User::whereIn('role', [User::ROLE_VP_INTERNAL, User::ROLE_PRESIDENT])->first() ?? $this->admin;
        $users    = $this->getOrCreateCandidateUsers();

        $filingCandidates = [
            [27, 'Public Information Officer',   'approved'],
            [29, 'Public Information Officer',   'pending'],
            [20, 'BSCS Program Representative',  'approved'],
            [24, 'BSCS Program Representative',  'pending'],
            [28, 'DIT Program Representative',   'approved'],
        ];

        foreach ($filingCandidates as [$poolIdx, $posTitle, $status]) {
            $user = $users[$poolIdx];
            $pos  = $posMap[$posTitle];

            if (Candidate::where('election_id', $election->id)->where('user_id', $user->id)->exists()) {
                continue;
            }

            Candidate::create([
                'election_id'    => $election->id,
                'position_id'    => $pos->id,
                'user_id'        => $user->id,
                'platform'       => $this->getPlatform($posTitle, $poolIdx),
                'party_name'     => null,
                'status'         => $status,
                'reviewed_by'    => $status === 'approved' ? $reviewer->id : null,
                'reviewed_at'    => $status === 'approved' ? now()->subDays(1) : null,
                'review_remarks' => $status === 'approved' ? 'Eligibility verified. Candidacy approved.' : null,
            ]);
        }

        $this->command->info('   ↳ Election 4 (2025-2026 Special · filing_open) seeded.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Election 5 — AY 2026-2027 General Elections | draft
    // ──────────────────────────────────────────────────────────────────────────
    private function createElection5(): void
    {
        $election = Election::updateOrCreate(
            ['title' => 'COMSOC General Elections — Academic Year 2026-2027'],
            [
                'description'     => 'Preliminary planning for the AY 2026-2027 General Elections is underway. Positions and timeline are being finalized by the Election Committee. Stay tuned for the official announcement.',
                'academic_year'   => '2026-2027',
                'filing_start_at' => now()->addMonths(2),
                'filing_end_at'   => now()->addMonths(2)->addWeeks(2),
                'voting_start_at' => now()->addMonths(3),
                'voting_end_at'   => now()->addMonths(3)->addDays(3),
                'status'          => Election::STATUS_DRAFT,
                'created_by'      => $this->admin->id,
            ]
        );

        $this->createPositions($election, [
            ['title' => 'President',                       'order' => 1,  'max_winners' => 1],
            ['title' => 'Vice President for Internal',     'order' => 2,  'max_winners' => 1],
            ['title' => 'Vice President for External',     'order' => 3,  'max_winners' => 1],
            ['title' => 'Secretary',                       'order' => 4,  'max_winners' => 1],
            ['title' => 'Treasurer',                       'order' => 5,  'max_winners' => 1],
            ['title' => 'Auditor',                         'order' => 6,  'max_winners' => 1],
            ['title' => 'Public Information Officer',      'order' => 7,  'max_winners' => 1],
            ['title' => 'BSCS Program Representative',    'order' => 8,  'max_winners' => 1],
            ['title' => 'BSIT Program Representative',    'order' => 9,  'max_winners' => 1],
            ['title' => 'DIT Program Representative',     'order' => 10, 'max_winners' => 1],
        ]);

        $this->command->info('   ↳ Election 5 (2026-2027 · draft) seeded.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Shared helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function createCandidateUsers(): void
    {
        foreach ($this->candidatePool as $data) {
            $prog = $this->programs[$data['program']] ?? null;
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => $this->password,
                    'role'              => User::ROLE_STUDENT,
                    'program_id'        => $prog?->id,
                    'account_status'    => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->memberships()->where('status', Membership::STATUS_ACTIVE)->exists()) {
                Membership::create([
                    'user_id'       => $user->id,
                    'program_id'    => $prog?->id,
                    'academic_year' => '2025-2026',
                    'status'        => Membership::STATUS_ACTIVE,
                    'approved_by'   => $this->admin->id,
                    'approved_at'   => now()->subMonths(3),
                    'start_date'    => now()->subMonths(3)->toDateString(),
                    'end_date'      => now()->addMonths(9)->toDateString(),
                ]);
            }
        }
    }

    private function getOrCreateCandidateUsers(): array
    {
        $users = [];
        foreach ($this->candidatePool as $idx => $data) {
            $users[$idx] = User::where('email', $data['email'])->firstOrFail();
        }
        return $users;
    }

    private function createPositions(Election $election, array $positions): array
    {
        $map = [];
        foreach ($positions as $pos) {
            $model = ElectionPosition::updateOrCreate(
                [
                    'election_id' => $election->id,
                    'title'       => $pos['title'],
                ],
                [
                    'order'       => $pos['order'],
                    'max_winners' => $pos['max_winners'],
                ]
            );
            $map[$pos['title']] = $model;
        }
        return $map;
    }

    private function seedCandidatesWithVotes(
        Election $election,
        array    $posMap,
        array    $assignments,
        bool     $approved,
        bool     $withVotes,
        int      $voterCount = 0,
        int      $voterOffset = 0
    ): void {
        $reviewer = User::whereIn('role', [User::ROLE_VP_INTERNAL, User::ROLE_PRESIDENT])->first() ?? $this->admin;
        $users    = $this->getOrCreateCandidateUsers();

        foreach ($assignments as [$poolIdx, $posTitle, $voteCount]) {
            $user     = $users[$poolIdx];
            $pos      = $posMap[$posTitle];
            $platform = $this->getPlatform($posTitle, $poolIdx);

            $candidate = Candidate::updateOrCreate(
                [
                    'election_id' => $election->id,
                    'position_id' => $pos->id,
                    'user_id'     => $user->id,
                ],
                [
                    'platform'       => $platform,
                    'party_name'     => $this->getPartyName($poolIdx),
                    'status'         => $approved ? Candidate::STATUS_APPROVED : Candidate::STATUS_PENDING,
                    'reviewed_by'    => $approved ? $reviewer->id : null,
                    'reviewed_at'    => $approved ? now()->subDays(3) : null,
                    'review_remarks' => $approved ? 'Eligibility verified. Candidacy approved.' : null,
                ]
            );

            if ($withVotes && $voteCount > 0) {
                // Clear existing simulated votes for idempotency
                Vote::where('election_id', $election->id)
                    ->where('position_id', $pos->id)
                    ->where('candidate_id', $candidate->id)
                    ->delete();

                for ($v = 0; $v < $voteCount; $v++) {
                    Vote::create([
                        'election_id'  => $election->id,
                        'position_id'  => $pos->id,
                        'candidate_id' => $candidate->id,
                        'created_at'   => now()->subMonths(4)->addMinutes(rand(1, 4320)),
                    ]);
                }
            }
        }

        // Seed realistic VoterParticipation records for the election
        if ($voterCount > 0) {
            $slice = array_slice($users, $voterOffset, $voterCount);
            foreach ($slice as $voter) {
                VoterParticipation::updateOrCreate(
                    [
                        'election_id' => $election->id,
                        'user_id'     => $voter->id,
                    ],
                    [
                        'receipt_code'      => 'VOTE-' . substr($election->academic_year, 0, 4) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
                        'verification_hash' => hash('sha256', $voter->id . '|' . $election->id . '|' . now()->toIso8601String() . '|' . config('app.key')),
                        'voted_at'          => now()->subMonths(4)->addMinutes(rand(1, 4320)),
                    ]
                );
            }
        }
    }

    private function getPlatform(string $posTitle, int $idx): string
    {
        $title = strtolower($posTitle);
        $key = match (true) {
            str_contains($title, 'president') && !str_contains($title, 'vice') => 'president',
            str_contains($title, 'internal')    => 'vp_internal',
            str_contains($title, 'external')    => 'vp_external',
            str_contains($title, 'secretary')   => 'secretary',
            str_contains($title, 'treasurer')   => 'treasurer',
            str_contains($title, 'auditor')     => 'auditor',
            str_contains($title, 'information') => 'pio',
            default                             => 'rep',
        };
        $pool = $this->platforms[$key];
        return $pool[$idx % count($pool)];
    }

    private function getPartyName(int $idx): ?string
    {
        $parties = [
            'COMSOC Unity Party',
            'TechServe Alliance',
            'InnovatePH Slate',
            'Alyansa ng COMSOC',
            null,
            null,
            'Progressive CS Bloc',
        ];
        return $parties[$idx % count($parties)];
    }
}
