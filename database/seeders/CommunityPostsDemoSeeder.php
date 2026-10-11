<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CommunityPostsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $officers = User::whereIn('role', [
            User::ROLE_PRESIDENT,
            User::ROLE_VP_INTERNAL,
            User::ROLE_VP_EXTERNAL,
            User::ROLE_SECRETARY,
            User::ROLE_TREASURER,
            User::ROLE_AUDITOR,
            User::ROLE_PIO,
            User::ROLE_PROGRAM_REP,
            User::ROLE_ADMIN,
        ])->get()->keyBy('role');

        $admin = $officers->get(User::ROLE_ADMIN) ?? User::where('email', 'admin@umak.edu.ph')->first();
        $president = $officers->get(User::ROLE_PRESIDENT) ?? $admin;
        $vpInternal = $officers->get(User::ROLE_VP_INTERNAL) ?? $admin;
        $vpExternal = $officers->get(User::ROLE_VP_EXTERNAL) ?? $admin;
        $secretary = $officers->get(User::ROLE_SECRETARY) ?? $admin;
        $treasurer = $officers->get(User::ROLE_TREASURER) ?? $admin;
        $auditor = $officers->get(User::ROLE_AUDITOR) ?? $admin;
        $pio = $officers->get(User::ROLE_PIO) ?? $admin;

        $csRep = User::where('email', 'johnpatrick.rongalerios@umak.edu.ph')->first()
            ?? User::where('email', 'cs.rep@umak.edu.ph')->first()
            ?? $admin;

        $itRep = User::where('email', 'charles.vega@umak.edu.ph')->first()
            ?? User::where('email', 'it.rep@umak.edu.ph')->first()
            ?? $admin;

        $students = User::where('role', User::ROLE_STUDENT)->get();
        if ($students->isEmpty()) {
            $students = User::where('email', 'like', '%@student.umak.edu.ph')->get();
        }

        // Get events for linking
        $techSummitEvent = Event::where('title', 'like', '%Tech Summit%')->first();
        $hackathonEvent  = Event::where('title', 'like', '%CodeFest%')->first();
        $gitEvent        = Event::where('title', 'like', '%Git & GitHub%')->first();
        $hacktoberEvent  = Event::where('title', 'like', '%Hacktoberfest%')->first();
        $uiuxEvent       = Event::where('title', 'like', '%UI/UX%')->first();

        // -------------------------------------------------------------------
        // 25+ Posts Definition
        // -------------------------------------------------------------------
        $postsData = [
            // 1. Pinned Official Welcome Post
            [
                'author'     => $president,
                'type'       => Post::TYPE_OFFICIAL,
                'is_pinned'  => true,
                'event_id'   => null,
                'body'       => "🌟 **Welcome to the Official Computer Society (ComSoc) Community Hub!** 🌟\n\nWelcome Herons and fellow tech innovators! This platform is your central space for announcements, academic discussions, event registrations, student elections, and tech project collaborations.\n\nKeep discussions respectful, inspire one another, and let's build the future together! 💻🚀\n\n— *Tristan Donne Brocales, COMSOC President*",
                'created_at' => Carbon::now()->subMonths(5),
                'likes'      => 28,
                'comments'   => [
                    ['user' => $students[0] ?? $admin, 'body' => 'Excited for this academic year! Kudos to the web team for this awesome portal! 🎉'],
                    ['user' => $students[1] ?? $admin, 'body' => 'Let\'s go COMSOC! Looking forward to upcoming workshops.'],
                    ['user' => $vpInternal, 'body' => 'Welcome everyone! Don\'t hesitate to reach out if you need assistance with org activities.'],
                ],
            ],

            // 2. Official Announcement: Tech Summit 2026
            [
                'author'     => $pio,
                'type'       => Post::TYPE_OFFICIAL,
                'is_pinned'  => false,
                'event_id'   => $techSummitEvent?->id,
                'body'       => "📢 **ANNOUNCEMENT: Registration is NOW OPEN for ComSoc Tech Summit 2026!**\n\nTheme: *Next-Gen AI & Web3 Horizons*\n📅 Date: In 2 Weeks\n📍 Venue: UMak University Auditorium\n\nJoin us for keynote talks from industry engineers, interactive exhibits, and exclusive swags. Limited slots available—register now via the Events tab!",
                'created_at' => Carbon::now()->subDays(5),
                'likes'      => 24,
                'comments'   => [
                    ['user' => $students[2] ?? $admin, 'body' => 'Already registered! Will there be certificates for attendees?'],
                    ['user' => $pio, 'body' => 'Yes! Digital verifiable certificates will be generated right here on ComSoc Hub after attendance check-in.'],
                    ['user' => $students[3] ?? $admin, 'body' => 'Can non-CCIS students also attend?'],
                    ['user' => $vpExternal, 'body' => 'Yes, guest passes are open for allied STEM students!'],
                ],
            ],

            // 3. Official Announcement: Membership Fee & Validation
            [
                'author'     => $treasurer,
                'type'       => Post::TYPE_OFFICIAL,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "💳 **ADVISORY: 1st Semester AY 2026-2027 Membership Validation**\n\nAll officially enrolled CCIS students who have settled their organizational dues may now verify their membership status on their profile page to unlock voting eligibility and free workshop passes.\n\nFor payment concerns or GCash reference verification, visit the Treasury desk at the CCIS Student Lounge.",
                'created_at' => Carbon::now()->subMonths(1),
                'likes'      => 19,
                'comments'   => [
                    ['user' => $students[4] ?? $admin, 'body' => 'Just verified my membership, instant approval! Smooth system.'],
                    ['user' => $auditor, 'body' => 'All transaction receipts are properly audited and logged. Thank you for your support!'],
                ],
            ],

            // 4. Official Post: CodeFest 2026 Proclamation
            [
                'author'     => $vpInternal,
                'type'       => Post::TYPE_OFFICIAL,
                'is_pinned'  => false,
                'event_id'   => $hackathonEvent?->id,
                'body'       => "🏆 **CodeFest 2026: Official Champions & Special Awards Announced!**\n\nCongratulations to **Team NeuralHerons** for taking home the Grand Champion trophy with their automated campus accessibility app!\n\n🥈 1st Runner Up: *Team ByteCraft*\n🥉 2nd Runner Up: *Team Algorithmic Titans*\n\nThank you to all 20 competing teams for showing world-class problem-solving skills!",
                'created_at' => Carbon::now()->subMonths(2),
                'likes'      => 32,
                'comments'   => [
                    ['user' => $students[5] ?? $admin, 'body' => 'Great competition! Learned so much about rapid prototyping under 24 hours.'],
                    ['user' => $students[6] ?? $admin, 'body' => 'Congrats to all winners! Well-deserved NeuralHerons! 👏'],
                ],
            ],

            // 5. Official Post: Call for Volunteers
            [
                'author'     => $secretary,
                'type'       => Post::TYPE_OFFICIAL,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "🤝 **CALL FOR STUDENT VOLUNTEERS: Join the ComSoc Committees!**\n\nWe are recruiting passionate members for:\n- 🎨 Creative Media & Documentation\n- ⚙️ Technical & Logistics Team\n- 💻 Web & Systems Development Unit\n\nGain leadership hours, hands-on experience, and exclusive committee merch. Submit your intent through the committees page!",
                'created_at' => Carbon::now()->subWeeks(2),
                'likes'      => 21,
                'comments'   => [
                    ['user' => $students[7] ?? $admin, 'body' => 'Submitted my application for Web & Systems! Excited to contribute.'],
                ],
            ],

            // 6. Official Post: Academic Mentorship
            [
                'author'     => $csRep,
                'type'       => Post::TYPE_OFFICIAL,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "📚 **Peer Mentorship Program: Data Structures & Algorithms (CS201)**\n\nStruggling with Linked Lists, Trees, Graph Traversals, or Dynamic Programming? Free weekly tutoring sessions by senior CS honor students every Wednesday 4:00 PM at Lab 4.\n\nSign up at the CCIS Department bulletin or message here!",
                'created_at' => Carbon::now()->subWeeks(3),
                'likes'      => 27,
                'comments'   => [
                    ['user' => $students[8] ?? $admin, 'body' => 'This is a lifesaver for upcoming midterms! See you guys on Wednesday.'],
                ],
            ],

            // 7. Community Post: DSA Midterm Tips
            [
                'author'     => $students[0] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Any seniors here who can share tips on how to prepare for the CS201 machine problem exam? Is it mostly pointer manipulation or time complexity analysis?",
                'created_at' => Carbon::now()->subDays(12),
                'likes'      => 14,
                'comments'   => [
                    ['user' => $students[9] ?? $admin, 'body' => 'Practice writing clean recursion and draw the memory stack pointers on paper before typing code!'],
                    ['user' => $csRep, 'body' => 'Make sure you understand Big-O edge cases (e.g. worst case quicksort vs mergesort). Good luck!'],
                ],
            ],

            // 8. Community Post: Capstone Tech Stack
            [
                'author'     => $students[1] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "What tech stack are you guys planning to use for your 3rd year Web Development capstone? We're leaning towards Laravel + Alpine.js + Tailwind vs Next.js + FastAPI. Would love some insights on maintainability!",
                'created_at' => Carbon::now()->subDays(10),
                'likes'      => 18,
                'comments'   => [
                    ['user' => $students[2] ?? $admin, 'body' => 'Laravel TALL stack (Tailwind, Alpine, Laravel, Livewire) is super fast to build with for university projects.'],
                    ['user' => $itRep, 'body' => 'Pick what your entire team is comfortable with. Maintainability and solid database schema design matter more than chasing hype!'],
                ],
            ],

            // 9. Community Post: Docker vs WSL2
            [
                'author'     => $students[3] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Quick question: For those running Windows 11, do you prefer Docker Desktop with WSL2 backend or native Linux dual-boot for developing backend microservices?",
                'created_at' => Carbon::now()->subDays(9),
                'likes'      => 11,
                'comments'   => [
                    ['user' => $students[4] ?? $admin, 'body' => 'WSL2 + Ubuntu + VS Code Remote extension is virtually identical to native Linux. Highly recommended!'],
                ],
            ],

            // 10. Community Post: Git Workshop Feedback
            [
                'author'     => $students[5] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => $gitEvent?->id,
                'body'       => "Big shoutout to the organizers of the Git & GitHub Essentials workshop last week! Finally understood `git rebase -i` and how to resolve detached HEAD states without panicking haha! 🙌",
                'created_at' => Carbon::now()->subDays(6),
                'likes'      => 22,
                'comments'   => [
                    ['user' => $secretary, 'body' => 'Glad it helped! The slides and sample exercise repository are uploaded on the event page.'],
                ],
            ],

            // 11. Community Post: Hackathon Teammates Search
            [
                'author'     => $students[6] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Looking for 1 UI/UX Designer and 1 Backend Developer (Node.js or Python) to team up for the upcoming National Collegiate Hackathon 2026. Comment below or DM me if interested!",
                'created_at' => Carbon::now()->subDays(7),
                'likes'      => 15,
                'comments'   => [
                    ['user' => $students[7] ?? $admin, 'body' => 'I do UI/UX in Figma and frontend CSS! Sent you a message on Discord.'],
                ],
            ],

            // 12. Community Post: CSS Cheatsheet
            [
                'author'     => $students[8] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Compiled a comprehensive cheat sheet for CSS Grid vs Flexbox alignments and common responsive layout pitfalls. Feel free to bookmark it for your web design lab subjects! 📑💡",
                'created_at' => Carbon::now()->subDays(8),
                'likes'      => 25,
                'comments'   => [
                    ['user' => $students[9] ?? $admin, 'body' => 'Bookmarked! Thank you for sharing this with the community.'],
                ],
            ],

            // 13. Community Post: Hacktoberfest Participation
            [
                'author'     => $students[10] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => $hacktoberEvent?->id,
                'body'       => "Who's participating in Hacktoberfest this month? Already got 2 pull requests merged into open source repositories! Don't forget to register your GitHub handles.",
                'created_at' => Carbon::now()->subDays(2),
                'likes'      => 16,
                'comments'   => [
                    ['user' => $pio, 'body' => 'Awesome work! We have beginner-friendly issues tagged on our ComSoc repositories too.'],
                ],
            ],

            // 14. Community Post: Modern PHP & Laravel
            [
                'author'     => $students[11] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "PHP in 2026 is so fast and elegant with PHP 8.3 type systems, enums, readonly classes, and match expressions. For anyone still thinking PHP is outdated, check out Laravel 11!",
                'created_at' => Carbon::now()->subDays(4),
                'likes'      => 30,
                'comments'   => [
                    ['user' => $president, 'body' => '100%! Modern PHP has evolved tremendously. Great ecosystem and community.'],
                ],
            ],

            // 15. Community Post: Mechanical Keyboard Discussion
            [
                'author'     => $students[12] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Mechanical keyboard enthusiasts of CCIS: What silent switches do you recommend for typing in university library study rooms without annoying everyone nearby? ⌨️🔇",
                'created_at' => Carbon::now()->subDays(11),
                'likes'      => 12,
                'comments'   => [
                    ['user' => $students[13] ?? $admin, 'body' => 'Outemu Silent Peach or Gateron Silent Yellows! Super quiet and smooth.'],
                ],
            ],

            // 16. Community Post: AI & LLM Tools in Software Engineering
            [
                'author'     => $students[14] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "How are professors in CCIS adapting to AI coding assistants? Are we going to see more emphasis on oral code defense and architectural design rather than rote syntax memorization?",
                'created_at' => Carbon::now()->subDays(13),
                'likes'      => 20,
                'comments'   => [
                    ['user' => $csRep, 'body' => 'Yes! The department is shifting towards deep code comprehension, security auditing, and design patterns.'],
                ],
            ],

            // 17. Community Post: VS Code Extensions
            [
                'author'     => $students[15] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "What are your top 3 must-have VS Code extensions for web development?\n\nMine: 1. Prettier, 2. GitLens, 3. Error Lens. Can't code without them!",
                'created_at' => Carbon::now()->subDays(14),
                'likes'      => 17,
                'comments'   => [
                    ['user' => $students[16] ?? $admin, 'body' => 'Auto Rename Tag and Tailwind CSS IntelliSense are indispensable too!'],
                ],
            ],

            // 18. Community Post: SQL Database Choice
            [
                'author'     => $students[17] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "PostgreSQL vs MySQL for relational database management subjects: Which dialect do you find more versatile for JSON columns and complex window functions?",
                'created_at' => Carbon::now()->subDays(15),
                'likes'      => 13,
                'comments'   => [
                    ['user' => $itRep, 'body' => 'PostgreSQL has incredible indexing features and JSONB support, definitely worth mastering.'],
                ],
            ],

            // 19. Community Post: Discrete Math Review Group
            [
                'author'     => $students[18] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Forming a weekend Discord review group for Discrete Mathematics (Set Theory, Combinatorics, and Proof by Induction). Leave your handle if you want to join!",
                'created_at' => Carbon::now()->subDays(16),
                'likes'      => 10,
                'comments'   => [
                    ['user' => $students[19] ?? $admin, 'body' => 'Count me in! Need to review proofs before next week.'],
                ],
            ],

            // 20. Community Post: Mobile Dev Workshop Poll
            [
                'author'     => $students[20] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Survey: Would students prefer a hands-on workshop on Flutter (Dart) or React Native (TypeScript) for cross-platform mobile development next semester?",
                'created_at' => Carbon::now()->subDays(17),
                'likes'      => 26,
                'comments'   => [
                    ['user' => $students[21] ?? $admin, 'body' => 'Flutter please! The widget tree and hot reload are amazing.'],
                    ['user' => $students[22] ?? $admin, 'body' => 'React Native would be great for those of us already doing React web.'],
                ],
            ],

            // 21. Community Post: Linux Terminal Basics
            [
                'author'     => $students[23] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Tip for 1st years taking Operating Systems: Learn `grep`, `sed`, `awk`, and pipe operators `|` early on. It saves hours of manual text processing in lab assignments!",
                'created_at' => Carbon::now()->subDays(18),
                'likes'      => 19,
                'comments'   => [
                    ['user' => $students[24] ?? $admin, 'body' => 'Also alias `ll` to `ls -la` in your `.bashrc`! Super convenient.'],
                ],
            ],

            // 22. Community Post: UI/UX Workshop Excitement
            [
                'author'     => $students[25] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => $uiuxEvent?->id,
                'body'       => "Saw the upcoming UI/UX Design Jam event posted on the portal! Ready to show off some sleek glassmorphism and modern micro-interactions in Figma. Who else is joining?",
                'created_at' => Carbon::now()->subDays(3),
                'likes'      => 14,
                'comments'   => [
                    ['user' => $vpInternal, 'body' => 'Bring your creative energy! We have exciting prompts and industry mentors reviewing your designs.'],
                ],
            ],

            // 23. Community Post: Competitive Programming Mock Contests
            [
                'author'     => $students[26] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Can we have monthly virtual LeetCode mock contests on Saturday nights? Great way to sharpen problem solving for technical interviews and ICPC qualifiers.",
                'created_at' => Carbon::now()->subDays(20),
                'likes'      => 23,
                'comments'   => [
                    ['user' => $president, 'body' => 'We are coordinating with the Competitive Programming sub-club to schedule bi-weekly contests! Stay tuned.'],
                ],
            ],

            // 24. Community Post: Accessibility in UI
            [
                'author'     => $students[27] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "Friendly reminder for fellow frontend developers: Always test your color contrast ratios (WCAG AA/AAA) and include `aria-labels` on icon buttons. Accessibility makes tech inclusive for everyone!",
                'created_at' => Carbon::now()->subDays(21),
                'likes'      => 28,
                'comments'   => [
                    ['user' => $students[28] ?? $admin, 'body' => 'Crucial point! Web accessibility should be standard practice in all our project submissions.'],
                ],
            ],

            // 25. Community Post: Senior Gratitude & Org Pride
            [
                'author'     => $students[29] ?? $admin,
                'type'       => Post::TYPE_COMMUNITY,
                'is_pinned'  => false,
                'event_id'   => null,
                'body'       => "As a graduating senior, seeing how far ComSoc has come with this integrated web platform, active workshops, and transparent elections makes me so proud to be a Heron. Keep raising the bar!",
                'created_at' => Carbon::now()->subDays(22),
                'likes'      => 35,
                'comments'   => [
                    ['user' => $president, 'body' => 'Thank you for paving the way for all of us! Once a Heron, always a Heron! 🦅💙'],
                    ['user' => $secretary, 'body' => 'Best of luck in your tech career! Don\'t forget to come back as guest speakers!'],
                ],
            ],
        ];

        foreach ($postsData as $pData) {
            $post = Post::updateOrCreate(
                [
                    'user_id' => $pData['author']->id,
                    'body'    => $pData['body'],
                ],
                [
                    'type'       => $pData['type'],
                    'is_pinned'  => $pData['is_pinned'],
                    'event_id'   => $pData['event_id'],
                    'created_at' => $pData['created_at'],
                    'updated_at' => $pData['created_at'],
                ]
            );

            // Add reactions (likes)
            $likeCount = $pData['likes'];
            $likers = $students->shuffle()->take(min($likeCount, $students->count()));
            foreach ($likers as $liker) {
                PostReaction::updateOrCreate(
                    [
                        'post_id' => $post->id,
                        'user_id' => $liker->id,
                    ],
                    [
                        'type'       => 'like',
                        'created_at' => Carbon::parse($post->created_at)->addHours(rand(1, 48)),
                    ]
                );
            }

            // Add comments
            if (!empty($pData['comments'])) {
                foreach ($pData['comments'] as $cIdx => $cData) {
                    $comment = Comment::updateOrCreate(
                        [
                            'post_id' => $post->id,
                            'user_id' => $cData['user']->id,
                            'body'    => $cData['body'],
                        ],
                        [
                            'parent_id'  => null,
                            'created_at' => Carbon::parse($post->created_at)->addMinutes(($cIdx + 1) * 35),
                            'updated_at' => Carbon::parse($post->created_at)->addMinutes(($cIdx + 1) * 35),
                        ]
                    );
                }
            }
        }
    }
}
