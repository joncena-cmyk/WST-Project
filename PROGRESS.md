# COMSOC Hub — Development Progress

**Project:** COMSOC Hub: A Secure Student Organization Engagement Platform  
**Client:** Computer Society (COMSOC) · CCIS · University of Makati  
**Stack:** Laravel 11 · Blade · Tailwind CSS · Alpine.js · MySQL  
**Last Updated:** August 2026

---

## Scope Change Notice

The project specification was updated after Phase 5. Three modules were added or promoted to full planned scope:

| Module | Change |
|--------|--------|
| Module 11 — Election & Voting Management | Promoted from "future/do not implement" to a real planned module with full requirements |
| Module 12 — Partnership & Technical Collaboration Management | **New module added.** VP for External Affairs workflow, partner records, collaboration tracking, partnership documents |
| Module 13 — Announcements & Communication | **New module added.** Referenced in spec as a distinct communication module |

These are no longer placeholder or future-only phases. They are part of the planned working application and will be built in controlled phases after Phase 9.

---

## Overall Status

| Phase | Description | Status |
|-------|-------------|--------|
| Phase 0 | Project setup, authentication, UI theme | ✅ Complete |
| Phase 1 | Users, roles, programs, membership foundation | ✅ Complete |
| Phase 2 | COMSOC Community (posts, comments, reactions, moderation) | ✅ Complete |
| Phase 2 (revisit) | User management UI, membership management UI | ✅ Complete |
| Phase 3 | Events, event approval, event registration | ✅ Complete |
| Phase 4 | QR Attendance | ✅ Complete |
| Phase 5 | Certificates | ✅ Complete |
| Phase 6 | Committees & Task Management | ✅ Complete |
| Phase 7 | Documents & Approval workflow | ⬜ Not started |
| Phase 8 | Dashboards, Reports & Analytics | ⬜ Not started |
| Phase 9 | Security hardening, audit logging, scheduled jobs | ⬜ Not started |
| Phase 10 | Election & Voting Management | ⬜ Not started |
| Phase 11 | Partnership & Technical Collaboration Management | ⬜ Not started |
| Phase 12 | Announcements & Communication | ⬜ Not started |

---

## Phase 0 — Project Setup & UI Theme ✅

### Completed
- [x] Laravel 11 + Breeze scaffolding confirmed
- [x] MySQL database configured (`comsoc_hub`)
- [x] App name set to `COMSOC Hub`, timezone `Asia/Manila`
- [x] **COMSOC green color palette** added to `tailwind.config.js`
  - Forest green (`comsoc-50` → `comsoc-950`) matching official COMSOC logo
  - Gold accent palette (`gold-300` → `gold-700`) for logo trim
- [x] **Guest layout** redesigned — split-screen: branded dark panel (left) + form (right)
- [x] **Auth views** restyled with COMSOC branding:
  - `login.blade.php` — "Sign in" with forgot password inline
  - `register.blade.php` — full branded form
  - `forgot-password.blade.php`
  - `reset-password.blade.php`
  - `verify-email.blade.php` — envelope icon, centered layout
  - `confirm-password.blade.php` — lock icon
- [x] **Primary button** updated to `comsoc-600`
- [x] **Text input** focus ring updated to `comsoc-500`
- [x] **Dashboard layout** built with collapsible sidebar
- [x] **Sidebar** — brand, nav links, user footer with avatar + role
- [x] **Welcome/landing page** fully rebuilt:
  - Sticky scroll-aware navbar
  - Full-screen hero with dot grid, floating particles, gradient text
  - Stats strip (7 roles / 10+ modules / 4 programs)
  - 6-feature cards with numbered accents and hover lift
  - "How It Works" 4-step process with connector lines
  - **Officers section** — 7 executive officers + 4 program reps (dummy names, shimmer photo placeholders, conic-gradient rings)
  - Roles section with permission lists per role
  - CTA banner with radial glows
  - 3-column footer
  - **Scroll-reveal animations** (IntersectionObserver — no library)
  - Staggered entry delays per card
- [x] Assets compiled (`npm run build`)

### Key Files
- `tailwind.config.js`
- `resources/css/app.css`
- `resources/views/layouts/guest.blade.php`
- `resources/views/layouts/dashboard.blade.php`
- `resources/views/layouts/partials/sidebar.blade.php`
- `resources/views/auth/*.blade.php`
- `resources/views/welcome.blade.php`
- `resources/views/components/primary-button.blade.php`
- `resources/views/components/text-input.blade.php`

---

## Phase 1 — Users, Roles, Programs, Membership Foundation ✅

### Database Migrations
- [x] `create_programs_table` — name, code, short_name, is_active
- [x] `add_comsoc_fields_to_users_table` — student_id, program_id, role, profile_picture, account_status
- [x] `create_memberships_table` — user_id, program_id, academic_year, status, start_date, end_date, approved_by, approved_at

### Models
- [x] `User` model extended:
  - Role constants: `student`, `member`, `program_rep`, `officer`, `president`, `adviser`, `admin`
  - Account status constants: `active`, `suspended`, `inactive`
  - Role helpers: `hasRole()`, `hasAnyRole()`, `isAdmin()`, `isAdviser()`, `isOfficerOrAbove()`, `isMemberOrAbove()`, `isActive()`, `getRoleLabel()`
  - `getProfilePictureUrl()` — serves uploaded photo or UI Avatars fallback
- [x] `Program` model — relationships to users and memberships
- [x] `Membership` model — status constants, soft deletes, relationships

### Seeders
- [x] `ProgramSeeder` — 4 programs: BSCS, BSIT, DIT, BSIS
- [x] `AdminUserSeeder` — 2 seed accounts:
  - `admin@comsoc.edu.ph` / `Admin@COMSOC2025!` (System Administrator)
  - `adviser@comsoc.edu.ph` / `Adviser@COMSOC2025!` (Adviser)

### Authentication Updates
- [x] Register form — added Student ID + Program fields
- [x] `RegisteredUserController` — stores student_id, program_id, defaults role to `student`
- [x] `ProfileUpdateRequest` — validates student_id, program_id

### Profile
- [x] `ProfileController` — handles profile picture upload/delete, passes programs to view
- [x] Profile form — name, student ID, program dropdown, profile picture upload, role badge (read-only)
- [x] Storage symlink created (`php artisan storage:link`)

### Key Files
- `app/Models/User.php`
- `app/Models/Program.php`
- `app/Models/Membership.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Requests/ProfileUpdateRequest.php`
- `database/seeders/ProgramSeeder.php`
- `database/seeders/AdminUserSeeder.php`
- `resources/views/auth/register.blade.php`
- `resources/views/profile/partials/update-profile-information-form.blade.php`

---

## Phase 2 — COMSOC Community ✅

### Database Migrations
- [x] `create_posts_table` — body, type (community/official), event_id, is_pinned, is_hidden, soft deletes
- [x] `create_post_images_table` — post_id, path, order (up to 4 images per post)
- [x] `create_post_reactions_table` — post_id, user_id, type (unique per user per post)
- [x] `create_comments_table` — post_id, user_id, parent_id (replies), is_hidden, soft deletes
- [x] `create_reports_table` — polymorphic (posts/comments), reason, status, reviewed_by

### Models
- [x] `Post` — type constants, helpers: `isOfficial()`, `isLikedBy()`, `likesCount()`, `commentsCount()`
- [x] `PostImage` — URL accessor
- [x] `PostReaction` — simple like
- [x] `Comment` — self-referencing parent/replies, `isReply()`
- [x] `Report` — polymorphic morphTo, reason/status constants

### Policies
- [x] `PostPolicy` — `viewAny`, `create`, `createOfficial` (officers+), `update`, `delete`, `moderate`
- [x] `CommentPolicy` — `create`, `update`, `delete`, `moderate`
- [x] Registered in `AppServiceProvider`

### Controllers
- [x] `PostController` — index (paginated feed), store (with images), edit, update, destroy, `toggleLike` (JSON response)
- [x] `CommentController` — store (with parent_id for replies), update, destroy
- [x] `ReportController` — store with duplicate report prevention
- [x] `ModerationController` — pending reports list, resolve/dismiss with optional content hiding

### Views
- [x] `community/index.blade.php` — paginated feed, flash messages, empty state
- [x] `community/partials/create-post-form.blade.php` — expandable composer, image preview (Alpine.js), official post toggle for officers, CSRF protected
- [x] `community/partials/post-card.blade.php` — author avatar, time, program badge, images grid, like button (AJAX), comment toggle, actions dropdown, report form
- [x] `community/partials/comment.blade.php` — threaded replies with reply toggle
- [x] `community/edit.blade.php` — post edit form
- [x] `moderation/index.blade.php` — pending reports with resolve/dismiss form and hide-content checkbox

### JavaScript
- [x] `toggleLike()` in `app.js` — AJAX like with button state update (no page reload)

### Key Files
- `app/Models/Post.php`, `PostImage.php`, `PostReaction.php`, `Comment.php`, `Report.php`
- `app/Http/Controllers/PostController.php`
- `app/Http/Controllers/CommentController.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Controllers/ModerationController.php`
- `app/Policies/PostPolicy.php`, `CommentPolicy.php`
- `resources/views/community/`
- `resources/views/moderation/`
- `resources/js/app.js`

---

## Phase 2 (Revisit) — User Management & Membership UI ✅

### Controllers
- [x] `Admin\UserController` — list (search/filter by name/email/role/status/program), show, update (role, status, student_id, program_id). Admin-only via constructor middleware.
- [x] `Admin\MembershipController` — list (search/filter by status/year), create, store (duplicate prevention), edit, update (auto-approves on activate). Officers+ via constructor middleware.
- [x] `MembershipController` — member's own membership history view

### Views
- [x] `admin/users/index.blade.php` — stats strip, search/filter form, user table with role badges and status indicators
- [x] `admin/users/show.blade.php` — profile card, account settings form (role/status/student_id/program), membership history table
- [x] `admin/memberships/index.blade.php` — filter form, memberships table
- [x] `admin/memberships/create.blade.php` — user selector, academic year, program, status, dates, notes
- [x] `admin/memberships/edit.blade.php` — edit with member info card, approval info display
- [x] `membership/index.blade.php` — membership card (active membership displayed as a digital card), history list

### Sidebar Updates
- [x] Membership link activated (was "Soon")
- [x] Management section: Moderation, Memberships (officers+)
- [x] Administration section: Users (admin only)
- [x] Event Approvals link for adviser/admin

### Key Files
- `app/Http/Controllers/Admin/UserController.php`
- `app/Http/Controllers/Admin/MembershipController.php`
- `app/Http/Controllers/MembershipController.php`
- `resources/views/admin/users/`
- `resources/views/admin/memberships/`
- `resources/views/membership/index.blade.php`

---

## Phase 3 — Events, Event Approval, Event Registration ✅

### Database Migrations
- [x] `create_events_table` — title, description, event_date, start_time, end_time, venue, registration_deadline, participant_limit, image, status, published_at, cancelled_at, soft deletes
- [x] `create_event_approvals_table` — event_id, reviewed_by, action (approved/rejected/returned), remarks
- [x] `create_event_requirements_table` — event_id, description (multiple requirements per event)
- [x] `create_event_registrations_table` — event_id, user_id, status, qr_code (unique), cancelled_at. Unique constraint prevents duplicate registration.
- [x] FK added on `posts.event_id` → `events.id` (deferred from Phase 2)

### Models
- [x] `Event` — 9 status constants (draft/submitted/under_review/returned/approved/published/ongoing/completed/cancelled), status color map, helpers: `isPublic()`, `isRegistrationOpen()`, `isFull()`, `isRegisteredBy()`, `registeredCount()`, `getImageUrl()`
- [x] `EventApproval` — action, remarks, reviewer relationship
- [x] `EventRequirement` — description, belongs to event
- [x] `EventRegistration` — status constants, `generateQrCode()` static method (unique random token)

### Policy
- [x] `EventPolicy` — `viewAny`, `view`, `create`, `update`, `submit`, `publish`, `cancel`, `review` (adviser only), `viewParticipants`
- [x] Registered in `AppServiceProvider`

### Controllers
- [x] `EventController`:
  - `index()` — public browse (published/ongoing events)
  - `manage()` — officer's own events list (admin sees all)
  - `show()` — event detail with user registration status
  - `create()` / `store()` — validated form with image upload and requirements
  - `edit()` / `update()` — editable only in draft/returned status
  - `submit()` — officer submits for adviser review
  - `publish()` — officer publishes after approval
  - `cancel()` — cancel with reason
  - `participants()` — officer views registrant list
- [x] `EventApprovalController` — adviser queue, show event for review, process decision (approved/rejected/returned) + creates `EventApproval` record
- [x] `EventRegistrationController` — `store()` (validates open/capacity/duplicate), `cancel()` (own registration), `myEvents()` (user's active registrations)

### Views
- [x] `events/index.blade.php` — card list of public events with image, date, venue, slots, register link
- [x] `events/show.blade.php` — full event detail, registration action, officer actions, approval history
- [x] `events/create.blade.php` — full form with dynamic requirements (Alpine.js add/remove)
- [x] `events/edit.blade.php` — pre-filled edit form, shows adviser return remarks
- [x] `events/manage.blade.php` — officer's event table with status badges
- [x] `events/my-events.blade.php` — student's registered events with cancel
- [x] `events/participants.blade.php` — registered/attended/cancelled summary + full participant table
- [x] `events/approval/index.blade.php` — adviser pending queue + approval history table
- [x] `events/approval/show.blade.php` — full event review with approve/return/reject decision form (Alpine.js)

### Routes
- [x] Public event routes (browse, show, register, cancel registration, my-events)
- [x] Officer routes (manage, create, edit, submit, publish, cancel, participants)
- [x] Adviser approval routes (`/events-approval/`)

### Sidebar Updates
- [x] Events link activated (was "Soon")
- [x] Event Approvals link in Management section (adviser/admin only)

### Key Files
- `app/Models/Event.php`, `EventApproval.php`, `EventRequirement.php`, `EventRegistration.php`
- `app/Http/Controllers/EventController.php`
- `app/Http/Controllers/EventApprovalController.php`
- `app/Http/Controllers/EventRegistrationController.php`
- `app/Policies/EventPolicy.php`
- `resources/views/events/`
- `routes/web.php`

---

## Phase 4 — QR Attendance ✅

### Database Migrations
- [x] `create_event_attendance_table` — event_id, user_id, registration_id, recorded_by, checked_in_at, attendance_status (present/late), notes
  - Unique constraint on `(event_id, user_id)` prevents double check-in
  - Indexes on `(event_id, checked_in_at)` for performance

### Models
- [x] `EventAttendance` — status constants (`present`, `late`), belongs to `Event`, `User`, `EventRegistration`, and `User` (as `recordedBy`)
- [x] `Event` model updated — `attendance()` and `certificates()` `HasMany` relationships added

### Controller
- [x] `AttendanceController`:
  - `showPass(Event $event)` — student's QR pass page; requires active (non-cancelled) registration; shows already-checked-in notice if applicable
  - `scannerPage(Event $event)` — officer's camera scanner page; shows live attendance/registered counts; restricted to published/ongoing/completed events
  - `checkIn(Request, Event)` — **AJAX JSON endpoint**; validates QR token → finds registration → checks duplicates → determines present vs late (>15 min after start) → records `EventAttendance` → updates registration to `attended`; returns structured JSON with student info
  - `manualCheckIn(Request, Event)` — form-based token fallback (32-char exact match); same duplicate/cancellation guards; redirects with flash
  - `index(Event $event)` — full attendance list with present/absent/attended counts; officer+ only

### Views
- [x] `attendance/pass.blade.php` — student's scannable QR code pass with event details and already-checked-in state
- [x] `attendance/scanner.blade.php` — officer's camera QR scanner with live feedback panel and manual-entry fallback form
- [x] `attendance/index.blade.php` — attendance table with present/late/absent breakdown and recorded-by info

### Routes
- [x] `GET /attendance/pass/{event}` — student QR pass
- [x] `GET /attendance/scanner/{event}` — officer scanner page
- [x] `POST /attendance/checkin/{event}` — AJAX check-in endpoint
- [x] `POST /attendance/manual/{event}` — manual token check-in
- [x] `GET /attendance/list/{event}` — attendance list

### Key Files
- `app/Models/EventAttendance.php`
- `app/Http/Controllers/AttendanceController.php`
- `resources/views/attendance/pass.blade.php`
- `resources/views/attendance/scanner.blade.php`
- `resources/views/attendance/index.blade.php`

---

## Phase 5 — Certificates ✅

### Database Migrations
- [x] `create_certificates_table` — user_id, event_id, attendance_id (nullable FK), certificate_number (unique), status (issued/revoked), issued_by, issued_at, revoked_at, revocation_reason
  - Unique constraint on `(user_id, event_id)` prevents duplicate issuance
  - Index on `(event_id, status)`

### Models
- [x] `Certificate` — status constants (`issued`, `revoked`), belongs to `User`, `Event`, `EventAttendance`, and `User` (as `issuedBy`)
  - `generateNumber()` static method — format `COMSOC-YYYY-NNNN` (sequential per year)
  - `User` model updated with `certificates()` `HasMany` relationship

### Controller
- [x] `CertificateController`:
  - `index()` — student's own issued certificates (excludes revoked)
  - `show(Certificate)` — printable/viewable single certificate; owner or officer+ only; 404 on revoked
  - `manage(Event $event)` — officer view: all `EventAttendance` records for an event + already-issued user IDs; officer+ only
  - `issue(Request, Event)` — single issuance; validates attendance record exists, prevents duplicate; creates certificate with auto-generated number
  - `issueAll(Event $event)` — bulk issuance to all attendees not yet issued; returns count of newly issued
  - `revoke(Request, Certificate)` — soft-revoke with optional reason; officer+ only

### Views
- [x] `certificates/index.blade.php` — student's certificate list with event name, date, certificate number, and view link
- [x] `certificates/show.blade.php` — printable certificate with COMSOC branding, student info, event details, certificate number, and issued-by
- [x] `certificates/manage.blade.php` — officer's per-event management table: attendee list, issue/already-issued state, individual issue form, revoke option, bulk "Issue All" button

### Routes
- [x] `GET /certificates` — student's certificate list
- [x] `GET /certificates/event/{event}/manage` — officer management view
- [x] `POST /certificates/event/{event}/issue` — issue to one attendee
- [x] `POST /certificates/event/{event}/issue-all` — bulk issue
- [x] `POST /certificates/{certificate}/revoke` — revoke
- [x] `GET /certificates/{certificate}` — view/print certificate

### Key Files
- `app/Models/Certificate.php`
- `app/Http/Controllers/CertificateController.php`
- `resources/views/certificates/index.blade.php`
- `resources/views/certificates/show.blade.php`
- `resources/views/certificates/manage.blade.php`

---

## Phase 6 — Committees & Task Management ✅

### Database Migrations
- [x] `create_committees_table` — event_id, name, description, head_user_id (nullable FK), created_by, soft deletes
- [x] `create_committee_members_table` — committee_id, user_id, role_in_committee (`head`, `assistant`, `member`), unique on `(committee_id, user_id)`
- [x] `create_event_tasks_table` — event_id, committee_id (nullable FK), title, description, assigned_to (nullable FK), created_by, due_date, priority (`low`, `medium`, `high`, `urgent`), status (`pending`, `in_progress`, `completed`, `cancelled`), completed_at, notes, soft deletes

### Models & Policies
- [x] `Committee` — relationships to `Event`, `head` (User), `creator` (User), `members` (CommitteeMember), `users` (BelongsToMany), `tasks` (EventTask). Helper: `taskCompletionPercentage()`, `isHead()`, `hasMember()`
- [x] `CommitteeMember` — role constants (`head`, `assistant`, `member`), belongs to `Committee` & `User`
- [x] `EventTask` — priority & status constants, helpers: `isOverdue()`, `isCompleted()`, `getPriorityBadgeClasses()`, `getStatusBadgeClasses()`, `getStatusLabel()`
- [x] `Event` model updated with `committees()` and `tasks()` `HasMany` relationships
- [x] `User` model updated with `assignedTasks()`, `committeeMemberships()`, `headedCommittees()`, `committees()` relationships
- [x] `CommitteePolicy` & `EventTaskPolicy` registered for role-based authorization

### Controllers
- [x] `CommitteeController`:
  - `index(Event)` — event committees hub with task completion progress %, metrics, and creation modal
  - `store(Request, Event)` — create committee + auto-assign head in pivot
  - `show(Committee)` — single committee workspace with roster management and committee task board
  - `update(Request, Committee)` — update committee details & head
  - `destroy(Committee)` — delete committee
  - `addMember(Request, Committee)` — add member to roster with role
  - `removeMember(Committee, User)` — remove member from roster
- [x] `EventTaskController`:
  - `myTasks(Request)` — personal task tracker for authenticated user with status filters and overdue alert banner
  - `eventTasks(Request, Event)` — event-wide master task board with committee/priority/status/assignee filters
  - `store(Request, Event)` — create new event task
  - `update(Request, EventTask)` — update task details & notes
  - `updateStatus(Request, EventTask)` — rapid status transition (`pending` → `in_progress` → `completed`) with `completed_at` timestamps
  - `destroy(EventTask)` — delete task

### Views
- [x] `tasks/my-tasks.blade.php` — personal task dashboard with metric cards, status pills, overdue alerts, and 1-click status transitions
- [x] `committees/index.blade.php` — event committee hub with progress metrics, committee cards with task progress bars, and create modal
- [x] `committees/show.blade.php` — committee workspace with member roster management and committee-scoped task board
- [x] `tasks/event-tasks.blade.php` — master task board with multi-attribute filtering and task creation modal
- [x] `layouts/partials/sidebar.blade.php` — added `Tasks` navigation link to main menu
- [x] `events/show.blade.php` — added `Committees & Tasks` action link in event details

### Key Files
- `app/Models/Committee.php`, `CommitteeMember.php`, `EventTask.php`
- `app/Policies/CommitteePolicy.php`, `EventTaskPolicy.php`
- `app/Http/Controllers/CommitteeController.php`, `EventTaskController.php`
- `resources/views/tasks/`, `resources/views/committees/`
- `tests/Feature/CommitteeTest.php`, `EventTaskTest.php`

---

## Database Tables (Current)

| Table | Purpose |
|-------|---------|
| `users` | All users with roles, program, profile |
| `password_reset_tokens` | Password reset |
| `sessions` | Database sessions |
| `cache` / `cache_locks` | Laravel cache |
| `jobs` / `job_batches` / `failed_jobs` | Queue |
| `programs` | BSCS, BSIT, DIT, BSIS |
| `memberships` | Academic-year membership records |
| `posts` | Community and official posts |
| `post_images` | Images attached to posts |
| `post_reactions` | Likes on posts |
| `comments` | Comments and threaded replies |
| `reports` | User-submitted content reports |
| `events` | COMSOC events |
| `event_approvals` | Adviser approval history per event |
| `event_requirements` | Requirements per event |
| `event_registrations` | Student registrations with QR codes |
| `event_attendance` | Check-in records with present/late status |
| `certificates` | Issued/revoked certificates per attendee per event |
| `committees` | Event-scoped functional committees |
| `committee_members` | Committee member assignments and roles |
| `event_tasks` | Event and committee task tracking records |

---

## What's Next — Planned Phases

---

### Phase 7 — Documents & Approval workflow
Replaces physical/email document routing for reservation letters, proposals, budget forms, etc.

**Planned tables:** `documents`, `document_reviews`

- Officers upload documents with category and optional event association
- Statuses: `draft`, `submitted`, `under_review`, `returned`, `approved`, `rejected`
- Adviser reviews, approves, returns, or rejects with remarks
- Secure file storage — documents must NOT be served via public URLs
- Approval history per document
- Document categories: reservation letter, letter of intent, budget request, event proposal, event report, other

---

### Phase 8 — Dashboards, Reports & Analytics
Replaces the current static `dashboard.blade.php` with real role-specific views.

**Role dashboards planned:**

| Role | Key Content |
|------|-------------|
| Student | Upcoming events, registered events, membership status, attendance history, certificates |
| Member | Student view + active membership card |
| Program Rep | Program activity summary, communication tools |
| Officer | Event management summary, registrations, attendance, tasks, documents |
| VP External Affairs | Partnership pipeline, collaboration activities, partner contacts |
| President | Organization overview, officer activity, event/membership/report summary |
| Adviser | Pending event approvals, pending document reviews, approval history |
| Admin | User stats, role distribution, audit log viewer, system health |

**Planned reports:**
- Event participation (registrations vs attendance per event)
- Membership report (active/expired/pending by program and year)
- Attendance summary per event
- Certificate issuance report
- Task completion rate per event
- Community engagement metrics (posts, reactions, comments)

---

### Phase 9 — Security Hardening, Audit Logging & Scheduled Jobs

**Planned table:** `audit_logs`

**Audit events to log:**
- Login / Logout / Failed login
- Role changes (by whom, from what role, to what role)
- Account suspension / reactivation
- Event creation, submission, approval, rejection, cancellation
- Document submission, approval, rejection
- Attendance recording
- Certificate issuance / revocation
- Post moderation (hide, report resolution)
- User deletion
- Election creation, candidate approval, voting submission, result publication *(Phase 10)*
- Partnership creation, status changes, document actions *(Phase 11)*

**Scheduled jobs planned:**
- Auto-transition events: `published` → `ongoing` (at `event_date + start_time`), `ongoing` → `completed` (at `event_date + end_time`)
- Auto-expire memberships past `end_date`

**Email notifications planned (Mailables):**
- Event registration confirmation
- Event cancellation notice to registered users
- Adviser notified when event is submitted for review
- Officer notified when event is approved / returned / rejected
- Certificate issued notification

**Other hardening:**
- Rate limiting on auth routes, report submission, like toggle
- Full input sanitization audit
- File upload security review (type, size, storage path)
- Policy coverage audit — ensure every sensitive route is policy-gated

---

### Phase 10 — Election & Voting Management
*(Promoted from future-only to planned module per updated spec)*

Actual online voting requires additional security requirements and university approval before going live. Architecture will be built in this phase but activation is conditional.

**Planned tables:** `elections`, `election_positions`, `candidates`, `candidate_documents`, `voter_eligibilities`, `ballots`, `votes`, `election_results`

**Planned features:**
- Election creation with positions and election period
- Candidate filing — eligible members submit candidacy
- Candidate documents upload and officer/adviser review
- Candidate approval workflow
- Voter eligibility rules (active membership required, program-scoped where applicable)
- Ballot generation per eligible voter
- Voting interface with confirmation
- Vote integrity: one vote per eligible voter per position, immutable once cast
- Result tallying and publication by authorized officer/admin
- Audit trail for all election actions

**Audit events:** Election creation, candidate approval, election status changes, voting submission, result publication

---

### Phase 11 — Partnership & Technical Collaboration Management
*(New module added per updated spec — responsibility of VP for External Affairs)*

**Planned tables:** `partner_organizations`, `partnerships`, `partnership_documents`, `collaboration_activities`

**Partner Organization records:**
- Organization name, type, contact person, contact email/number
- Website / social media
- Assigned COMSOC representative (VP for External Affairs or officer)
- Partnership history view

**Partnership workflow:**
- Statuses: `proposed`, `under_review`, `approved`, `active`, `completed`, `archived`, `declined`
- Officers record: proposal, meeting notes, collaboration objectives, dates, outcomes
- Adviser can review and approve/decline partnerships where required
- Required documents associated per partnership

**Technical Collaborations:**
- Types: programming workshop, cybersecurity activity, hackathon, seminar, competition, software project, technical training, community activity
- Collaboration may be linked to a COMSOC event
- Assigned officer responsible per collaboration

**Partnership Documents:**
- Partnership proposals, letters of intent, agreements, invitations, meeting documents, activity plans, completion reports
- Same secure storage and access-control rules as Phase 7 documents

**Partnership History:**
- Full historical record of completed and archived partnerships for future officers

**Audit events:** Partnership creation, status changes, document actions

---

### Phase 12 — Announcements & Communication
*(New module added per updated spec)*

Scope to be defined. Likely builds on the existing community/official post system and adds:
- Targeted announcements (broadcast to specific roles or programs)
- Notification inbox per user
- In-app notification system (`notifications` table — Laravel's built-in `DatabaseNotification` or custom)
- Potentially email digest for important announcements

---

## Known Gaps / Technical Debt

- **Dashboard is static** — `dashboard.blade.php` contains no real data. Phase 8 addresses this.
- **No scheduled jobs** — event status transitions (`published` → `ongoing` → `completed`) and membership expiry require a Laravel scheduler. Currently manual.
- **No email notifications** — no Mailables defined. Registration confirmations, approval results, and moderation alerts are not sent. Phase 9 addresses this.
- **No API layer** — no `routes/api.php`. Application is session/web-only.
- **Certificates not linked from sidebar** — no Certificates nav link for students or officer shortcut to per-event certificate management.
- **Attendance not surfaced from event detail** — `events/show.blade.php` does not yet link to the QR pass or officer scanner. Needs wiring.
- **No audit logging** — sensitive actions (role changes, event approvals, moderation) are not logged. Phase 9.
- **Rate limiting minimal** — only default Laravel throttle on email verification. Phase 9.
- **VP for External Affairs role not yet distinct** — currently mapped to `officer` role. Phase 11 may require a dedicated role or scoped permissions for external affairs actions.

---

## Seed Accounts

| Email | Password | Role |
|-------|----------|------|
| `admin@umak.edu.ph` | `Admin@COMSOC2025!` | System Administrator |
| `adviser@umak.edu.ph` | `Adviser@COMSOC2025!` | Adviser |

---

## Technical Notes

- **RBAC** is implemented as a string `role` column on `users` with constants and helper methods. No external package.
- **Email domain restriction** — only `@umak.edu.ph` addresses can register or log in. Enforced server-side via `App\Rules\UmakEmail` applied to both `RegisteredUserController` and `LoginRequest`.
- **Policies** gate all sensitive actions. No business logic in Blade.
- **Soft deletes** on `users` (via Laravel default), `posts`, `comments`, `memberships`, `events`.
- **Profile pictures** stored in `storage/app/public/profile-pictures/`, served via symlink.
- **Event images** stored in `storage/app/public/events/`.
- **Community images** stored in `storage/app/public/community/posts/`.
- **QR codes** are random 32-character tokens stored in `event_registrations.qr_code`.
- **Late detection** — attendance marked `late` if check-in is more than 15 minutes after `event.start_time`.
- **Certificate numbers** follow the format `COMSOC-YYYY-NNNN` (year + sequential count).
- **Alpine.js** used for interactive UI (sidebar toggle, post composer, image preview, event forms, approval form).
- **Scroll animations** use native `IntersectionObserver` — no external library.
- `posts.event_id` FK to `events` was deferred and added in the Phase 3 migration.
- **No `routes/api.php`** — this is a pure web (session-based) application.
- **Documents (Phase 7) must use protected storage** — not `public` disk. Use `private` disk with controller-served downloads.
- **Voting integrity (Phase 10)** — votes must be immutable once cast. Consider soft-delete protection or DB-level constraints. University approval required before activating live voting.
