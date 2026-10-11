<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $officers = User::whereIn('role', [
            User::ROLE_PRESIDENT,
            User::ROLE_VP_INTERNAL,
            User::ROLE_VP_EXTERNAL,
            User::ROLE_SECRETARY,
            User::ROLE_PIO,
            User::ROLE_PROGRAM_REP,
            User::ROLE_ADMIN,
        ])->get()->keyBy('role');

        $admin = $officers->get(User::ROLE_ADMIN) ?? User::where('email', 'admin@umak.edu.ph')->first();
        $president = $officers->get(User::ROLE_PRESIDENT) ?? $admin;
        $vpInternal = $officers->get(User::ROLE_VP_INTERNAL) ?? $admin;
        $vpExternal = $officers->get(User::ROLE_VP_EXTERNAL) ?? $admin;
        $secretary = $officers->get(User::ROLE_SECRETARY) ?? $admin;
        $pio = $officers->get(User::ROLE_PIO) ?? $admin;

        $students = User::where('role', User::ROLE_STUDENT)->get();
        if ($students->isEmpty()) {
            $students = User::where('email', 'like', '%@student.umak.edu.ph')->get();
        }

        // -------------------------------------------------------------------
        // 1. Past / Completed Events (4 Events)
        // -------------------------------------------------------------------
        $eventsData = [
            [
                'title'                 => 'ComSoc General Assembly 2025: Ignite & Innovate',
                'description'           => 'The official kick-off assembly of the Computer Society for Academic Year 2025-2026. Introducing the Executive Board, organizational roadmap, upcoming tech workshops, and member benefits.',
                'event_date'            => Carbon::now()->subMonths(6)->toDateString(),
                'start_time'            => '09:00:00',
                'end_time'              => '16:00:00',
                'venue'                 => 'Grand Theater, University of Makati',
                'registration_deadline' => Carbon::now()->subMonths(6)->subDays(2),
                'participant_limit'     => 300,
                'status'                => Event::STATUS_COMPLETED,
                'published_at'          => Carbon::now()->subMonths(7),
                'created_by'            => $president->id,
            ],
            [
                'title'                 => 'CodeFest 2026: 24-Hour Collegiate Hackathon',
                'description'           => 'An intensive hackathon challenging CCIS students to build cutting-edge software solutions addressing community resilience, smart campus utilities, and public health in Makati City.',
                'event_date'            => Carbon::now()->subMonths(2)->toDateString(),
                'start_time'            => '08:00:00',
                'end_time'              => '20:00:00',
                'venue'                 => 'CCIS Computer Laboratories 4 & 5',
                'registration_deadline' => Carbon::now()->subMonths(2)->subDays(5),
                'participant_limit'     => 80,
                'status'                => Event::STATUS_COMPLETED,
                'published_at'          => Carbon::now()->subMonths(3),
                'created_by'            => $vpInternal->id,
            ],
            [
                'title'                 => 'Cybersecurity & Ethical Hacking Bootcamp',
                'description'           => 'A deep-dive hands-on workshop on network packet analysis, web vulnerability assessment (OWASP Top 10), and ethical penetration testing methodologies led by industry security analysts.',
                'event_date'            => Carbon::now()->subWeeks(4)->toDateString(),
                'start_time'            => '13:00:00',
                'end_time'              => '17:30:00',
                'venue'                 => 'CCIS Audio-Visual Room (AVR)',
                'registration_deadline' => Carbon::now()->subWeeks(4)->subDays(3),
                'participant_limit'     => 60,
                'status'                => Event::STATUS_COMPLETED,
                'published_at'          => Carbon::now()->subWeeks(6),
                'created_by'            => $vpExternal->id,
            ],
            [
                'title'                 => 'Git & GitHub Essentials: Collaborative Workflow Masterclass',
                'description'           => 'Master version control, branching strategies, resolving merge conflicts, pull request reviews, and continuous integration workflows for academic and industry projects.',
                'event_date'            => Carbon::now()->subWeeks(1)->toDateString(),
                'start_time'            => '10:00:00',
                'end_time'              => '15:00:00',
                'venue'                 => 'Computer Lab 3, CCIS Building',
                'registration_deadline' => Carbon::now()->subWeeks(1)->subDays(2),
                'participant_limit'     => 50,
                'status'                => Event::STATUS_COMPLETED,
                'published_at'          => Carbon::now()->subWeeks(3),
                'created_by'            => $secretary->id,
            ],

            // -------------------------------------------------------------------
            // 2. Ongoing / Current Week Event (1 Event)
            // -------------------------------------------------------------------
            [
                'title'                 => 'Hacktoberfest 2026: Open-Source Contribution Sprint',
                'description'           => 'Join the global celebration of open-source software! Contribute meaningful pull requests to ComSoc Hub, university open tools, and featured open-source repositories with senior student mentors.',
                'event_date'            => Carbon::now()->toDateString(),
                'start_time'            => '09:00:00',
                'end_time'              => '18:00:00',
                'venue'                 => 'CCIS Innovation Hub & Discord Voice Stages',
                'registration_deadline' => Carbon::now()->addDays(2),
                'participant_limit'     => 120,
                'status'                => Event::STATUS_ONGOING,
                'published_at'          => Carbon::now()->subWeeks(2),
                'created_by'            => $pio->id,
            ],

            // -------------------------------------------------------------------
            // 3. Upcoming / Future Events (3 Events)
            // -------------------------------------------------------------------
            [
                'title'                 => 'ComSoc Tech Summit 2026: Next-Gen AI & Web3 Horizons',
                'description'           => 'Our premier technology conference featuring keynote speakers from top tech startups and multinational IT enterprises. Topics include Large Language Models, Cloud-Native Architectures, and Decentralized Systems.',
                'event_date'            => Carbon::now()->addWeeks(2)->toDateString(),
                'start_time'            => '08:30:00',
                'end_time'              => '17:00:00',
                'venue'                 => 'UMak University Auditorium',
                'registration_deadline' => Carbon::now()->addWeeks(1)->addDays(4),
                'participant_limit'     => 250,
                'status'                => Event::STATUS_PUBLISHED,
                'published_at'          => Carbon::now()->subDays(5),
                'created_by'            => $president->id,
            ],
            [
                'title'                 => 'UI/UX Design Jam & Design System Workshop with Figma',
                'description'           => 'Learn user empathy, wireframing, interactive prototyping, and building responsive design tokens in Figma. Compete in a rapid 3-hour app redesign challenge for exciting prizes!',
                'event_date'            => Carbon::now()->addWeeks(4)->toDateString(),
                'start_time'            => '13:00:00',
                'end_time'              => '18:00:00',
                'venue'                 => 'Multimedia Laboratory, 6th Floor CCIS',
                'registration_deadline' => Carbon::now()->addWeeks(3)->addDays(5),
                'participant_limit'     => 45,
                'status'                => Event::STATUS_PUBLISHED,
                'published_at'          => Carbon::now()->subDays(3),
                'created_by'            => $vpInternal->id,
            ],
            [
                'title'                 => 'Cloud Computing & AWS Architecture 101',
                'description'           => 'An introductory workshop on Amazon Web Services (AWS). Learn about EC2 instances, S3 storage buckets, serverless Lambda functions, and deploying full-stack web applications to the cloud.',
                'event_date'            => Carbon::now()->addWeeks(6)->toDateString(),
                'start_time'            => '10:00:00',
                'end_time'              => '16:00:00',
                'venue'                 => 'Virtual via Microsoft Teams & CCIS Lab 2',
                'registration_deadline' => Carbon::now()->addWeeks(5)->addDays(3),
                'participant_limit'     => 100,
                'status'                => Event::STATUS_PUBLISHED,
                'published_at'          => Carbon::now()->subDays(1),
                'created_by'            => $vpExternal->id,
            ],
            [
                'title'                 => 'ComSoc Year-End Algorithmic Coding Challenge & Gala 2026',
                'description'           => 'Celebrate a fantastic year of technology and brotherhood with a competitive programming tournament followed by the ComSoc Annual Awards and Fellowship Dinner.',
                'event_date'            => Carbon::now()->addMonths(2)->toDateString(),
                'start_time'            => '14:00:00',
                'end_time'              => '21:00:00',
                'venue'                 => 'UMak Mini Theater & HPS Activity Center',
                'registration_deadline' => Carbon::now()->addMonths(1)->addWeeks(3),
                'participant_limit'     => 200,
                'status'                => Event::STATUS_APPROVED,
                'published_at'          => null,
                'created_by'            => $president->id,
            ],
        ];

        foreach ($eventsData as $data) {
            $event = Event::updateOrCreate(
                ['title' => $data['title']],
                $data
            );

            // Populate registrations, attendances, and certificates based on event status
            if ($students->isNotEmpty()) {
                if ($event->status === Event::STATUS_COMPLETED) {
                    $attendees = $students->take(min(18, $students->count()));
                    foreach ($attendees as $idx => $student) {
                        $reg = EventRegistration::updateOrCreate(
                            ['event_id' => $event->id, 'user_id' => $student->id],
                            [
                                'status'     => EventRegistration::STATUS_ATTENDED,
                                'qr_code'    => Str::random(32),
                                'created_at' => Carbon::parse($event->event_date)->subDays(rand(3, 10)),
                            ]
                        );

                        $att = EventAttendance::updateOrCreate(
                            ['event_id' => $event->id, 'user_id' => $student->id],
                            [
                                'registration_id'   => $reg->id,
                                'recorded_by'       => $secretary->id,
                                'checked_in_at'     => Carbon::parse($event->event_date)->setTime(rand(8, 9), rand(10, 55)),
                                'attendance_status' => rand(0, 5) === 0 ? EventAttendance::STATUS_LATE : EventAttendance::STATUS_PRESENT,
                                'notes'             => 'Verified entry at registration desk.',
                            ]
                        );

                        // Generate certificate for attended participants
                        $certNumber = sprintf('COMSOC-%s-%04d', Carbon::parse($event->event_date)->format('Y'), $event->id * 100 + $idx + 1);
                        Certificate::updateOrCreate(
                            ['user_id' => $student->id, 'event_id' => $event->id],
                            [
                                'attendance_id'      => $att->id,
                                'certificate_number' => $certNumber,
                                'status'             => 'issued',
                                'issued_by'          => $president->id,
                                'issued_at'          => Carbon::parse($event->event_date)->addDays(1),
                            ]
                        );
                    }
                } elseif (in_array($event->status, [Event::STATUS_PUBLISHED, Event::STATUS_ONGOING])) {
                    // Seed active registrations for upcoming/ongoing events
                    $registrants = $students->random(min(12, $students->count()));
                    foreach ($registrants as $student) {
                        EventRegistration::updateOrCreate(
                            ['event_id' => $event->id, 'user_id' => $student->id],
                            [
                                'status'     => EventRegistration::STATUS_REGISTERED,
                                'qr_code'    => Str::random(32),
                                'created_at' => Carbon::now()->subDays(rand(1, 4)),
                            ]
                        );
                    }
                }
            }
        }
    }
}
