# 🕌 Smart Donation and Volunteer Engagement Platform for Mosque — Architectural Analysis

> Generated: 2026-05-07
> Updated At: 2026-08-06

---

## 1. PROJECT MAPPING

### 1.1 Primary Tech Stack

| Layer         | Technology                                           |
| ------------- | ---------------------------------------------------- | ----- |
| **Language**  | PHP 7.3+ / 8.x (composer.json: ^7.3                  | ^8.0) |
| **Framework** | Laravel 8.x (MVC)                                    |
| **Runtime**   | Laravel Artisan (CLI) / PHP-FPM                      |
| **Database**  | MySQL (default)                                      |
| **Frontend**  | Blade templates, Laravel Mix, Axios, Lodash, PostCSS |
| **Auth**      | Laravel Sanctum (API tokens) + Session-based auth    |
| **PDF Gen**   | barryvdh/laravel-dompdf                              |
| **HTTP**      | Guzzle HTTP Client                                   |
| **Testing**   | PHPUnit 9.x + Laravel Dusk (via Sail)                |

### 1.2 High-Level Directory Tree

```
mosque/
├── app/                       # Core application code
│   ├── Console/               # Artisan commands & kernel
│   ├── Enums/                 # PHP enums (e.g., Role)
│   ├── Exceptions/            # Exception handling
│   ├── Http/                  # Controllers, Middleware, FormRequests
│   ├── Models/                # Eloquent ORM models
│   ├── Notifications/         # Mail/database notification classes
│   ├── Observers/             # Model lifecycle hooks
│   ├── Policies/              # Authorization policies (Donation, Event, WithdrawalRequest)
│   ├── Providers/             # Service providers (App, Auth, Broadcast, Event, Route)
│   ├── Rules/                 # Custom validation rules (UniqueEventLocation)
│   ├── Services/              # Business logic service layer
│   └── Transports/            # Custom mail transports (ResendTransport)
├── bootstrap/                 # Laravel framework bootstrap
│   └── app.php                # App bootstrap logic
├── config/                    # App configuration (database, auth, roles, etc.)
├── database/                  # Migrations, seeders, factories
│   ├── factories/             # 10 model factories
│   ├── migrations/            # 47 migration files
│   └── seeders/               # Populate test/dev data
├── public/                    # Web server document root
│   └── index.php              # Single entry point
├── resources/                 # Views (Blade) & raw assets
│   ├── views/                 # Blade templates
│   └── js, sass, lang         # Compiled frontend sources
├── routes/                    # Route definitions
│   ├── web.php                # Web routes (main)
│   ├── api.php                # API routes (minimal)
│   ├── channels.php           # Broadcast channels
│   └── console.php            # Artisan console routes
├── storage/                   # Logs, cache, compiled views, uploads
├── tests/                     # PHPUnit test suites
├── vendor/                    # Composer dependencies (ignored)
├── artisan                    # CLI interface
├── composer.json              # PHP dependencies
├── package.json               # NPM frontend dependencies
└── webpack.mix.js             # Laravel Mix build config
```

---

## 2. ARCHITECTURAL DISCOVERY

### 2.1 Entry Point

```
public/index.php
    ↓
bootstrap/app.php           ← Creates Laravel Application instance
    ↓
Kernel (app/Http/Kernel.php) ← Global & route middleware stack
    ↓
Service Providers (app/Providers/)
    ├── AppServiceProvider       ← App-wide bindings
    ├── AuthServiceProvider      ← Policy registration
    ├── BroadcastServiceProvider ← Broadcast channels
    ├── EventServiceProvider     ← Event/Listener mapping
    └── RouteServiceProvider     ← Loads routes/web.php & routes/api.php
```

### 2.2 Request/Data Flow

```
Browser Request
    │
    ▼
public/index.php
    │
    ▼
HTTP Kernel
    │
    ├─ Middleware (EncryptCookies, VerifyCsrfToken, CheckRole, etc.)
    │
    ▼
Route (routes/web.php)
    │
    ▼
Controller (app/Http/Controllers/)
    │
    ├─ FormRequest (app/Http/Requests/) ← Validation
    │
    ▼
Service Layer (app/Services/)        ← Business Logic
    │                                  (GamificationService, ExportService, etc.)
    ▼
Model (app/Models/)                  ← Eloquent ORM
    │
    ▼
Database (MySQL)
    │
    ▼
Response (Blade View / Redirect)
```

**Flow Example — Event Join:**

1. User POSTs to `/events/{id}/join` (web.php line 48)
2. `CheckRole` middleware verifies user is authenticated
3. `VerifyCsrfToken` middleware validates CSRF token
4. `VolunteerController::joinEvent()` validates request
5. `EventVolunteer` pivot record created in DB
6. `EventVolunteerObserver` triggers gamification logic
7. User redirected with success message

### 2.3 Configuration Files & External Dependencies

| File                     | Purpose                                                       |
| ------------------------ | ------------------------------------------------------------- |
| `.env.example`           | Template for env vars (DB, mail, Redis, AWS, Pusher)          |
| `config/app.php`         | App name, timezone, locale, providers                         |
| `config/auth.php`        | Guard config (session), user provider                         |
| `config/database.php`    | MySQL connection, Redis config                                |
| `config/roles.php`       | RBAC role-permission mapping + special codes for registration |
| `config/sanctum.php`     | API token auth via Laravel Sanctum                            |
| `config/cors.php`        | CORS settings (fruitcake/laravel-cors)                        |
| `config/mail.php`        | Mail configuration (SMTP/Resend HTTP API)                     |
| `config/filesystems.php` | Local & S3 disk config                                        |
| `config/queue.php`       | Queue driver (sync by default)                                |
| `config/session.php`     | File-based session, 120min lifetime                           |
| `config/cache.php`       | File-based cache with Redis option                            |

**External Integrations:**

- **Resend** (HTTP API) — Production email delivery via `App\Transports\ResendTransport` (SwiftMailer-compatible custom transport)
- **Mailhog** / MailPit — Local dev email testing (SMTP)
- **AWS S3** for file storage _(configured but not actively used)_
- **Pusher** for WebSocket broadcasting _(not actively implemented)_
- **Redis** for cache/queue _(available but not actively used)_

---

## 3. MODULE & DEPENDENCY ANALYSIS

### 3.1 Core Modules & Relationships

```
┌─────────────────────────────────────────────────────────────────┐
│                      AUTH / USER MODULE                         │
│  User ──hasOne── VolunteerProfile ──hasMany── Referrals         │
│    │                                                            │
│    ├── Donation (financial giving module)                       │
│    ├── WithdrawalRequest (fund disbursement)                    │
│    ├── Event ◄── Pivot: event_volunteer ──► User               │
│    │                                                            │
│    └── GamificationModule                                       │
│          ├── MemberPoints (points summary)                       │
│          ├── Badge / BadgeEarning (achievements)                 │
│          ├── Reward / RewardRedemption (catalog + claims)        │
│          ├── PointTransaction (audit trail)                      │
│          └── TierMilestone (rank progression)                    │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Database Schema Summary (22 tables)

| Table                        | Key Fields                                                                                                                                                                                                    | Domain                    |
| ---------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------- |
| **users**                    | id, name, email, password, role, phone, age, address, avatar, referred_code, referred_by, referred_code_updated_at, hide_from_leaderboard, is_amil                                                            | Auth & Profiles           |
| **volunteer_profiles**       | user_id, skills, hobbies, languages, availability, certification                                                                                                                                              | Volunteer matching        |
| **donations**                | user_id, amount, category, type, fund_purpose, source, status, reference, receipt_number, verified_by, verified_at, donation_date, description, donor_name, donor_ic, donor_phone, donor_email, donor_address | Financial Giving          |
| **withdrawal_requests**      | requested_by, type, amount, purpose, fund_purpose, status, approved_by, approved_at, maker_checked_by, maker_checked_at, rejection_reason, rejected_by, rejected_at                                           | Fund Disbursement         |
| **withdrawal_documents**     | withdrawal_request_id, uploaded_by, file_path, file_name, file_size, mime_type, uploaded_at                                                                                                                   | Supporting Documents      |
| **zakat_akads**              | donation_id, reference, muzakki_name, muzakki_ic, amil_name, amil_user_id, akad_date, amount, notes                                                                                                           | Zakat Akad Contracts      |
| **events**                   | title, description, event_date, end_time, location, max_volunteers, required_skills/hobbies/languages, status, gamification_category                                                                          | Event Management          |
| **event_volunteer**          | event_id, user_id, status, joined_at, attendance_status, absence_reason, points_awarded, points_earned                                                                                                        | Attendance & Volunteering |
| **member_points**            | user_id, total_points, available_points, redeemed_points, current_streak, longest_streak, last_activity_date                                                                                                  | Points Tracking           |
| **badges**                   | code, name, name_my, description, description_my, icon_svg, tier, points_awarded                                                                                                                              | Achievement Catalog       |
| **badge_earnings**           | user_id, badge_id, earned_at, source_event_id                                                                                                                                                                 | Badge Awards              |
| **rewards**                  | code, name, name_my, description, category, points_cost, valid_from/until                                                                                                                                     | Reward Catalog            |
| **reward_redemptions**       | user_id, reward_id, used_for_event_id, points_spent, status, claim_code, fulfilled_by, fulfilled_at                                                                                                           | Redemption Fulfillment    |
| **point_transactions**       | user_id, type, points, balance_after, reason, breakdown, source_type, source_id, admin_id, admin_notes                                                                                                        | Audit Trail               |
| **tier_milestones**          | tier, min_points, name, name_my, benefits, benefits_my, icon_svg                                                                                                                                              | Tier System               |
| **fund_purposes**            | name, sort_order, is_active                                                                                                                                                                                   | Fund Purpose Config       |
| **settings**                 | key (PK), value                                                                                                                                                                                               | App Settings              |
| **receipt_number_sequences** | prefix, year, last_number                                                                                                                                                                                     | Receipt Numbering         |
| **password_resets**          | email, token                                                                                                                                                                                                  | Auth                      |
| **failed_jobs**              | uuid, connection, queue, payload                                                                                                                                                                              | Queue                     |
| **jobs**                     | queue, payload, attempts, reserved_at                                                                                                                                                                         | Queue Jobs                |
| **personal_access_tokens**   | tokenable_id, name, token, abilities                                                                                                                                                                          | Sanctum API Auth          |

### 3.3 Key Third-Party Libraries

| Package                         | Role                                                        |
| ------------------------------- | ----------------------------------------------------------- |
| **laravel/framework 8.x**       | Core MVC framework: routing, ORM, blade, auth, queues, etc. |
| **laravel/sanctum 2.x**         | API token authentication (SPA + mobile token support)       |
| **barryvdh/laravel-dompdf 2.x** | PDF generation for exportable financial/attendance reports  |
| **fruitcake/laravel-cors 2.x**  | CORS middleware for API requests                            |
| **guzzlehttp/guzzle 7.x**       | HTTP client (for external API calls)                        |
| **laravel/tinker 2.x**          | REPL interactive shell for debugging                        |
| **laravel/sail 1.x** (dev)      | Docker-based local dev environment                          |
| **fakerphp/faker** (dev)        | Fake data generation for seeding/testing                    |
| **phpunit/phpunit 9.x** (dev)   | Unit & feature testing framework                            |
| **facade/ignition 2.x** (dev)   | Pretty error pages with code context                        |
| **axios 0.21** (frontend)       | Promise-based HTTP client for AJAX requests                 |
| **laravel-mix 6.x** (frontend)  | Webpack wrapper for asset compilation                       |

---

## 4. SYSTEM PHILOSOPHY

### Architecture Pattern: **Monolithic MVC with Service-Oriented Business Logic**

This is a **monolithic Laravel MVC** application — a single deployable unit that handles all responsibilities (web UI, API, background jobs). Within this monolith, the codebase is well-structured with clear separation of concerns:

### Design Principles

1. **Fat Models, Thin Controllers, Extracted Services** — Controllers handle request/response and delegate business logic to dedicated **Service classes** (GamificationService, ExportService, CertificateService, RecommendationService, LeaderboardService, ReceiptNumberService, RegistrationCodeService). Models encapsulate relationships, scopes, and domain logic (e.g., `Event::canEdit()`, `Event::updateStatusBasedOnCapacity()`).

2. **Role-Based Access Control (RBAC)** — Three distinct roles (`admin`, `treasurer`, `member`) with granular permissions defined in `config/roles.php`. Middleware (`CheckRole`) enforces access, and a `hasPermission()` method enables fine-grained checks.

3. **Gamification-First Engagement** — A comprehensive points/badges/tiers/rewards system drives volunteer participation. The `GamificationService` orchestrates:
    - Points calculation (base + early join + streak bonus + high-impact category bonus)
    - Streak tracking (3/5/10 event streaks)
    - Badge progression (1, 5, 10, 25, 50 events + category-specific badges)
    - Tier milestones (points-based rank progression)
    - Reward redemption with claim codes + admin fulfillment
    - Referral bonuses
    - Full audit trail via `PointTransaction`

4. **Event-Driven Notifications** — The notification system uses Laravel's built-in notification channels (database by default). Key lifecycle events trigger notifications:
    - Points earned from event completion
    - Badges unlocked
    - Tier upgrades
    - Referral bonuses
    - Withdrawal request status changes

5. **Observer Pattern for Cross-Cutting Concerns** — `EventObserver` and `EventVolunteerObserver` hook into model lifecycle events to automate side effects (e.g., awarding points when attendance is marked "completed").

6. **Malay/Bilingual Support** — Badge names, descriptions, reward names, and tier benefits are stored in both English (`name`, `description`) and Malay (`name_my`, `description_my`), reflecting the likely Malaysian mosque context.

### Key Workflows

```
Donation Flow:      Admin creates donation → stored in DB → reflected in dashboard
Withdrawal Flow:    Treasurer requests → admin approves/rejects → funds disbursed
Event Flow:         Admin creates event → members join → attendance tracked → gamification triggers
Referral Flow:      Member generates code → new user registers with code → referrer gets bonus points
Reward Flow:        Member earns points → browses catalog → redeems → admin fulfills (or auto-fulfilled for certificates)
Priority Bypass:    Member redeems priority → joins full event → redemption consumed automatically
Reporting Flow:     Treasurer views reports → exports CSV or PDF (donations, events, attendance, financial)
```

### What This System Is NOT

- **Not microservices** — it's a single Laravel application
- **Not headless** — primarily server-rendered Blade templates with minimal AJAX
- **Not event-driven** (beyond internal Laravel events) — no message broker (RabbitMQ/Kafka) or async event sourcing
- **Not a multi-tenant architecture** — designed for a single mosque/community

### Target Use Case

A mosque management system serving a local Muslim community for:

- Tracking financial giving (Zakat, Waqf, Sadaqah) by Asnaf categories
- Managing community events and volunteer sign-ups
- Handling fund withdrawals with treasurer/approval workflow
- Gamifying volunteer engagement (points, badges, streaks, tiers)
- `absence_reason`, `points_awarded`, `points_earned`

#### Event Actions

- Create event: `EventController@store`
- Edit event: `EventController@edit`
- Update event: `EventController@update`
- Change status: `EventController@changeStatus`
- Delete event: `EventController@destroy`
- Manage volunteers: `EventController@volunteers`
- Remove volunteer: `EventController@removeVolunteer`
- Update attendance: `EventController@updateAttendance`
- Bulk approve/absent attendance: `bulkApproveAttendance`, `bulkMarkAbsent`

#### Recommendation Logic (Enhanced v2)

`RecommendationService::getRecommendations($user, $limit = null)` returns scored recommendations **never empty for members**:

**Criteria detection** (`userHasCriteria()`):

- Checks if `skills`, `hobbies`, `interests`, or `languages` in `VolunteerProfile` are non-null and non-empty.
- Returns `bool` used by controller for `$hasCriteria` flag → drives conditional UI rendering.

**Fallback for users without criteria** (`getFallbackEvents($user, $limit)`):

- Queries open, future events with available capacity (excludes already-joined events).
- MySQL `orderByRaw` CASE prioritizes events with NO strict requirements (NULL/empty JSON `required_skills`, `required_hobbies`, `required_languages`), then by `event_date` ASC.
- Returns: `Collection` of `['event' => Event, 'score' => 0, 'reasons' => [], 'hasCriteria' => false]`.

**Full matching for users with criteria** (`getScoredRecommendations($user, $profile, $limit)`):

- **Location**: +2 pts (partial match).
- **Skills**: +2 pts/match (max 6).
- **Languages**: +1 pt/match (max 3).
- **Hobbies**: +1 pt/match (max 3).
- **Interests**: +1 pt/match vs event hobbies (max 3).
- Returns: `Collection` of `['event' => Event, 'score' => int, 'reasons' => [], 'hasCriteria' => true]`, sorted by score DESC.

**Integration**:

- `DashboardController@index`: Returns `$recommendedEvents` (top 8), `$openEvents` (chronological fallback, limit 8), and `$hasCriteria` (bool). Blade renders "Recommended For You" section only when `$hasCriteria && $recommendedEvents->isNotEmpty()`, plus an always-visible "Open Community Events" section.
- Dashboard shows a dismissible Alpine.js banner prompting profile completion when `$hasCriteria === false`.

Helpers: `parseToArray()`, `locationMatches()`, `countMatches()`, `getEligibleEvents()`.

### Volunteer Profiles

- Model: `App\Models\VolunteerProfile`
- Controllers: `VolunteerController`, `ProfileController`
- Validation: `VolunteerProfileRequest`

#### Profile Fields

- `user_id` (unique FK)
- `skills`, `availability`, `hobbies`, `interests`, `languages` (JSON)
- `location`, `health_status`, `experience`, `long_term_availability`
- `experience_years`, `status`

#### Update Flow

- `VolunteerController@updateProfile` updates skills and availability.
- `ProfileController@updateSkills` updates expanded profile fields.

### Financial Transparency & Reports

- Controller: `App\Http\Controllers\ReportController`
- Service: `App\Services\ExportService`
- View: `resources/views/reports/index.blade.php`

#### Transparency

- `VolunteerController@transparency` shows donation totals for today, month, year.
- Approved withdrawal expenses are listed and summed.
- Date range filter validates input and limits queries.

#### Reports Dashboard (Tabbed Interface)

The reports page at `/reports` features a **tabbed interface** with 5 tabs:

| Tab         | Description                                             | Pagination                    |
| ----------- | ------------------------------------------------------- | ----------------------------- |
| Donations   | Individual donation records with sorting                | 20 per page                   |
| Events      | Event list with date, location, status, volunteer count | 20 per page                   |
| Attendance  | Volunteer attendance records per event                  | 20 per page (volunteer-level) |
| Financial   | Summary cards (Total Donations, Withdrawals, Balance)   | N/A (summary only)            |
| Withdrawals | Approved withdrawal requests                            | 20 per page                   |

**Key Features:**

- Month/Year filter at top (applies to all tabs)
- Quick Export dropdowns for each report type (CSV/PDF)
- Tab state stored in URL query: `?tab=donations|events|attendance|financial|withdrawals`
- Scroll-to-table on pagination (maintains user position)
- Default tab: `donations`

**Query Parameters:**

- `month` - Filter by month (1-12)
- `year` - Filter by year
- `tab` - Active tab selection
- `sort` - Sort field for donations table
- `direction` - Sort direction (asc/desc)
- `page` - Pagination (triggers scroll-to-table)

**Report Logic:**

- Donations filtered by `donation_date` (month/year)
- Events filtered by `event_date` (month/year)
- Attendance filtered by event `event_date` (month/year)
- Withdrawals filtered by `created_at` (month/year) and `status = approved`
- Balance = total donations − total approved withdrawals

#### Exports

- CSV/PDF exports for donations, events, attendance, and financial summaries.
- Export routes are restricted to `admin` and `treasurer`.
- Export buttons available in Quick Export section and per-tab dropdowns.

#### Certificate Generation

- Service: `App\Services\CertificateService`
- Generates PDF certificates for redeemed rewards via `barryvdh/laravel-dompdf`.
- `generateCertificate(User $user, RewardRedemption $redemption)` → stores to `public` disk under `certificates/`.
- `downloadCertificate(RewardRedemption $redemption)` → streams download; requires `status === 'claimed'`.
- Uses `gamification.certificate` Blade view for PDF layout.

---

### Variables & Constants

- CHUNK_SIZE: 1000
    - Location: app/Services/ExportService.php
    - Description: chunk size for chunked exports to avoid memory exhaustion
- CACHE_TTL_MINUTES: 60
    - Location: app/Services/LeaderboardService.php
    - Description: leaderboard cache TTL in minutes
- BASE_POINTS: 50
- EARLY_JOIN_BONUS: 10
- STREAK_3_BONUS: 25
- STREAK_5_BONUS: 50
- STREAK_10_BONUS: 100
- HIGH_IMPACT_BONUS: 20
- PROFILE_COMPLETION_BONUS: 20
- REFERRAL_BONUS: 15
- HIGH_IMPACT_CATEGORIES: ['religious', 'education', 'emergency']
    - Location: app/Services/GamificationService.php
    - Description: gamification constants for scoring and categories

## 5. API / Interface Documentation

### Web Routes

| Method | URI                                                    | Controller@Method                                    | Description                                                                      |
| ------ | ------------------------------------------------------ | ---------------------------------------------------- | -------------------------------------------------------------------------------- |
| GET    | `/`                                                    | LandingController@index                              | Landing page (redirects to /dashboard if authenticated)                          |
| GET    | `/dashboard`                                           | DashboardController@index                            | Authenticated dashboard                                                          |
| GET    | `/login`                                               | AuthController@showLoginForm                         | Login page                                                                       |
| POST   | `/login`                                               | AuthController@login                                 | Authenticate user                                                                |
| POST   | `/logout`                                              | AuthController@logout                                | Logout user                                                                      |
| GET    | `/register`                                            | AuthController@showRegisterForm                      | Registration page                                                                |
| POST   | `/register`                                            | AuthController@register                              | Create user account (triggers email verification notification)                   |
| GET    | `/password/reset`                                      | AuthController@showLinkRequestForm                   | Password reset request page                                                      |
| POST   | `/password/email`                                      | AuthController@sendResetLinkEmail                    | Send reset link email                                                            |
| GET    | `/password/reset/{token}`                              | AuthController@showResetForm                         | Password reset form                                                              |
| POST   | `/password/reset`                                      | AuthController@reset                                 | Process password reset                                                           |
| GET    | `/email/verify`                                        | AuthController@showVerifyNotice                      | Verification notice page                                                         |
| GET    | `/email/verify/{id}/{hash}`                            | AuthController@verifyEmail                           | Verify email (signed route, no auth middleware)                                  |
| POST   | `/email/verification-notification`                     | AuthController@resendVerification                    | Resend verification email                                                        |
| GET    | `/email/resend`                                        | AuthController@showResendForm                        | Public resend verification form                                                  |
| POST   | `/email/resend`                                        | AuthController@resendVerification                    | Public resend (throttled, non-enumeration)                                       |
| GET    | `/profile`                                             | ProfileController@index                              | View user profile                                                                |
| POST   | `/profile/update-info`                                 | ProfileController@updateInfo                         | Update basic profile info                                                        |
| POST   | `/profile/update-skills`                               | ProfileController@updateSkills                       | Update volunteer profile fields                                                  |
| POST   | `/profile/update-password`                             | ProfileController@updatePassword                     | Change password                                                                  |
| POST   | `/profile/referral/generate`                           | ProfileController@generateReferralCode               | Generate referral code (AJAX)                                                    |
| POST   | `/profile/update-avatar`                               | ProfileController@updateAvatar                       | Upload user avatar (AJAX)                                                        |
| DELETE | `/profile/delete-avatar`                               | ProfileController@deleteAvatar                       | Remove user avatar (AJAX)                                                        |
| GET    | `/transparency`                                        | VolunteerController@transparency                     | Donation and withdrawal transparency                                             |
| GET    | `/volunteer/my-events`                                 | VolunteerController@myEvents                         | Member event list                                                                |
| POST   | `/volunteer/profile/update`                            | VolunteerController@updateProfile                    | Update skills and availability                                                   |
| POST   | `/events/{id}/join`                                    | VolunteerController@joinEvent                        | Volunteer joins event                                                            |
| DELETE | `/events/{id}/leave`                                   | VolunteerController@leaveEvent                       | Leave a volunteer from event                                                     |
| GET    | `/notifications`                                       | NotificationController@index                         | View notifications                                                               |
| POST   | `/notifications/mark-all-read`                         | NotificationController@markAllRead                   | Mark all notifications read                                                      |
| POST   | `/notifications/{id}/mark-read`                        | NotificationController@markRead                      | Mark a notification read                                                         |
| GET    | `/donations`                                           | DonationController@index                             | List donations                                                                   |
| POST   | `/donations`                                           | DonationController@store                             | Record donation                                                                  |
| GET    | `/donations/batch`                                     | DonationController@batchForm                         | Batch entry form (Sadaqah only)                                                  |
| POST   | `/donations/batch`                                     | DonationController@batchStore                        | Process batch donation entries                                                   |
| GET    | `/donations/bulk`                                      | DonationController@bulkForm                          | Bulk donation entry form (single-amount multi-purpose)                           |
| POST   | `/donations/bulk`                                      | DonationController@bulkStore                         | Process bulk donation entries                                                    |
| GET    | `/donations/fund-purposes`                             | DonationController@fundPurposeIndex                  | List fund purposes (admin)                                                       |
| POST   | `/donations/fund-purposes`                             | DonationController@fundPurposeStore                  | Create fund purpose (admin)                                                      |
| PUT    | `/donations/fund-purposes/{fundPurpose}`               | DonationController@fundPurposeUpdate                 | Update fund purpose (admin)                                                      |
| DELETE | `/donations/fund-purposes/{fundPurpose}`               | DonationController@fundPurposeDestroy                | Delete fund purpose (admin)                                                      |
| PATCH  | `/donations/fund-purposes/{fundPurpose}/toggle-active` | DonationController@fundPurposeToggleActive           | Toggle fund purpose active status (admin)                                        |
| PATCH  | `/donations/{id}/confirm`                              | DonationController@confirm                           | Confirm a pending donation                                                       |
| PATCH  | `/donations/{id}/dispute`                              | DonationController@dispute                           | Mark a donation as disputed                                                      |
| GET    | `/donations/{id}/akad/print`                           | DonationController@printAkad                         | Download printable akad PDF slip                                                 |
| GET    | `/donations/{id}/receipt/print`                        | DonationController@printReceipt                      | Download printable donation receipt                                              |
| POST   | `/withdrawals`                                         | WithdrawalController@store                           | Create withdrawal request                                                        |
| POST   | `/withdrawals/{id}/documents`                          | WithdrawalController@uploadDocuments                 | Upload supporting documents                                                      |
| GET    | `/events/manage`                                       | EventController@index                                | Manage events                                                                    |
| POST   | `/events`                                              | EventController@store                                | Create event                                                                     |
| GET    | `/events/{id}/edit`                                    | EventController@edit                                 | Edit event form                                                                  |
| PUT    | `/events/{id}`                                         | EventController@update                               | Update event                                                                     |
| PATCH  | `/events/{id}/status`                                  | EventController@changeStatus                         | Change event status                                                              |
| DELETE | `/events/{id}`                                         | EventController@destroy                              | Delete event                                                                     |
| GET    | `/events/{id}/volunteers`                              | EventController@volunteers                           | Event volunteer list                                                             |
| DELETE | `/events/{eventId}/volunteers/{userId}`                | EventController@removeVolunteer                      | Remove volunteer from event                                                      |
| PATCH  | `/events/{eventId}/attendance/{userId}`                | EventController@updateAttendance                     | Update volunteer attendance                                                      |
| POST   | `/events/{eventId}/attendance/bulk-approve`            | EventController@bulkApproveAttendance                | Bulk approve attendance                                                          |
| POST   | `/events/{eventId}/attendance/bulk-absent`             | EventController@bulkMarkAbsent                       | Bulk mark volunteers absent                                                      |
| GET    | `/withdrawals`                                         | WithdrawalController@index                           | List withdrawal requests                                                         |
| GET    | `/reports`                                             | ReportController@index                               | Reports dashboard (supports ?tab=, ?month=, ?year=, ?sort=, ?direction=, ?page=) |
| GET    | `/reports/export/donations/csv`                        | ReportController@exportDonationsCSV                  | Export donations CSV                                                             |
| GET    | `/reports/export/donations/pdf`                        | ReportController@exportDonationsPDF                  | Export donations PDF                                                             |
| GET    | `/reports/export/events/csv`                           | ReportController@exportEventsCSV                     | Export events CSV                                                                |
| GET    | `/reports/export/events/pdf`                           | ReportController@exportEventsPDF                     | Export events PDF                                                                |
| GET    | `/reports/export/attendance/csv`                       | ReportController@exportAttendanceCSV                 | Export attendance CSV                                                            |
| GET    | `/reports/export/attendance/pdf`                       | ReportController@exportAttendancePDF                 | Export attendance PDF                                                            |
| GET    | `/reports/export/financial/csv`                        | ReportController@exportFinancialCSV                  | Export financial summary CSV                                                     |
| GET    | `/reports/export/financial/pdf`                        | ReportController@exportFinancialPDF                  | Export financial summary PDF                                                     |
| GET    | `/reports/export/gamification/csv`                     | ReportController@exportGamificationCSV               | Export gamification report CSV                                                   |
| GET    | `/reports/export/gamification/pdf`                     | ReportController@exportGamificationPDF               | Export gamification report PDF                                                   |
| POST   | `/withdrawals/{id}/approve`                            | WithdrawalController@approve                         | Approve withdrawal                                                               |
| POST   | `/withdrawals/{id}/reject`                             | WithdrawalController@reject                          | Reject withdrawal                                                                |
| GET    | `/gamification/dashboard`                              | GamificationController@dashboard                     | Gamification overview                                                            |
| GET    | `/gamification/points-history`                         | GamificationController@pointsHistory                 | Points history                                                                   |
| GET    | `/gamification/badges`                                 | GamificationController@badges                        | Badges catalog                                                                   |
| GET    | `/gamification/rewards`                                | GamificationController@rewards                       | Reward catalog                                                                   |
| POST   | `/gamification/rewards/{reward}/redeem`                | GamificationController@redeem                        | Redeem reward                                                                    |
| GET    | `/gamification/leaderboard`                            | GamificationController@leaderboard                   | Leaderboard views                                                                |
| GET    | `/gamification/my-redemptions`                         | GamificationController@myRedemptions                 | User redemptions                                                                 |
| GET    | `/gamification/certificate/{redemption}/download`      | GamificationController@downloadCertificate           | Download auto-generated certificate                                              |
| GET    | `/admin/gamification/`                                 | Admin\GamificationAdminController@index              | Admin gamification list                                                          |
| POST   | `/admin/gamification/members/{user}/adjust`            | Admin\GamificationAdminController@adjustPoints       | Adjust member points                                                             |
| GET    | `/admin/gamification/members/{user}/transactions`      | Admin\GamificationAdminController@viewTransactions   | Member transactions                                                              |
| GET    | `/admin/gamification/redemptions`                      | Admin\GamificationAdminController@pendingRedemptions | Pending redemptions                                                              |
| POST   | `/admin/gamification/redemptions/{redemption}/fulfill` | Admin\GamificationAdminController@fulfillRedemption  | Fulfill or reject reward                                                         |
| GET    | `/admin/gamification/badges`                           | Admin\GamificationAdminController@badgesIndex        | List all badges                                                                  |
| GET    | `/admin/gamification/badges/create`                    | Admin\GamificationAdminController@createBadge        | Show badge creation form                                                         |
| POST   | `/admin/gamification/badges`                           | Admin\GamificationAdminController@storeBadge         | Create new badge                                                                 |
| GET    | `/admin/gamification/badges/{badge}/edit`              | Admin\GamificationAdminController@editBadge          | Show badge edit form                                                             |
| PUT    | `/admin/gamification/badges/{badge}`                   | Admin\GamificationAdminController@updateBadge        | Update badge                                                                     |
| PATCH  | `/admin/gamification/badges/{badge}/toggle`            | Admin\GamificationAdminController@toggleBadge        | Toggle badge active status                                                       |
| DELETE | `/admin/gamification/badges/{badge}`                   | Admin\GamificationAdminController@destroyBadge       | Delete badge                                                                     |
| GET    | `/admin/gamification/rewards`                          | Admin\GamificationAdminController@rewardsIndex       | List all rewards                                                                 |
| GET    | `/admin/gamification/rewards/create`                   | Admin\GamificationAdminController@createReward       | Show reward creation form                                                        |
| POST   | `/admin/gamification/rewards`                          | Admin\GamificationAdminController@storeReward        | Create new reward                                                                |
| GET    | `/admin/gamification/rewards/{reward}/edit`            | Admin\GamificationAdminController@editReward         | Show reward edit form                                                            |
| PUT    | `/admin/gamification/rewards/{reward}`                 | Admin\GamificationAdminController@updateReward       | Update reward                                                                    |
| PATCH  | `/admin/gamification/rewards/{reward}/toggle`          | Admin\GamificationAdminController@toggleReward       | Toggle reward active status                                                      |
| DELETE | `/admin/gamification/rewards/{reward}`                 | Admin\GamificationAdminController@destroyReward      | Delete reward                                                                    |
| GET    | `/admin/gamification/tiers`                            | Admin\GamificationAdminController@tiersIndex         | List all tier milestones                                                         |
| GET    | `/admin/gamification/tiers/create`                     | Admin\GamificationAdminController@createTier         | Show tier creation form                                                          |
| POST   | `/admin/gamification/tiers`                            | Admin\GamificationAdminController@storeTier          | Create new tier milestone                                                        |
| GET    | `/admin/gamification/tiers/{tier}/edit`                | Admin\GamificationAdminController@editTier           | Show tier edit form                                                              |
| PUT    | `/admin/gamification/tiers/{tier}`                     | Admin\GamificationAdminController@updateTier         | Update tier milestone                                                            |
| DELETE | `/admin/gamification/tiers/{tier}`                     | Admin\GamificationAdminController@destroyTier        | Delete tier milestone                                                            |
| GET    | `/admin/settings`                                      | Admin\AdminSettingsController@index                  | Admin settings page                                                              |
| POST   | `/admin/settings/regenerate-admin`                     | Admin\AdminSettingsController@regenerateAdmin        | Regenerate admin registration code                                               |
| POST   | `/admin/settings/regenerate-treasurer`                 | Admin\AdminSettingsController@regenerateTreasurer    | Regenerate treasurer registration code                                           |
| GET    | `/admin/amils`                                         | Admin\AmilAdminController@index                      | Amil management page                                                             |
| POST   | `/admin/amils/{user}/toggle`                           | Admin\AmilAdminController@toggle                     | Toggle amil appointment status                                                   |

### API Routes (Sanctum)

| Method | URI         | Controller@Method | Description                       |
| ------ | ----------- | ----------------- | --------------------------------- |
| GET    | `/api/user` | Closure           | Return authenticated user details |

---

## 6. Database Schema

### `users`

| Column                   | Type                               | Constraints             | Notes                         |
| ------------------------ | ---------------------------------- | ----------------------- | ----------------------------- |
| id                       | bigint                             | PK, auto_increment      |                               |
| name                     | varchar(255)                       | NOT NULL                |                               |
| email                    | varchar(255)                       | UNIQUE, NOT NULL        |                               |
| email_verified_at        | timestamp                          | NULLABLE                |                               |
| password                 | varchar(255)                       | NOT NULL                |                               |
| role                     | enum('admin','treasurer','member') | DEFAULT 'member'        |                               |
| phone                    | varchar(255)                       | NULLABLE                |                               |
| age                      | integer                            | NULLABLE                |                               |
| address                  | varchar(255)                       | NULLABLE                |                               |
| avatar                   | varchar(255)                       | NULLABLE                | profile picture filename      |
| referred_code            | varchar(20)                        | UNIQUE, NULLABLE        | referral code                 |
| referred_by              | bigint                             | FK → users.id, NULLABLE | referral source               |
| hide_from_leaderboard    | boolean                            | DEFAULT false           | leaderboard privacy           |
| is_amil                  | boolean                            | DEFAULT false           | amil appointment status       |
| referred_code_updated_at | timestamp                          | NULLABLE                | last referral code generation |
| remember_token           | varchar(100)                       | NULLABLE                |                               |
| created_at               | timestamp                          |                         |                               |
| updated_at               | timestamp                          |                         |                               |

### `donations`

| Column         | Type                                        | Constraints             | Notes                                |
| -------------- | ------------------------------------------- | ----------------------- | ------------------------------------ |
| id             | bigint                                      | PK, auto_increment      |                                      |
| user_id        | bigint                                      | FK → users.id           | recorder (admin who input the data)  |
| amount         | decimal(10,2)                               | NOT NULL                |                                      |
| category       | enum('zakat','zakat_fitr','sadaqah','waqf') | NOT NULL                | Shariah classification               |
| type           | enum('obligatory','voluntary','endowment')  | DEFAULT 'voluntary'     | Auto-derived from category           |
| fund_purpose   | varchar(100)                                | NULLABLE                | e.g. "General Fund", "Kipas Gergasi" |
| asnaf_category | varchar(255)                                | NULLABLE                | (unused — asnaf tracking rejected)   |
| source         | enum('cash','online')                       | DEFAULT 'cash'          | payment source                       |
| status         | enum('pending','confirmed','disputed')      | DEFAULT 'pending'       | verification workflow                |
| reference      | varchar(100)                                | NULLABLE                | bank ref / WhatsApp ref              |
| verified_by    | bigint                                      | FK → users.id, NULLABLE | who verified                         |
| verified_at    | timestamp                                   | NULLABLE                | when verified                        |
| donation_date  | date                                        | NOT NULL                | when donation was received           |
| receipt_number | varchar(30)                                 | UNIQUE, NULLABLE        | auto-generated receipt number        |
| description    | text                                        | NULLABLE                | notes                                |
| donor_name     | varchar(255)                                | NULLABLE                | donor full name                      |
| donor_ic       | varchar(20)                                 | NULLABLE                | MyKad (format: XXXXXX-XX-XXXX)       |
| donor_phone    | varchar(15)                                 | NULLABLE                | Malaysian phone number               |
| donor_email    | varchar(255)                                | NULLABLE                | donor email                          |
| donor_address  | text                                        | NULLABLE                | donor address                        |
| created_at     | timestamp                                   |                         |                                      |
| updated_at     | timestamp                                   |                         |                                      |

### `withdrawal_requests`

| Column           | Type                                                  | Constraints             | Notes                                      |
| ---------------- | ----------------------------------------------------- | ----------------------- | ------------------------------------------ |
| id               | bigint                                                | PK, auto_increment      |                                            |
| requested_by     | bigint                                                | FK → users.id           | requester                                  |
| type             | enum('zakat','zakat_fitr','sadaqah','waqf')           | DEFAULT 'sadaqah'       | fund type                                  |
| amount           | decimal(10,2)                                         | NOT NULL                |                                            |
| purpose          | text                                                  | NOT NULL                | reason                                     |
| fund_purpose     | varchar(100)                                          | NOT NULL                | e.g. "General Fund", "Kipas Gergasi"       |
| status           | enum('pending','maker_checked','approved','rejected') | DEFAULT 'pending'       | workflow state                             |
| rejection_reason | text                                                  | NULLABLE                | rejection reason when status is 'rejected' |
| approved_by      | bigint                                                | FK → users.id, NULLABLE | approver                                   |
| approved_at      | timestamp                                             | NULLABLE                | approval datetime                          |
| maker_checked_by | bigint                                                | FK → users.id, NULLABLE | maker-checker first reviewer               |
| maker_checked_at | timestamp                                             | NULLABLE                | maker-checker review datetime              |
| rejected_by      | bigint                                                | FK → users.id, NULLABLE | person who rejected                        |
| rejected_at      | timestamp                                             | NULLABLE                | rejection datetime                         |
| created_at       | timestamp                                             |                         |                                            |
| updated_at       | timestamp                                             |                         |                                            |

### `withdrawal_documents`

| Column                | Type         | Constraints                          | Notes                           |
| --------------------- | ------------ | ------------------------------------ | ------------------------------- |
| id                    | bigint       | PK, auto_increment                   |                                 |
| withdrawal_request_id | bigint       | FK → withdrawal_requests.id, CASCADE | linked withdrawal               |
| uploaded_by           | bigint       | FK → users.id, CASCADE               | uploader                        |
| file_path             | varchar(500) | NOT NULL                             | stored path relative to storage |
| file_name             | varchar(255) | NOT NULL                             | original filename               |
| file_size             | integer      | NOT NULL                             | size in bytes                   |
| mime_type             | varchar(100) | NOT NULL                             | file MIME type                  |
| uploaded_at           | timestamp    | NOT NULL                             | upload time                     |
| created_at            | timestamp    |                                      |                                 |
| updated_at            | timestamp    |                                      |                                 |

### `volunteer_profiles`

| Column                 | Type                      | Constraints           | Notes                |
| ---------------------- | ------------------------- | --------------------- | -------------------- |
| id                     | bigint                    | PK, auto_increment    |                      |
| user_id                | bigint                    | FK → users.id, UNIQUE | one profile per user |
| skills                 | json                      | NULLABLE              | skill array          |
| availability           | json                      | NULLABLE              | availability flags   |
| hobbies                | json                      | NULLABLE              | hobbies array        |
| interests              | json                      | NULLABLE              | interests array      |
| languages              | json                      | NULLABLE              | languages array      |
| experience_years       | integer                   | DEFAULT 0             |                      |
| status                 | enum('active','inactive') | DEFAULT 'active'      | profile state        |
| experience             | text                      | NULLABLE              | free-text experience |
| location               | varchar(255)              | NULLABLE              | city/location        |
| health_status          | varchar(255)              | NULLABLE              | physical condition   |
| long_term_availability | text                      | NULLABLE              | availability notes   |
| created_at             | timestamp                 |                       |                      |
| updated_at             | timestamp                 |                       |                      |

### `events`

> **Note:** The `Event` model includes attendance review helpers (`needsReview()`, `hasReviewableAttendance()`, `pendingReviewCount`, `completedCount`, `absentCount`, `confirmedCount`), scopes (`scopeNeedsAttendanceReview()`, `scopeUpcoming()`, `scopeJoinable()`), and an `effective_status` computed property that reflects real-time status without DB writes. Past events are auto-closed via the `events:close-past` scheduled command (hourly), not via model hooks.

| Column                | Type         | Constraints        | Notes                 |
| --------------------- | ------------ | ------------------ | --------------------- |
| id                    | bigint       | PK, auto_increment |                       |
| title                 | varchar(255) | NOT NULL           |                       |
| description           | text         | NULLABLE           |                       |
| event_date            | datetime     | NOT NULL           |                       |
| location              | varchar(255) | NULLABLE           |                       |
| required_skills       | json         | NULLABLE           | skill requirements    |
| required_hobbies      | json         | NULLABLE           | hobby requirements    |
| required_languages    | json         | NULLABLE           | language requirements |
| event_location        | varchar(255) | NULLABLE           | event venue/location  |
| location_radius       | varchar(255) | NULLABLE           | default 'Any'         |
| health_requirement    | varchar(255) | NULLABLE           |                       |
| max_volunteers        | integer      | DEFAULT 10         | capacity              |
| status                | string       | DEFAULT 'open'     | open/closed/cancelled |
| gamification_category | string       | DEFAULT 'general'  | points category       |
| end_time              | datetime     | NULLABLE           | event end time        |
| created_at            | timestamp    |                    |                       |
| updated_at            | timestamp    |                    |                       |

### `event_volunteer`

| Column                    | Type                                                    | Constraints               | Notes                   |
| ------------------------- | ------------------------------------------------------- | ------------------------- | ----------------------- |
| id                        | bigint                                                  | PK, auto_increment        |                         |
| event_id                  | bigint                                                  | FK → events.id            |                         |
| user_id                   | bigint                                                  | FK → users.id             |                         |
| joined_at                 | timestamp                                               | DEFAULT current_timestamp | join time               |
| status                    | enum('confirmed','completed','absent')                  | DEFAULT 'confirmed'       | original status         |
| attendance_status         | enum('confirmed','pending_review','completed','absent') | DEFAULT 'confirmed'       | attendance workflow     |
| absence_reason            | text                                                    | NULLABLE                  | absence reason          |
| points_awarded            | boolean                                                 | DEFAULT false             | gamification tracker    |
| points_earned             | integer                                                 | DEFAULT 0                 | gamification points     |
| unique(event_id, user_id) |                                                         | UNIQUE                    | one join per event/user |

### `member_points`

| Column              | Type      | Constraints           | Notes                    |
| ------------------- | --------- | --------------------- | ------------------------ |
| id                  | bigint    | PK, auto_increment    |                          |
| user_id             | bigint    | FK → users.id, UNIQUE | one summary row per user |
| total_points        | integer   | DEFAULT 0             |                          |
| available_points    | integer   | DEFAULT 0             |                          |
| redeemed_points     | integer   | DEFAULT 0             |                          |
| current_streak      | integer   | DEFAULT 0             |                          |
| longest_streak      | integer   | DEFAULT 0             |                          |
| last_activity_date  | date      | NULLABLE              |                          |
| created_at          | timestamp |                       |                          |
| updated_at          | timestamp |                       |                          |
| index(total_points) |           | INDEX                 | leaderboard queries      |

### `badges`

| Column         | Type      | Constraints        | Notes                 |
| -------------- | --------- | ------------------ | --------------------- |
| id             | bigint    | PK, auto_increment |                       |
| code           | string    | UNIQUE             | badge identifier      |
| name           | string    | NOT NULL           |                       |
| name_my        | string    | NOT NULL           | localized name        |
| description    | text      | NOT NULL           |                       |
| description_my | text      | NOT NULL           | localized description |
| icon_svg       | text      | NULLABLE           | SVG/icon markup       |
| tier           | string    | NOT NULL           |                       |
| points_awarded | integer   | DEFAULT 0          |                       |
| is_active      | boolean   | DEFAULT true       |                       |
| created_at     | timestamp |                    |                       |
| updated_at     | timestamp |                    |                       |

### `badge_earnings`

| Column                    | Type      | Constraints        | Notes              |
| ------------------------- | --------- | ------------------ | ------------------ |
| id                        | bigint    | PK, auto_increment |                    |
| user_id                   | bigint    | FK → users.id      |                    |
| badge_id                  | bigint    | FK → badges.id     |                    |
| earned_at                 | timestamp | NOT NULL           |                    |
| source_event_id           | bigint    | NULLABLE           | event source       |
| created_at                | timestamp |                    |                    |
| updated_at                | timestamp |                    |                    |
| unique(user_id, badge_id) |           | UNIQUE             | one badge per user |

### `rewards`

| Column         | Type              | Constraints        | Notes                  |
| -------------- | ----------------- | ------------------ | ---------------------- |
| id             | bigint            | PK, auto_increment |                        |
| code           | string            | UNIQUE             | reward identifier      |
| name           | string            | NOT NULL           |                        |
| name_my        | string            | NOT NULL           | localized name         |
| description    | text              | NOT NULL           |                        |
| description_my | text              | NULLABLE           | localized description  |
| category       | string            | NOT NULL           |                        |
| image          | varchar(500)      | NULLABLE           | reward image file path |
| image_svg      | varchar(1000)     | NULLABLE           | SVG icon markup        |
| points_cost    | integer           | NOT NULL           |                        |
| stock_quantity | integer, unsigned | NULLABLE           | inventory tracking     |
| valid_from     | date              | NULLABLE           |                        |
| valid_until    | date              | NULLABLE           |
| is_active      | boolean           | DEFAULT true       |                        |
| created_at     | timestamp         |                    |                        |
| updated_at     | timestamp         |                    |                        |

### `point_transactions`

| Column                     | Type      | Constraints             | Notes                                     |
| -------------------------- | --------- | ----------------------- | ----------------------------------------- |
| id                         | bigint    | PK, auto_increment      |                                           |
| user_id                    | bigint    | FK → users.id           |                                           |
| type                       | string    | NOT NULL                | earned/redeemed/adjusted/revoked/refunded |
| points                     | integer   | NOT NULL                | signed amount                             |
| balance_after              | integer   | NOT NULL                |                                           |
| reason                     | string    | NOT NULL                |                                           |
| breakdown                  | json      | NULLABLE                | gamification point breakdown details      |
| source_type                | string    | NULLABLE                | event/reward/referral/admin               |
| source_id                  | bigint    | NULLABLE                | related record ID                         |
| admin_id                   | bigint    | FK → users.id, NULLABLE | admin actor                               |
| admin_notes                | text      | NULLABLE                |                                           |
| created_at                 | timestamp |                         |                                           |
| updated_at                 | timestamp |                         |                                           |
| index(user_id, created_at) |           | INDEX                   | query performance                         |
| index(type)                |           | INDEX                   | transaction filtering                     |

### `reward_redemptions`

| Column            | Type      | Constraints                                  | Notes                                                     |
| ----------------- | --------- | -------------------------------------------- | --------------------------------------------------------- |
| id                | bigint    | PK, auto_increment                           |                                                           |
| user_id           | bigint    | FK → users.id                                |                                                           |
| reward_id         | bigint    | FK → rewards.id                              |                                                           |
| used_for_event_id | bigint    | FK → events.id, NULLABLE, SET NULL on delete | Tracks which event consumed a Priority Event Registration |
| points_spent      | integer   | NOT NULL                                     |                                                           |
| status            | string    | DEFAULT 'pending'                            | pending/claimed/rejected                                  |
| redeemed_at       | timestamp | NOT NULL                                     |                                                           |
| claimed_at        | timestamp | NULLABLE                                     |                                                           |
| expires_at        | timestamp | NULLABLE                                     |                                                           |
| claim_code        | string    | NULLABLE                                     |                                                           |
| fulfillment_notes | text      | NULLABLE                                     |                                                           |
| fulfilled_by      | bigint    | FK → users.id, NULLABLE                      |                                                           |
| fulfilled_at      | timestamp | NULLABLE                                     |                                                           |
| created_at        | timestamp |                                              |                                                           |
| updated_at        | timestamp |                                              |                                                           |

### `zakat_akads`

| Column       | Type          | Constraints                | Notes                                  |
| ------------ | ------------- | -------------------------- | -------------------------------------- |
| id           | bigint        | PK, auto_increment         |                                        |
| donation_id  | bigint        | FK → donations.id, CASCADE | linked donation                        |
| reference    | varchar(30)   | UNIQUE, NULLABLE           | akad reference code (ZKT-YYYYMMDD-NNN) |
| muzakki_name | varchar(255)  | NOT NULL                   | donor name                             |
| muzakki_ic   | varchar(20)   | NULLABLE                   | donor MyKad                            |
| amil_name    | varchar(255)  | NOT NULL                   | amil who conducted akad                |
| amil_user_id | bigint        | FK → users.id, NULLABLE    | amil as system user                    |
| akad_date    | date          | NOT NULL                   | when akad was conducted                |
| amount       | decimal(10,2) | NOT NULL                   | zakat amount                           |
| notes        | text          | NULLABLE                   | akad notes                             |
| created_at   | timestamp     |                            |                                        |
| updated_at   | timestamp     |                            |                                        |

### `tier_milestones`

| Column      | Type      | Constraints        | Notes |
| ----------- | --------- | ------------------ | ----- |
| id          | bigint    | PK, auto_increment |       |
| tier        | string    | UNIQUE             |       |
| min_points  | integer   | NOT NULL           |       |
| name        | string    | NOT NULL           |       |
| name_my     | string    | NOT NULL           |       |
| benefits    | text      | NOT NULL           |       |
| benefits_my | text      | NOT NULL           |       |
| icon_svg    | text      | NULLABLE           |       |
| created_at  | timestamp |                    |       |
| updated_at  | timestamp |                    |       |

### `fund_purposes`

| Column     | Type         | Constraints        | Notes                  |
| ---------- | ------------ | ------------------ | ---------------------- |
| id         | bigint       | PK, auto_increment |                        |
| name       | varchar(255) | NOT NULL, UNIQUE   | fund purpose label     |
| sort_order | integer      | DEFAULT 0          | display ordering       |
| is_active  | boolean      | DEFAULT true       | active/inactive toggle |
| created_at | timestamp    |                    |                        |
| updated_at | timestamp    |                    |                        |

### `settings`

| Column     | Type         | Constraints | Notes                                                |
| ---------- | ------------ | ----------- | ---------------------------------------------------- |
| key        | varchar(255) | PK          | setting identifier (e.g. admin_code, treasurer_code) |
| value      | text         | NULLABLE    | setting value                                        |
| created_at | timestamp    |             |                                                      |
| updated_at | timestamp    |             |                                                      |

### `receipt_number_sequences`

| Column               | Type             | Constraints        | Notes                        |
| -------------------- | ---------------- | ------------------ | ---------------------------- |
| id                   | bigint           | PK, auto_increment |                              |
| prefix               | varchar(10)      | NOT NULL           | receipt prefix               |
| year                 | char(4)          | NOT NULL           | calendar year                |
| last_number          | unsigned integer | DEFAULT 0          | current sequence counter     |
| created_at           | timestamp        |                    |                              |
| updated_at           | timestamp        |                    |                              |
| unique(prefix, year) |                  | UNIQUE             | one sequence per prefix-year |

### Standard Laravel Tables

| Table                    | Notes                        |
| ------------------------ | ---------------------------- |
| `notifications`          | Laravel notification storage |
| `password_resets`        | Password reset tokens        |
| `failed_jobs`            | Queue failure logging        |
| `jobs`                   | Queue jobs table             |
| `personal_access_tokens` | Sanctum API token storage    |

### Pivot & Indexed Columns

- `event_volunteer` is the pivot table linking `events` and `users`.
- `event_volunteer` has unique constraint on `(event_id, user_id)`.
- `users.email` is unique.
- `users.referred_code` is unique.
- `member_points.user_id` is unique and indexed.
- `point_transactions` indexes `user_id, created_at` and `type`.
- `withdrawal_documents` has FK cascade delete on `withdrawal_request_id` and `uploaded_by`.

---

## 7. Setup & Installation

### Prerequisites

- PHP 8.3 or newer
- Composer
- MySQL or compatible relational database
- Node.js + npm for Laravel Mix asset compilation
- Web server or `php artisan serve`

### Install Steps

```bash
cd c:/laragon baru/www/mosque
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` with database credentials and other environment settings, then run:

```bash
php artisan migrate
```

Optional asset preparation:

```bash
npm install
npm run dev
```

Run locally:

```bash
php artisan serve
```

### Default Test Accounts

- No seeded default user accounts exist in the repository.
- Create roles through registration using special codes (generated via `php artisan codes:generate`):
    - **Admin code** — set `ADMIN_CODE` in `.env` (run `php artisan codes:generate` to generate)
    - **Treasurer code** — set `TREASURER_CODE` in `.env`
- No hardcoded defaults — if codes are not set, no one can register as admin/treasurer.
- Standard registration creates a `member` account.
- **Referral Codes**: Members can generate unique referral codes from their Profile page. Share with friends to earn 15 bonus points when they register!
- **Bulk Generation**: Run `php artisan referral:generate-all` to create codes for all existing members.

### Essential `.env` Variables

| Variable                   | Purpose                                                                                   |
| -------------------------- | ----------------------------------------------------------------------------------------- |
| `APP_NAME`                 | Application name                                                                          |
| `APP_ENV`                  | Environment type                                                                          |
| `APP_KEY`                  | Laravel app key                                                                           |
| `APP_URL`                  | Base URL                                                                                  |
| `DB_CONNECTION`            | Database driver                                                                           |
| `DB_HOST`                  | Database host                                                                             |
| `DB_PORT`                  | Database port                                                                             |
| `DB_DATABASE`              | Database name                                                                             |
| `DB_USERNAME`              | Database username                                                                         |
| `DB_PASSWORD`              | Database password                                                                         |
| `ADMIN_CODE`               | Admin registration code (generate via `php artisan codes:generate`; empty = disabled)     |
| `TREASURER_CODE`           | Treasurer registration code (generate via `php artisan codes:generate`; empty = disabled) |
| `MAIL_MAILER`              | Mail transport (`smtp` for local/Mailhog, `resend` for production via Resend HTTP API)    |
| `MAIL_FROM_ADDRESS`        | Sender email address (`onboarding@resend.dev` for Resend test mode)                       |
| `MAIL_FROM_NAME`           | Sender display name                                                                       |
| `RESEND_API_KEY`           | Resend API key (required when `MAIL_MAILER=resend`)                                       |
| `SANCTUM_STATEFUL_DOMAINS` | Sanctum stateful domains for API auth                                                     |

---

## 8. Known Limitations & TODOs

- [x] Add login throttling and rate limiting for authentication flows.
- [x] Implement email verification flow (MustVerifyEmail interface, signed verification URLs, public resend form, non-enumeration error handling, Malay notification language).
- [x] Remove hardcoded default registration codes (ADMIN123/TREASURER123). Codes now generated via `php artisan codes:generate` with no default fallbacks.
- [x] Add `rejection_reason` to `WithdrawalRequest::$fillable` or normalize rejection storage.
- [x] Implement rejection reason modal input (treasurer enters reason before rejecting withdrawal request).
- [x] Fix `UpdateEventRequest::withValidator()` route parameter lookup; it uses `route('event')` while route is declared as `{id}`.
- [x] Remove side effects in `Event::booted()`; reading an event updates its status. (Replaced with `effective_status` accessor + scheduled `events:close-past` command running hourly)
- [ ] Use `approved_at` for withdrawal/financial reporting instead of `created_at` where appropriate.
- [ ] Add explicit policy checks for donations and withdrawals beyond role middleware.
- [ ] Consolidate volunteer profile update handling: profile and skill updates are split across two controllers.
- [ ] Seed sample users and data for test/QA environments.

**Recently Completed:**

- [x] Implement referral code generation and processing system (member-to-member referrals, 15-point bonus, monthly regeneration limit)
- [x] Implement tabbed Reports interface (Donations, Events, Attendance, Financial, Withdrawals) with pagination for each tab and scroll-to-table functionality
- [x] Add Monthly/Yearly report toggle in Reports page
- [x] Add sort functionality to Events, Attendance, and Withdrawals tables in Reports
- [x] Update treasurer navigation to include Reports access
- [x] Implement chunked CSV export for large datasets to prevent memory exhaustion
- [x] Add avatar upload/delete for user profiles (ProfileController, avatar migration, GenerateUserAvatars seeder)
- [x] Add gamification admin CRUD for badges, rewards, and tier milestones
- [x] Add gamification report export (CSV/PDF) for admin/treasurer
- [x] Add image fields and stock quantity tracking to rewards
- [x] Add donor info fields (donor_name, donor_ic, donor_phone, email, address) on donations
- [x] Split categories into Shariah Type + Fund Purpose (replaced flat dropdown)
- [x] Implement verification workflow (pending → confirm/dispute) for donation reconciliation
- [x] Add Akad & Amil recording for zakat donations with printable PDF slips
- [x] Add Batch Entry mode (multi-row Sadaqah-only quick entry for box/event collections)
- [x] Split all grand totals into 4 Shariah types: Zakat, Zakat Fitr, Sadaqah, Waqf (inflow + outflow)
- [x] Add withdrawal balance validation (server-side check preventing over-withdrawal)
- [x] Reorganize transparency & reports: simplified transparency page, enhanced reports with chart + category breakdown
- [x] Add Fund Purpose Management CRUD (database-backed purposes, admin add/edit/delete)
- [x] Replace all native browser dialogs with custom modal system
- [x] Add back button system across all 24 pages
- [x] Mobile responsiveness overhaul: 13 tables with mobile card views, responsive spacing, button fixes
- [x] Dynamic fund distribution progress bars on landing page (per-category, real data)
- [x] Restructure financial summary: removed combined totals, added per-category In/Out/Net
- [x] Replace reward catalog with mosque-appropriate items (14 rewards across 5 categories)
- [x] Implement auto-fulfillment for Certificate of Appreciation (PDF generation on redemption)
- [x] Implement Priority Event Registration capacity bypass (one-time use per redemption)
- [x] Add reward deletion protection (blocks deletion if redemptions exist)
- [x] Add `used_for_event_id` column to reward_redemptions for tracking priority consumption
- [x] Implement email verification (MustVerifyEmail, signed URLs, public resend, Malay language, non-enumeration)
- [x] Implement custom Resend HTTP API transport for Railway deployment (SMTP ports blocked by Railway)
- [x] Add withdrawal fund_purpose field (required, with balance validation at fund purpose level)
- [x] Implement 3-layer race condition fix for withdrawal approvals (transaction + lockForUpdate + pending balance blocking)
- [x] Add supporting document upload system for withdrawals (optional, admin invoices + treasurer proofs)
- [x] Add per-fund-purpose cash flow breakdown to financial reports
- [x] Add Railway deployment: custom Dockerfile, stderr logging, persistence-less filesystem workarounds
- [x] Fix XSS in gamification SVG rendering (AG-1/PG-1) — Created `@safe_svg()` Blade directive that strips `<script>`, event handlers, `<foreignObject>`, and dangerous elements from SVG markup
- [x] Add `is_amil` to `User::$fillable` — was missing, causing amil toggle operations to silently fail
- [x] Fix admin fund purposes (AD-4) — Replaced delete with activate/deactivate toggle (`is_active`)
- [x] Fix registration code display (AD-1) — Added Alpine.js toggle to mask codes by default
- [x] Fix amil management (AD-2) — Added `showConfirmModal` for appoint/revoke, "Not appointed" text for non-amils, removed inline notification banner
- [x] Fix settings page confirm() calls (AD-3) — Replaced `return confirm()` with `showConfirmModal`
- [x] Fix admin gamification hardcoded URL (AG-3) — Uses `route('admin.gamification.adjust', ...)` via `data-route`
- [x] Fix tier delete message (AG-4) — Explains points/badges preserved, perks lost, moved to next tier
- [x] Centralize event categories (PG-2) — Created `Event::CATEGORIES` constant, referenced in forms, validation, factories, seeders
- [x] Centralize reward categories & emojis (PG-3) — Created `Reward::CATEGORIES` and `Reward::CATEGORY_EMOJI_MAP` constants
- [x] Fix landing page CTAs (LP-1) — CTAs now link to target routes instead of forcing registration for unauthenticated visitors
- [x] Fix landing page trust bar empty state (LP-2) — Shows realistic sample data (RM 42,257 Zakat, 700 volunteers, etc.) when DB is empty; both trust bar and fund distribution use consistent fallback values

---

## 9. Recent Feature Updates

### 9.1 Monthly/Yearly Report Toggle

The Reports page at `/reports` now supports both **Monthly** and **Yearly** report modes.

#### Features:

- **Toggle Switch**: Radio button selector for Monthly/Yearly mode
- **Dynamic Form**: When Monthly selected → shows Month and Year dropdowns; When Yearly selected → shows only Year dropdown
- **Auto-submit**: Form automatically submits when toggling between Monthly/Yearly

#### Query Parameters:

- `report_type` - Either 'monthly' (default) or 'yearly'
- `month` - Month filter (1-12, only used when report_type = 'monthly')
- `year` - Year filter (required)

#### Controller Logic:

In `ReportController@index`, queries are conditionally built based on `$reportType`:

```php
// Monthly: filter by specific month + year
$donations = Donation::whereMonth("donation_date", $month)
    ->whereYear("donation_date", $year)
    ->get();

// Yearly: filter by year only (all months)
$donations = Donation::whereYear("donation_date", $year)->get();
```

#### Files Modified:

- `app/Http/Controllers/ReportController.php` - Added `$reportType` input handling and conditional queries
- `resources/views/reports/index.blade.php` - Added toggle UI and conditional form fields

---

### 9.2 Table Sorting in Reports

The Reports page includes sortable column headers for better data exploration.

#### Sortable Tables:

| Table       | Sortable Columns                                                  |
| ----------- | ----------------------------------------------------------------- |
| Donations   | donation_date, category, source, created_at (Recorded By), amount |
| Events      | event_date, title, status, event_location                         |
| Attendance  | event_date, event_title, volunteer_name, email, attendance_status |
| Withdrawals | created_at, purpose, amount, requested_by                         |

#### Implementation:

**Controller** (`ReportController.php`):

- Separate sort parameters per section: `sortDonation`, `sortEvent`, `sortAttendance`, `sortWithdrawal`
- Direction parameters: `directionDonation`, `directionEvent`, `directionAttendance`, `directionWithdrawal`
- Whitelist validation for allowed sort columns (security)

**View** (`reports/index.blade.php`):

- Sortable headers use `<a href="{{ request()->fullUrlWithQuery(...) }}">` pattern
- Direction toggles: `asc` → `desc` on same column click
- Arrow icon indicates current sort direction (`rotate-180` class for descending)

#### Default Sort Order:

| Table       | Default Column | Default Direction |
| ----------- | -------------- | ----------------- |
| Donations   | donation_date  | desc              |
| Events      | event_date     | desc              |
| Attendance  | event_date     | desc              |
| Withdrawals | created_at     | desc              |

---

### 9.3 Treasurer Reports Access

Treasurers now have access to the Reports page with the same functionality as admins.

#### Navigation Updates:

- Added "Reports" link to treasurer navigation bar (blue button style, same as admin)
- Treasurer sees both "Requests" and "Reports" links in navbar
- Routes already supported treasurer access via middleware: `role:admin,treasurer`

#### Files Modified:

- `resources/views/layouts/app.blade.php` - Added Reports link for treasurer role

---

### 9.4 Chunked CSV Export for Large Datasets

To handle yearly reports with large datasets without hitting PHP memory limits, exports now use **chunked processing**.

#### Implementation:

**ExportService.php** uses Laravel's `chunkById()` method:

- Processes records in chunks of 1000 (configurable via `CHUNK_SIZE` constant)
- Streams directly to response buffer (no memory accumulation)
- Uses `fopen('php://output', 'w')` for efficient writing

```php
private const CHUNK_SIZE = 1000;

private function generateCSVChunked($query, string $filename, ?string $period, callable $transformCallback)
{
    $callback = function () use ($query, $period, $transformCallback) {
        $query->chunkById(self::CHUNK_SIZE, function ($records) use ($handle, &$firstRow, ...){
            // Write headers on first chunk
            // Write data for each record
        });
    };
    return response()->stream($callback, 200, $headers);
}
```

#### Exported Methods:

- `generateDonationsReport()` - Uses chunked CSV
- `generateEventsReport()` - Uses chunked CSV
- `generateAttendanceReport()` - Uses chunked CSV
- Financial summary remains small, no chunking needed

#### Benefits:

| Before                              | After                    |
| ----------------------------------- | ------------------------ |
| Loads ALL records into memory       | Processes 1000 at a time |
| Memory limit exceeded on large data | Works with any data size |
| Timeout on large exports            | Fast streaming response  |

---

### 9.5 Avatar & Image Support

User profiles now support avatar images, and rewards have been enhanced with image support and stock tracking.

#### Features:

- **Avatar Upload/Delete**: Users can upload a profile avatar (JPEG, PNG, GIF, WebP, max 2MB) via their Profile page. Avatars are stored in `storage/app/public/avatars/` and served via `storage/avatars/` symlink.
- **Initials Fallback**: When no avatar is set, the system displays user initials generated from their name (max 2 characters).
- **Reward Images**: Rewards can have uploaded images or raw SVG markup. Images stored in `storage/app/public/rewards/`.
- **Stock Tracking**: Rewards now support optional `stock_quantity` for inventory management. `isAvailable()` checks stock levels before allowing redemption.

#### Implementation:

- `ProfileController@updateAvatar` handles upload via AJAX, returns new avatar URL and initials.
- `ProfileController@deleteAvatar` removes avatar file and clears the database field.
- `User` model has `getAvatarUrlAttribute()` and `getInitialsAttribute()` accessors.
- `Reward` model has `getImageUrlAttribute()`, `getIsRawSvgAttribute()`, and `isAvailable()` methods.
- `GenerateUserAvatars` seeder creates 128x128 unique-colored placeholder avatars for existing users.

#### Files Modified:

- `app/Http/Controllers/ProfileController.php` - Added `updateAvatar()`, `deleteAvatar()` methods
- `app/Models/User.php` - Added `avatar` fillable, avatar URL and initials accessors
- `app/Models/Reward.php` - Added image fields, stock quantity, availability checks
- `database/migrations/2026_05_09_000001_add_image_fields_to_rewards_table.php` - New migration
- `database/migrations/2026_05_09_000002_add_avatar_to_users_table.php` - New migration
- `database/seeders/GenerateUserAvatars.php` - New seeder
- `routes/web.php` - Added avatar upload/delete routes

---

### 9.6 Gamification Admin CRUD

Admins can now manage badges, rewards, and tier milestones through full CRUD interfaces in the admin panel.

#### Features:

- **Badge Management**: Index list with sortable columns (code, name, tier, points_awarded, is_active, created_at), create/edit form with icon upload, toggle active status, delete with storage cleanup.
- **Reward Management**: Index list with sortable columns (code, name, category, points_cost, stock_quantity, is_active, valid_until, created_at), create/edit form with image upload (handles both standard file upload and stream-based temp file workaround), toggle active status, delete cleanup.
- **Tier Milestone Management**: Index list ordered by min_points, create/edit form, delete. Tiers define rank progression threshold.

#### Route Group (`/admin/gamification`):

All routes are protected by `auth` and `role:admin` middleware.

#### Files Modified:

- `app/Http/Controllers/Admin/GamificationAdminController.php` - Added full badges/rewards/tiers CRUD methods
- `app/Http/Requests/BadgeRequest.php` - Badge form validation
- `app/Http/Requests/RewardRequest.php` - Reward form validation (new)
- `app/Http/Requests/TierMilestoneRequest.php` - Tier milestone form validation (new)
- `resources/views/admin/gamification/badges-index.blade.php` - Badge list view
- `resources/views/admin/gamification/badges-form.blade.php` - Badge create/edit form
- `resources/views/admin/gamification/rewards-index.blade.php` - Reward list view
- `resources/views/admin/gamification/rewards-form.blade.php` - Reward create/edit form
- `resources/views/admin/gamification/tiers-index.blade.php` - Tier list view
- `resources/views/admin/gamification/tiers-form.blade.php` - Tier create/edit form
- `routes/web.php` - Added CRUD routes for badges, rewards, tiers

---

### 9.7 Gamification Report Export

Admins and treasurers can now export a comprehensive gamification report in CSV or PDF format.

#### Features:

- **Report Sections**:
    - Summary header (total members, points earned/redeemed/adjusted/refunded)
    - Member Points Summary (all users with tier, points breakdown, streaks)
    - Point Transactions (full audit trail with dates, types, admin actors)
    - Badge Earnings (who earned which badges, when)
    - Reward Redemptions (all redemptions with status, claim codes)
- **Date Filtering**: Supports monthly and yearly report periods via existing `report_type`, `month`, `year` query parameters.
- **CSV Export**: Well-structured CSV with named sections and headers.
- **PDF Export**: Dedicated `reports/gamification_pdf` Blade view rendered via dompdf.

#### Files Modified:

- `app/Http/Controllers/ReportController.php` - Added `exportGamificationCSV()`, `exportGamificationPDF()`
- `app/Services/ExportService.php` - Added `generateGamificationReport()`, `generateGamificationPDF()`
- `resources/views/reports/gamification_pdf.blade.php` - PDF template
- `routes/web.php` - Added gamification export routes

---

### 9.8 Donor Information (Phase 1)

Donations now capture detailed donor information for Shariah compliance.

#### Features:

- **Donor Fields**: `donor_name`, `donor_ic` (MyKad), `donor_phone`, `donor_email`, `donor_address` on all donations
- **Conditional Requirements**: `donor_name` and `donor_ic` are **required** when category is Zakat or Waqf; optional for Sadaqah
- **IC Masking**: Displayed as `XXXXXX-**-XXXX` in tables for privacy
- **Malaysian IC Validation**: Accepts `010203-10-1234` or `010203101234` formats

#### Files Modified:

- `database/migrations/*_add_donor_info_to_donations_table.php` - Added donor columns
- `app/Models/Donation.php` - Added `$fillable` fields, accessors (`donor_display_name`, `donor_display_ic`, `has_donor_info`)
- `app/Http/Requests/DonationRequest.php` - Added donor validation rules + conditional requirements
- `app/Http/Controllers/DonationController.php` - Passes donor data to create
- `resources/views/donations/index.blade.php` - Donor section in form (shown for Zakat/Waqf), donor column in table

---

### 9.9 Shariah Type + Fund Purpose Split (Phase 2)

The old `category` dropdown (which mixed Shariah classification with mosque fund designation) was split into two independent fields.

#### Before:

- One dropdown: `zakat`, `zakat_fitr`, `sadaqah`, `sadaqah_jariyah`, `infaq`, `waqf`, `operations`, `construction`, `education_community`, `humanitarian`, `other`

#### After:

| Field                        | Values                                          | Purpose                                 |
| ---------------------------- | ----------------------------------------------- | --------------------------------------- |
| **category** (Shariah Type)  | `zakat`, `zakat_fitr`, `sadaqah`, `waqf`        | Determines Islamic ruling + badge color |
| **fund_purpose** (free-text) | e.g. "General Fund", "Kipas Gergasi", "Aircond" | Specifies what the donation is for      |

- **fund_purpose** is **required** when Shariah Type is `sadaqah`
- Clickable suggestion chips ("General Fund", "Kipas Gergasi", etc.) speed up entry
- Old data was migrated: `operations` → `sadaqah` + `fund_purpose: Operations`

#### Files Modified:

- `database/migrations/*_add_fund_purpose_to_donations_table.php` - Added `fund_purpose` column + data migration
- `app/Models/Donation.php` - Added `fund_purpose` to `$fillable`, `getSuggestedPurposes()`, `getFundPurposeLabelAttribute()`
- `app/Http/Requests/DonationRequest.php` - Simplified category validation to 4 values, added fund_purpose rules
- `app/Http/Controllers/DonationController.php` - Passes `$suggestedPurposes` to view
- `resources/views/donations/index.blade.php` - New Shariah Type dropdown + Fund Purpose input with chips

---

### 9.10 Verification Flow (Phase 3)

Donations now have a verification workflow to solve the WhatsApp/bank reconciliation problem.

#### Donation Statuses:

| Status      | Description                         | Next Action        |
| ----------- | ----------------------------------- | ------------------ |
| `pending`   | Just entered, awaiting cross-check  | Confirm or Dispute |
| `confirmed` | Matched against bank/cash records   | Locked             |
| `disputed`  | Mismatch found, needs investigation | Flagged            |

#### Features:

- **Default Status**: New donations start as `pending`
- **Reference Field**: Optional bank ref, WhatsApp ref, or receipt number
- **Status Filter Bar**: Filter by All / Pending / Confirmed / Disputed (with red badge for pending count)
- **Confirm/Dispute Buttons**: Appear for pending donations in the Actions column
- **Audit Trail**: `verified_by` (user) and `verified_at` (timestamp) recorded on each action
- **Status Badges**: Color-coded badges in table (yellow=pend, green=confirm, red=dispute)

#### Files Modified:

- `database/migrations/*_add_verification_fields_to_donations_table.php` - Added `status`, `reference`, `verified_by`, `verified_at`
- `app/Models/Donation.php` - Added scopes (`pending()`, `confirmed()`, `disputed()`), accessors, `verifier()` relationship
- `app/Http/Requests/DonationRequest.php` - Added `reference` field, default status = `pending`
- `app/Http/Controllers/DonationController.php` - Added `confirm()` and `dispute()` methods
- `resources/views/donations/index.blade.php` - Status filter bar, status badges, confirm/dispute actions, reference input
- `routes/web.php` - Added `PATCH donations/{id}/confirm` and `PATCH donations/{id}/dispute`

---

### 9.11 Akad & Amil Recording (Phase 5)

Zakat donations now have Shariah-compliant akad (contract) tracking with printable PDF slips.

#### Features:

- **ZakatAkad Model**: Links to donation via `donation_id`, captures `muzakki_name`, `muzakki_ic`, `amil_name`, `amil_user_id` (optional system user link), `akad_date`, `amount`, `notes`
- **Amil Options**: Free-text name (always available) + system user dropdown (optional link to registered user)
- **Conditional Display**: Akad section appears automatically when Zakat/Zakat Fitr is selected
- **Akad Reference**: Auto-generated code format: `ZKT-YYYYMMDD-NNN`
- **Printable PDF Slip**: Downloadable receipt with mosque header, bismillah, Quran verse, signature line — uses DOMPDF
- **Print Button**: Printer icon in the Actions column for zakat donations with akad records

#### Files Modified:

- `database/migrations/*_create_zakat_akads_table.php` - New `zakat_akads` table
- `app/Models/ZakatAkad.php` - New model with relationships and `akad_reference`, `amil_display` accessors
- `app/Models/Donation.php` - Added `hasOne(ZakatAkad)` relationship
- `app/Http/Requests/DonationRequest.php` - Added `amil_name`, `amil_user_id`, `akad_date`, `akad_notes` validation
- `app/Http/Controllers/DonationController.php` - Auto-creates ZakatAkad after zakat donation; `printAkad($id)` method downloads PDF
- `resources/views/donations/akad-pdf.blade.php` - New printable PDF template
- `resources/views/donations/index.blade.php` - Akad section in form, print button in table
- `routes/web.php` - Added `GET donations/{id}/akad/print`

---

### 9.12 Batch Entry Mode (Phase 6)

Admin can now enter multiple Sadaqah donations at once — useful for Friday prayers, events, or box collection.

#### Features:

- **Multi-Row Entry**: Table with add/remove row buttons (up to 50 rows per batch)
- **Sadaqah Only**: Batch entry is restricted to Sadaqah only — Zakat/Waqf require the single entry form for Shariah compliance
- **Purpose Chips**: Clickable fund purpose suggestions (same pattern as single entry)
- **Defaults**: Date = today, Sadaqah type, Cash source — partially pre-filled
- **Bulk Validation**: All rows validated at once, errors shown with row indices
- **Navigation**: "Batch Entry" button on the main donations page header

#### Files Modified:

- `app/Http/Requests/BatchDonationRequest.php` - New request: validates array of donations, forces `category='sadaqah'`
- `app/Http/Controllers/DonationController.php` - Added `batchForm()` and `batchStore()` methods
- `resources/views/donations/batch.blade.php` - New batch entry interface with dynamic rows
- `resources/views/donations/index.blade.php` - Added "Batch Entry" link in form header
- `routes/web.php` - Added `GET donations/batch` and `POST donations/batch`

---

### 9.13 Reporting Enhancements (Phase 7)

Exports, summary stats, and dashboard were enhanced with new fields and cards.

#### Features:

- **Export Fields**: CSV/PDF exports now include `Donor Name`, `Donor IC`, `Fund Purpose`, `Status`, `Reference`
- **Summary Stats Cards**: Donations page shows Pending (count + RM), Confirmed (RM), Disputed (RM) cards
- **Fund Purpose Breakdown**: Chips showing amounts per fund purpose in donation page
- **Dashboard Cards**: Admin/treasurer dashboard shows Zakat, Sadaqah, Waqf totals + This Month + Pending

#### Files Modified:

- `app/Services/ExportService.php` - Added new columns to donation CSV/PDF exports
- `app/Http/Controllers/DonationController.php` - Added `pendingTotal`, `confirmedTotal`, `disputedTotal`, `fundPurposeBreakdown`
- `app/Http/Controllers/DashboardController.php` - Added `$donationStats` with per-type breakdowns
- `resources/views/donations/index.blade.php` - Summary stat cards + fund purpose breakdown chips
- `resources/views/dashboard.blade.php` - Donation summary cards for admin/treasurer

---

### 9.14 Grand Total Split: Zakat / Zakat Fitr / Sadaqah / Waqf (Phase 7b)

All pages that previously displayed a combined "Grand Total" now show four separate totals for Zakat, Zakat Fitr, Sadaqah, and Waqf.

#### Cash Inflow (Donations):

| Page               | Before                        | After                                               |
| ------------------ | ----------------------------- | --------------------------------------------------- |
| **Donations list** | 3 badges (Zakat/Sadaqah/Waqf) | 4 badges (Zakat/Zakat Fitr/Sadaqah/Waqf)            |
| **Dashboard**      | 3 colored cards               | 4 colored cards + This Month + Pending              |
| **Landing page**   | 3-type breakdown              | 4-type breakdown (hero, trust bar, impact, summary) |
| **Transparency**   | 3 inflow cards                | 4 inflow cards (added Zakat Fitr)                   |
| **Reports**        | 3 summary cards               | 4 summary cards (added Zakat Fitr)                  |

#### Cash Outflow (Withdrawals):

- **`type` field** on `withdrawal_requests`: `zakat`, `zakat_fitr`, `sadaqah`, `waqf` (default: `sadaqah`)
- **Withdrawal form** has a Fund Type dropdown with 4 options
- **Withdrawals page** shows 4 "Out" summary cards + Fund badges in table
- **Transparency** shows 4 expense cards
- **Reports** show per-type outflow for all 4 types

#### Files Modified (Inflow):

- `app/Http/Controllers/LandingController.php` - Removed `$totalAmount`, added `$waqfTotal`
- `resources/views/landing.blade.php` - Multiple sections updated to show 3-type breakdown
- `resources/views/reports/index.blade.php` - Replaced single summary cards with per-type in/out cards

#### Files Modified (Outflow):

- `database/migrations/*_add_type_to_withdrawal_requests_table.php` - Added `type` enum to withdrawals
- `database/migrations/*_add_zakat_fitr_to_withdrawal_type.php` - Added `zakat_fitr` to withdrawal type enum
- `app/Models/WithdrawalRequest.php` - Added `type` to `$fillable`
- `app/Http/Requests/WithdrawalRequestForm.php` - Added `type` validation
- `app/Http/Controllers/WithdrawalController.php` - Computes per-type totals, includes type in store
- `resources/views/withdrawals/index.blade.php` - Summary cards, Fund column, type dropdown
- `app/Http/Controllers/VolunteerController.php` - Computes per-type transparency values
- `resources/views/transparency/index.blade.php` - Per-type inflow/outflow cards

---

### 9.15 Withdrawal Balance Validation (Phase 7c)

Withdrawal requests are now validated against available funds to prevent over-withdrawal.

#### Features:

- **Balance Display**: When creating a withdrawal request, the form shows the available balance for the selected fund type
- **Dynamic Updates**: Changing the Fund Type dropdown automatically updates the displayed balance via JavaScript
- **Server-side Validation**: The `WithdrawalController@store` method computes `confirmed donations - approved withdrawals` for the requested fund type and rejects the request if the amount exceeds the available balance
- **Error Message**: User sees a clear error: "Insufficient balance. Available for Sadaqah: RM 0.00"

#### Balance Calculation:

```
Available = SUM(confirmed donations for category) - SUM(approved withdrawals for type)
```

#### Files Modified:

- `app/Http/Controllers/WithdrawalController.php` - Added balance computation (`$typeBalances`) and server-side validation in `store()`; imported `Donation` model
- `resources/views/withdrawals/index.blade.php` - Added dynamic balance display with `onchange` handler + `updateBalance()` JS function; passes `$typeBalances` as JSON to JavaScript

---

### 9.16 Transparency & Reports Reorganization (Phase 7d)

The transparency page was simplified for all users, and the chart + category breakdown was moved to the admin/treasurer reports page.

#### Transparency Page (`/transparency`) — Simplified

| Removed                                          | Reason                                                |
| ------------------------------------------------ | ----------------------------------------------------- |
| Quick filter buttons (Today/Week/Month/Year/All) | Moved to reports page (already had month/year filter) |
| Custom date range form                           | Redundant — reports page already has filtering        |
| Bar chart (Chart.js — Donations vs Expenses)     | Moved to reports financial tab                        |
| Donation Breakdown by Category section           | Moved to reports financial tab                        |

**Now shows only:** Inflow cards (Month / Year per type), Outflow cards (Year per type), Approved Withdrawals table

#### Reports Page (`/reports?tab=financial`) — Enhanced

| Added                  | Description                                                            |
| ---------------------- | ---------------------------------------------------------------------- |
| **Category Breakdown** | Cards per Shariah type with amount, percentage, and progress bars      |
| **Bar Chart**          | "Donations vs Expenses (Last 6 Months)" — Chart.js with green/red bars |
| Zakat Fitr Out         | Added to financial summary table + export                              |

#### Files Modified:

- `app/Http/Controllers/VolunteerController.php` - Simplified `transparency()` method: removed filter logic, chart data, category breakdown computation
- `resources/views/transparency/index.blade.php` - Removed filters, chart, category breakdown; simplified to just inflow/outflow cards + expenses table
- `app/Http/Controllers/ReportController.php` - Added chart data + category breakdown computation; added `chartLabels`, `chartDonations`, `chartExpenses`, `categoryBreakdown`, `catLabels` to compact
- `resources/views/reports/index.blade.php` - Added "Donations by Shariah Type" section with category cards + "Donations vs Expenses" chart in financial tab

---

### 9.17 Fund Purpose Management (Phase 7e)

Admin can now dynamically manage fund purpose suggestions that appear as clickable chips on donation forms.

#### Features:

- **Database-backed**: New `fund_purposes` table with `name`, `sort_order`, `is_active` columns
- **Seeded Defaults**: 8 default purposes seeded on migration (General Fund, Kipas Gergasi, Aircond, Carpets, etc.)
- **Admin CRUD**: Add, edit (inline), and delete fund purposes from a dedicated management page
- **Instant Reflection**: Changes appear immediately in both single entry and batch entry forms
- **Static Method**: `Donation::getSuggestedPurposes()` now queries the database instead of returning a hardcoded array

#### Files Modified:

- `database/migrations/*_create_fund_purposes_table.php` - New table + seed data
- `app/Models/FundPurpose.php` - New model with `active()` and `ordered()` scopes
- `app/Models/Donation.php` - `getSuggestedPurposes()` now queries DB
- `app/Http/Controllers/DonationController.php` - Added `fundPurposeIndex`, `fundPurposeStore`, `fundPurposeUpdate`, `fundPurposeDestroy` methods
- `resources/views/admin/fund-purposes.blade.php` - New admin management page
- `resources/views/donations/index.blade.php` - Added "Purposes" gear icon button in form header
- `routes/web.php` - Added GET/POST/PUT/DELETE routes for fund purposes

---

### 9.18 Native Browser Dialogs Replaced with Modal System

All native `confirm()` and `alert()` browser dialogs were replaced with the app's built-in modal system.

#### Replaced Dialogs:

| File                                       | Before                                       | After                              |
| ------------------------------------------ | -------------------------------------------- | ---------------------------------- |
| `donations/index.blade.php`                | `confirm('Confirm this donation?')`          | `showConfirmModal(...)`            |
| `donations/index.blade.php`                | `confirm('Mark this donation as disputed?')` | `showConfirmModal(...)`            |
| `donations/batch.blade.php`                | `alert('At least one row is required.')`     | `showNotification('warning', ...)` |
| `admin/fund-purposes.blade.php`            | `confirm('Delete this fund purpose?')`       | `showConfirmModal(...)`            |
| `admin/gamification/tiers-index.blade.php` | `confirm('Delete this tier?...')`            | `showConfirmModal(...)`            |
| `profile/index.blade.php`                  | `confirm('Generate a new code?...')`         | `showConfirmDialog(...)`           |

#### Added Modal Functions:

- `showConfirmDialog(title, message, confirmText, confirmClass, onConfirm)` — For non-form actions (JS callbacks)
- `showConfirmModal(...)` — Existing function enhanced for all form-based confirmations

#### Files Modified:

- `resources/views/layouts/app.blade.php` - Added `showConfirmDialog()` function
- `resources/views/donations/index.blade.php` - Confirm/dispute buttons
- `resources/views/donations/batch.blade.php` - Row removal warning
- `resources/views/admin/fund-purposes.blade.php` - Delete button
- `resources/views/admin/gamification/tiers-index.blade.php` - Delete button
- `resources/views/profile/index.blade.php` - Referral code regeneration

---

### 9.19 Back Button System & UI Improvements

A unified back button system was added across all pages, plus mobile UI fixes.

#### Back Button System:

- **Layout-driven**: `@section('back', '/url')` in any Blade template shows a "← Back" link above the content
- **24 pages** now have appropriate back buttons (dashboard excluded as home page)
- **Smart routing**: Most pages → Dashboard; batch → donations; volunteers → events; admin pages → /admin/gamification

#### Flash Message UI Fix:

- **Positioning**: Changed from `top-4 right-4` to `top-16 left-4 right-4 sm:top-4 sm:left-auto sm:right-4` — sits below navbar on mobile, original position on desktop

#### Mobile Navigation Fix:

- **Notifications link**: Added bell icon with unread count badge to the mobile hamburger menu (was only accessible via desktop user dropdown)

#### Files Modified:

- `resources/views/layouts/app.blade.php` - Back button wrapper in layout, flash message positioning, notification link in mobile nav
- All 24 page views - Added `@section('back', ...)` declarations

---

### 9.20 Mobile Responsiveness Overhaul

A comprehensive mobile responsiveness pass was applied to all 26 pages.

#### Tables with Added Mobile Card Views (13 tables):

| File                                         | Tables                                                    |
| -------------------------------------------- | --------------------------------------------------------- |
| `admin/fund-purposes.blade.php`              | Fund Purposes table                                       |
| `transparency/index.blade.php`               | Expenses table                                            |
| `gamification/rewards.blade.php`             | My Redemptions table                                      |
| `admin/gamification/index.blade.php`         | Members table                                             |
| `admin/gamification/rewards-index.blade.php` | Rewards table                                             |
| `admin/gamification/badges-index.blade.php`  | Badges table                                              |
| `admin/gamification/transactions.blade.php`  | Transactions table                                        |
| `admin/gamification/redemptions.blade.php`   | Redemptions table                                         |
| `reports/index.blade.php`                    | Donations, Events, Attendance, Withdrawals tables         |
| `donations/batch.blade.php`                  | Batch entry form (mobile card layout with stacked inputs) |

#### Responsive Spacing Fixes (8+ files):

- `p-6` → `p-4 sm:p-6`, `px-6` → `px-4 sm:px-6` across withdrawals, events, volunteer, reports, profile, dashboard, notifications, transparency
- 60+ `px-6` replacements in reports page alone

#### Button/Header Overflow Fixes (4 files):

- Reports tab nav: Added `overflow-x-auto` for horizontal scroll
- Withdrawals/Events/Create forms: Button groups use `flex-col sm:flex-row` + `w-full sm:w-auto`
- Admin gamification search: Input + button stack vertically on mobile
- Admin CRUD form buttons (badges, rewards, tiers): Auto Fill + Cancel/Submit stack on mobile

#### Pagination Duplicate Fix (2 files):

- Desktop pagination now has `hidden md:block` to prevent duplicate rendering on mobile
- Fixed: `donations/index.blade.php` and `withdrawals/index.blade.php`

#### Event Delete Hide on Mobile:

- Delete event button in mobile card view now `hidden sm:flex` — only visible on tablet/desktop

---

### 9.21 Dynamic Fund Distribution & Financial Summary

#### Landing Page — Fund Distribution Progress:

- **Changed from static 75%** to real per-category data
- Shows 4 separate progress bars (Zakat, Zakat Fitr, Sadaqah, Waqf) with actual collected vs distributed amounts
- Each bar: collected (total donations) vs distributed (approved withdrawals)
- Color-coded per category (gold, amber, blue, purple)

#### Reports — Financial Summary Restructured:

- **Removed**: "Total Donations (In)", "Total Withdrawals (Out)", "Net Balance" rows (combined totals)
- **Added**: Per-category "Net" row after each In/Out pair — each category now shows In, Out, and Net as a self-contained block

#### Files Modified:

- `app/Http/Controllers/LandingController.php` - Added `$distributions` array with per-category in/out/percent
- `resources/views/landing.blade.php` - Dynamic progress bars replacing static 75%; updated "How It Works" step 2 text
- `resources/views/reports/index.blade.php` - Removed combined totals; added per-category net rows

---

### 9.22 System-Automated Rewards & Reward Catalog Overhaul

Two reward types are now automatically executed by the system, and the reward catalog was replaced with mosque-appropriate items.

#### Automated Reward Types

**1. Certificate of Appreciation (Auto-Fulfill)**

When a user redeems the Certificate of Appreciation reward:

- System automatically sets redemption status to `claimed` (no admin approval needed)
- PDF certificate is generated instantly via `CertificateService`
- User sees a "Download Certificate" button in their My Redemptions section
- Admin redemptions page shows "Auto-fulfilled" badge instead of Approve/Reject buttons

**Implementation:**

- `GamificationService::redeemReward()` checks `$reward->code === 'APPRECIATION_CERT'` after creating redemption
- If matched: calls `CertificateService::generateCertificate()` and updates status
- Certificate PDF uses `resources/views/gamification/certificate.blade.php` — A4 landscape with Bismillah, Quran verse (Surah Al-Hujurat 49:13), user name, tier, and dual signature lines
- Download route: `GET /gamification/certificate/{redemption}/download` — protected by ownership check (`$redemption->user_id === Auth::id()`) and `status === 'claimed'`

**2. Priority Event Registration (Capacity Bypass)**

When a user with an active Priority Event Registration redemption attempts to join a full event:

- System checks for an unused (`used_for_event_id IS NULL`) priority redemption with `status = 'claimed'`
- If found: allows the join past capacity, then marks the redemption as consumed by setting `used_for_event_id`
- If not found: returns standard "This event is full" error
- One redemption = one bypass (consumed on first use)

**Implementation:**

- `VolunteerController::joinEvent()` intercepts the `$event->isFull()` check
- Queries `$user->rewardRedemptions()` with `whereHas('reward', fn($q) => $q->where('code', 'PRIORITY_EVENT_REG'))`
- Calls `$priorityRedemption->consumeForEvent($eventId)` to mark as used
- UI shows "Priority Active" badge for unused redemptions, "Used for event #X" for consumed ones

#### Reward Catalog Overhaul

The previous dummy rewards were replaced with 14 mosque-appropriate rewards across 5 categories:

| Category                | Rewards                                                                         | Points Range |
| ----------------------- | ------------------------------------------------------------------------------- | ------------ |
| **facilities**          | Priority Parking (Friday), Priority Event Registration, Family Facility Booking | 50–200       |
| **recognition**         | Certificate of Appreciation, Name on Mosque Appreciation Board                  | 200–300      |
| **events**              | Free Iftar Meal (Ramadan)                                                       | 250          |
| **merchandise_common**  | Mosque Keychain, Sticker Pack, Tote Bag, T-shirt                                | 30–150       |
| **merchandise_limited** | Mosque Tumbler, Kopiah (Embroidered), Prayer Mat, Commemorative Plaque          | 175–500      |

**Seeder Command:** `php artisan mosque:seed-rewards [--force]`

- Truncates `reward_redemptions` and `rewards` tables (with FK check bypass)
- Inserts all 14 rewards with bilingual names/descriptions (English + Malay)
- Located at `app/Console/Commands/SeedMosqueRewards.php`

#### Reward Deletion Protection

`GamificationAdminController::destroyReward()` now prevents deletion if any redemption records exist:

- Returns error: "Cannot delete a reward that has been redeemed. Deactivate it instead."
- Admins should use the toggle (`is_active`) to retire rewards instead of deleting

#### Files Modified/Created

| File                                                                                          | Change                                                                                                                                                           |
| --------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `database/migrations/2026_05_16_145129_add_used_for_event_id_to_reward_redemptions_table.php` | **New** — adds `used_for_event_id` FK to `events` table                                                                                                          |
| `app/Models/RewardRedemption.php`                                                             | Added `used_for_event_id` to fillable, `usedForEvent()` relationship, `isPriorityRegistration()`, `isCertificate()`, `isConsumed()`, `consumeForEvent()` methods |
| `app/Services/GamificationService.php`                                                        | Auto-fulfill logic for Certificate of Appreciation in `redeemReward()`                                                                                           |
| `app/Http/Controllers/VolunteerController.php`                                                | Priority bypass in `joinEvent()` — checks for unused priority redemption before rejecting full event                                                             |
| `app/Http/Controllers/GamificationController.php`                                             | Added `downloadCertificate()` method with ownership + status validation                                                                                          |
| `app/Http/Controllers/Admin/GamificationAdminController.php`                                  | Deletion protection in `destroyReward()`                                                                                                                         |
| `resources/views/gamification/certificate.blade.php`                                          | **New** — PDF certificate template (A4 landscape, bilingual)                                                                                                     |
| `resources/views/gamification/rewards.blade.php`                                              | Category filter updated to new categories, certificate download button, priority status badges                                                                   |
| `resources/views/admin/gamification/redemptions.blade.php`                                    | Certificates show "Auto-fulfilled" badge instead of action buttons                                                                                               |
| `routes/web.php`                                                                              | Added `gamification.certificate.download` route                                                                                                                  |
| `app/Console/Commands/SeedMosqueRewards.php`                                                  | **New** — artisan command to reset and seed reward catalog                                                                                                       |

### 9.23 Email Verification & Resend Mail Transport

The application now implements Laravel's `MustVerifyEmail` interface for email verification, with a custom HTTP API transport for Resend (resend.com) to bypass Railway's SMTP port restrictions.

#### Email Verification Flow

1. **Registration**: `AuthController@register()` creates the user and immediately calls `$user->sendEmailVerificationNotification()`
2. **Verification Email**: Sent via Resend HTTP API with Malay subject line ("Pengesahan Alamat E-mel"), `Assalamu'alaikum` greeting, and a signed verification URL
3. **Signed URL**: `/email/verify/{id}/{hash}` — uses Laravel's signed URL middleware (no `auth` middleware needed), verified users redirected to dashboard
4. **Login Gate**: `AuthController@login()` blocks unverified users with Malay error message (`"E-mel anda belum disahkan. Sila sahkan e-mel anda terlebih dahulu."`), automatically logs them out
5. **Resend (Authenticated)**: `POST /email/verification-notification` — throttle-protected, linked from login page
6. **Resend (Public)**: `GET /email/resend` + `POST /email/resend` — no authentication required, same success message regardless of whether email exists (non-enumeration)

#### Key Files

| File                                                   | Purpose                                                                                              |
| ------------------------------------------------------ | ---------------------------------------------------------------------------------------------------- |
| `app/Transports/ResendTransport.php`                   | Custom SwiftMailer transport implementing `Swift_Transport` — calls Resend HTTP API via Guzzle       |
| `app/Providers/AppServiceProvider.php`                 | Registers Resend transport via `afterResolving('mail.manager', fn($m) => $m->extend('resend', ...))` |
| `config/mail.php`                                      | Added `resend` mailer config with `api_key` from env                                                 |
| `resources/views/vendor/notifications/email.blade.php` | Malay notification template with `Assalamu'alaikum` greeting                                         |

#### Resend Transport Architecture

```
Mail::send() / Notification::mail()
    ↓
MailManager (mail.manager — deferred singleton)
    ↓
createTransport(['transport' => 'resend', 'api_key' => ...])
    ↓
customCreators['resend'] (registered via afterResolving + extend)
    ↓
App\Transports\ResendTransport implements Swift_Transport
    ↓
GuzzleHttp\Client::post('https://api.resend.com/emails')
    ↓
Resend HTTP API → Email delivered
```

- No SMTP (Laravel 8 uses SwiftMailer, not Symfony Mailer; `Mail::extend()` does not exist on Mail facade)
- `afterResolving('mail.manager')` is used because `MailServiceProvider implements DeferrableProvider` — `mail.manager` binding doesn't exist during `boot()`
- Railway blocks outbound SMTP ports 587 and 465; HTTP API bypasses this restriction
- Falls back to `config('mail.from.address')` for sender; Resend `onboarding@resend.dev` used for testing (no domain verification needed, but only sends to your Resend account email)

#### Railway Deployment Notes

| Constraint                    | Workaround                                                                       |
| ----------------------------- | -------------------------------------------------------------------------------- |
| SMTP ports (587, 465) blocked | HTTP API transport via `guzzlehttp/guzzle`                                       |
| Read-only runtime filesystem  | `LOG_CHANNEL=stderr` (file logging unavailable)                                  |
| Custom Dockerfile required    | Builds with `composer install --no-scripts`, then `php artisan package:discover` |
| `.env` from `.env.example`    | Railway Variables override build-copied `.env` values                            |

**Required Railway Environment Variables:**

- `MAIL_MAILER=resend`
- `LOG_CHANNEL=stderr`
- `RESEND_API_KEY=re_...`
- `MAIL_FROM_ADDRESS=onboarding@resend.dev` (or verified domain)
- `MAIL_FROM_NAME=Masjid Al-Iman`
- Remove all SMTP-related variables (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`)

#### Files Modified/Created:

| File                                                 | Change                                                           |
| ---------------------------------------------------- | ---------------------------------------------------------------- |
| `app/Transports/ResendTransport.php`                 | **New** — custom Swift_Transport calling Resend HTTP API         |
| `app/Providers/AppServiceProvider.php`               | Registered Resend transport via `afterResolving('mail.manager')` |
| `config/mail.php`                                    | Added `resend` mailer config                                     |
| `config/logging.php`                                 | Changed emergency channel to `stderr`                            |
| `.env.example`                                       | Updated mail defaults for Resend                                 |
| `app/Http/Controllers/AuthController.php`            | Added verify/resend methods, login gate for unverified users     |
| `routes/web.php`                                     | Added email verification routes (public resend, signed verify)   |
| `resources/views/auth/login.blade.php`               | Added "Resend Verification Email?" link                          |
| `resources/views/auth/resend-verification.blade.php` | **New** — public resend form                                     |
| `app/Models/User.php`                                | Implements `MustVerifyEmail` contract                            |

---

### 9.24 Withdrawal Fund Purpose, Race Condition Fix & Document Uploads

#### Fund Purpose on Withdrawals

Withdrawal requests now require a `fund_purpose` field (e.g. "General Fund", "Kipas Gergasi") in addition to the existing Shariah `type` field.

**Features:**

- **Required Field**: `fund_purpose` is `VARCHAR(100) NOT NULL` on `withdrawal_requests`
- **Backfill**: Existing data migrated to "General Fund"
- **Balance Validation**: Checks both Shariah type AND fund purpose levels — prevents over-withdrawal at either granularity
- **Form UI**: Dropdown with clickable suggestion chips (same pattern as donation form)
- **Financial Reports**: Per-fund-purpose cash flow breakdown on `/reports?tab=financial`

**Files Modified:**

- `database/migrations/2026_05_21_142221_add_fund_purpose_to_withdrawal_requests_table.php` — Added `fund_purpose` column (raw SQL `ALTER TABLE` to avoid `doctrine/dbal` dependency)
- `app/Models/WithdrawalRequest.php` — Added `fund_purpose` to `$fillable`
- `app/Http/Requests/WithdrawalRequestForm.php` — Added `fund_purpose` validation (required)
- `app/Http/Controllers/WithdrawalController.php` — Added `$fundPurposeBalances` computation, validation against fund purpose balance, passes `$suggestedPurposes` to view
- `resources/views/withdrawals/index.blade.php` — Added fund purpose dropdown with chips, dynamic balance display

#### Race Condition Prevention (3-Layer Fix)

Concurrent withdrawal approvals could cause negative balances. Fixed with three layers of protection:

1. **Database Transaction + Row Lock**: Balance validation wrapped in `DB::transaction()` with `lockForUpdate()` on the relevant donation/withdrawal queries — serializes concurrent approvals
2. **Pending/Maker-Check Balance Blocking**: Pending withdrawals count as committed against available balance — prevents double-spending during the approval window
3. **Server-Side Re-Validation**: Final balance check at approval time (not just creation time) — catches any race between request creation and approval

**Implementation:**

```php
DB::transaction(function () use ($withdrawal) {
    $available =
        Donation::where("category", $withdrawal->type)
            ->where("status", "confirmed")
            ->lockForUpdate()
            ->sum("amount") -
        WithdrawalRequest::whereIn("status", ["approved", "pending"])
            ->where("type", $withdrawal->type)
            ->lockForUpdate()
            ->sum("amount");

    if ($withdrawal->amount > $available) {
        throw new \Exception("Insufficient balance");
    }

    $withdrawal->update([
        "status" => "approved",
        "approved_by" => Auth::id(),
        "approved_at" => now(),
    ]);
});
```

**Files Modified:**

- `app/Http/Controllers/WithdrawalController.php` — `approve()` method wrapped in transaction with `lockForUpdate()`, balance check includes pending withdrawals

#### Supporting Document Uploads

Admins can attach supporting documents (invoices, receipts, quotes) to withdrawal requests. Treasurers can upload proof of payment during approval.

**Features:**

- **Optional at Creation**: Documents are not required to submit a withdrawal request
- **Admin Upload**: Multiple files can be attached when creating or viewing a withdrawal
- **Treasurer Upload**: Proof of payment uploaded during approval flow
- **File Storage**: `storage/app/public/withdrawals/{id}/invoices/` (admin) or `proofs/` (treasurer)
- **Filename Preservation**: Uses `storeAs()` with original filename + extension to prevent `.bin` download issues
- **View UI**: Document badge in table, documents modal with download links
- **Cascade Delete**: Deleting a withdrawal removes all associated documents (FK cascade)

**Files Modified/Created:**

- `database/migrations/2026_05_21_201247_create_withdrawal_documents_table.php` — New table with FK cascade delete
- `app/Models/WithdrawalDocument.php` — New model with `withdrawalRequest()`, `uploader()` relationships, file helper methods
- `app/Http/Controllers/WithdrawalController.php` — Added `uploadDocument()`, `deleteDocument()` methods, document handling in `store()` and `approve()`
- `resources/views/withdrawals/index.blade.php` — File upload input in form, document badges in table, documents modal
- `routes/web.php` — Added POST/DELETE routes for document management

#### Financial Report Fund Purpose Breakdown

The financial tab now shows a per-fund-purpose cash flow breakdown alongside the existing per-Shariah-type summary.

**Features:**

- Groups confirmed donations and approved withdrawals by `fund_purpose`
- Shows In / Out / Net for each fund purpose
- Displays percentage of total and progress bars

**Files Modified:**

- `app/Http/Controllers/ReportController.php` — Added `$fundPurposeBreakdown` query, passed to view
- `resources/views/reports/index.blade.php` — Added fund purpose breakdown section in financial tab

---

### 9.25 UI/UX Audit Fixes (May 2026)

A comprehensive UI/UX audit was conducted covering Admin (AD), Admin Gamification (AG), Public Gamification (PG), and Landing Page (LP) issues.

#### Key Patterns Established

| Pattern                                  | Description                                                                                                                                                                                                               | Files                                           |
| ---------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------- |
| **`@safe_svg()` Directive**              | Blade directive that sanitizes SVG markup — strips `<script>`, event handlers (`on*`), `<foreignObject>`, `<object>`, `<embed>`, `<iframe>`, `<style>`, `<link>`, and dangerous attributes (`xlink:href`, `href` in SVGs) | `app/Providers/AppServiceProvider.php`          |
| **Model Constants for Categories**       | Event categories and reward categories + emoji mappings stored as class constants, not hardcoded in views                                                                                                                 | `app/Models/Event.php`, `app/Models/Reward.php` |
| **`showConfirmModal` for Confirmations** | All `confirm()` and `return confirm()` calls replaced with the global `showConfirmModal()` function                                                                                                                       | Across 6+ Blade views                           |
| **`data-*` Attributes for Dynamic URLs** | Route URLs passed via `data-route` to avoid quoting issues with user-generated content in inline `onclick`                                                                                                                | `admin/gamification/index.blade.php`            |

#### Admin Module (AD)

| #    | Fix                            | Details                                                                                                                                                                                      |
| ---- | ------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| AD-1 | Registration code masking      | `admin/settings.blade.php` — Alpine.js toggle (`x-show`, `@click`) toggles between masked (`••••••••`) and visible registration codes                                                        |
| AD-2 | Amil confirmation dialogs      | `admin/amils.blade.php` — Appoint/revoke actions use `showConfirmModal`; non-amil desktop indicator changed from `—` to "Not appointed"; inline `@if(session('success'))` banners removed    |
| AD-3 | Settings confirm() replacement | `admin/settings.blade.php` — Both "Regenerate" buttons use `showConfirmModal` instead of `return confirm()`                                                                                  |
| AD-4 | Fund purpose deactivate        | `admin/fund-purposes.blade.php` + `DonationController.php` + `routes/web.php` — Delete replaced with activate/deactivate toggle using `is_active` column; inline notification banner removed |

#### Admin Gamification Module (AG)

| #    | Fix                    | Details                                                                                                                                                                                            |
| ---- | ---------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| AG-1 | SVG XSS sanitization   | 7 views, 10 locations — All `{!! $...->icon_svg !!}` and `{!! $...['tier_icon'] !!}` replaced with `@safe_svg()`; includes admin gamification views + public gamification views                    |
| AG-3 | Hardcoded URL replaced | `admin/gamification/index.blade.php` — Adjust Points modal URL changed from hardcoded `/admin/gamification/members/{userId}/adjust` to `data-route` with `route('admin.gamification.adjust', ...)` |
| AG-4 | Tier delete messaging  | `admin/gamification/tiers-index.blade.php` — Delete confirmation explains that points/badges are preserved but tier perks are lost and members move to the next available tier                     |

#### Public Gamification Module (PG)

| #    | Fix                                    | Details                                                                                                                                                                                                                                                                                                                                                  |
| ---- | -------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| PG-1 | SVG XSS sanitization                   | Covered by AG-1 fix (same `@safe_svg()` directive applied to public gamification views: `dashboard.blade.php`, `leaderboard.blade.php`, `badges.blade.php`)                                                                                                                                                                                              |
| PG-2 | Centralized event categories           | `app/Models/Event.php` — `CATEGORIES` constant (`['general', 'religious', 'education', 'community', 'emergency', 'environment', 'health', 'cultural']`); referenced in `leaderboard.blade.php`, `events/index.blade.php` (form dropdown + JS auto-fill), `EventRequest.php`, `UpdateEventRequest.php`, `EventFactory.php`, `RealisticPopulateSeeder.php` |
| PG-3 | Centralized reward categories & emojis | `app/Models/Reward.php` — `CATEGORIES` and `CATEGORY_EMOJI_MAP` constants; replaced `@if/@elseif/@else` emoji chain with `$categoryEmojis[$reward->category] ?? '🌟'` mapping                                                                                                                                                                            |

#### Landing Page (LP)

| #    | Fix                   | Details                                                                                                                                                                                                                                                                                                                                                                                   |
| ---- | --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| LP-1 | CTA route fix         | `landing.blade.php` — 6 CTAs (5 donation cards + 1 volunteer card) changed from `auth()->check() ? route('X') : route('register')` to `route('X')` directly; unauthenticated visitors land on the target page and get redirected to login by auth middleware if needed                                                                                                                    |
| LP-2 | Trust bar empty state | `app/Http/Controllers/LandingController.php` — All trust bar stats and distribution values use `?:` (Elvis) operator with realistic sample fallbacks (Zakat: RM 42,257, Zakat Fitr: RM 50, Sadaqah: RM 2,548, 700 Volunteers with 125 events, Waqf: RM 100,000); both "Live Impact Dashboard" stats and "Fund Distribution Progress" section use the same fallback values for consistency |

#### Key Fix: Missing `$fillable` Property

**Bug:** `App\Models\User` was missing `'is_amil'` in its `$fillable` array. This caused the `AmilAdminController::toggle()` method's `$user->update($validated)` call to silently discard the `is_amil` field due to mass-assignment protection. The controller always showed the opposite success message regardless of the actual outcome.

**Fix:** `app/Models/User.php` — Added `'is_amil'` to `$fillable` array.

#### Live Impact Dashboard Cards

The 5 metric cards in the "Live Impact Dashboard" section on the landing page were refined for consistent responsive layout:

- **Card structure**: Each card uses `flex flex-col h-full` with three vertical sections (icon row → number → subtext area)
- **Typography**: Number values use `text-sm lg:text-lg` with `whitespace-nowrap` — 14px on mobile fits within ~115px available width, 18px on desktop
- **Card padding**: `p-6` (24px) provides balanced whitespace around content
- **Subtext consistency**: All cards have a subtext container `div.flex.items-center.gap-1.mt-2.min-h-[18px]` — empty for Zakat/Zakat Fitr/Sadaqah, populated for Volunteers ("125 events completed") and Waqf ("Endowment")
- **Grid**: `grid-cols-2 lg:grid-cols-5` — 2 columns on mobile, 5 columns on desktop

**Files Modified:** `resources/views/landing.blade.php` (Section 3: LIVE IMPACT DASHBOARD)

#### View Cache

After every Blade template change, run:

```bash
php artisan view:clear
```

The application's Blade cache is at `storage/framework/views/`.

---

## 10. File Reference

| File                                                                                          | Purpose                                                                                                                                                                                                                      |
| --------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `routes/web.php`                                                                              | Primary web route definitions and role-based route groups                                                                                                                                                                    |
| `routes/api.php`                                                                              | Sanctum-protected API route for authenticated user data                                                                                                                                                                      |
| `app/Http/Controllers/AuthController.php`                                                     | Authentication and registration control; processes referral codes during registration                                                                                                                                        |
| `app/Http/Controllers/DashboardController.php`                                                | Dashboard with `$recommendedEvents` (top 8), `$openEvents` (chronological fallback), `$hasCriteria` flag                                                                                                                     |
| `app/Http/Controllers/DonationController.php`                                                 | Donation listing and creation                                                                                                                                                                                                |
| `app/Http/Controllers/WithdrawalController.php`                                               | Withdrawal request workflow, balance validation (type + fund purpose), race condition prevention (transaction + lockForUpdate), document upload/delete                                                                       |
| `app/Http/Controllers/EventController.php`                                                    | Event CRUD, volunteer assignment, attendance management                                                                                                                                                                      |
| `app/Http/Controllers/VolunteerController.php`                                                | Volunteer profile, event joining (with priority bypass), transparency                                                                                                                                                        |
| `app/Http/Controllers/ProfileController.php`                                                  | Profile view/updates + volunteer skill management + referral code generation + avatar upload/delete                                                                                                                          |
| `app/Http/Controllers/ReportController.php`                                                   | Reports dashboard and export generation (donations, events, attendance, financial, gamification); fund purpose breakdown on financial tab                                                                                    |
| `app/Http/Controllers/NotificationController.php`                                             | Notification list and read actions                                                                                                                                                                                           |
| `app/Http/Controllers/GamificationController.php`                                             | Gamification UI, points, badges, rewards, leaderboard, certificate download                                                                                                                                                  |
| `app/Http/Controllers/Admin/GamificationAdminController.php`                                  | Admin gamification dashboard, point adjustments, transaction viewer, reward fulfillment, badges/rewards/tiers CRUD management, reward deletion protection                                                                    |
| `app/Http/Controllers/Admin/AdminSettingsController.php`                                      | Admin settings page, registration code regeneration                                                                                                                                                                          |
| `app/Http/Controllers/Admin/AmilAdminController.php`                                          | Amil management: list users, toggle amil appointment status                                                                                                                                                                  |
| `app/Http/Controllers/LandingController.php`                                                  | Public landing page with trust bar stats, fund distribution progress, and live impact dashboard                                                                                                                              |
| `app/Http/Requests/RegisterRequest.php`                                                       | Registration validation and sanitization                                                                                                                                                                                     |
| `app/Http/Requests/DonationRequest.php`                                                       | Donation validation rules (incl. donor fields, fund_purpose, akad fields)                                                                                                                                                    |
| `app/Http/Requests/BatchDonationRequest.php`                                                  | Batch donation entry validation (Sadaqah-only, array-based)                                                                                                                                                                  |
| `app/Http/Requests/BulkDonationRequest.php`                                                   | Bulk donation entry validation (single-amount multi-purpose)                                                                                                                                                                 |
| `app/Http/Requests/WithdrawalRequestForm.php`                                                 | Withdrawal request validation rules (incl. type field, fund_purpose required)                                                                                                                                                |
| `app/Http/Requests/EventRequest.php`                                                          | Event creation validation rules                                                                                                                                                                                              |
| `app/Http/Requests/UpdateEventRequest.php`                                                    | Event update validation and cross-checks                                                                                                                                                                                     |
| `app/Http/Requests/ProfileUpdateRequest.php`                                                  | Profile info validation rules                                                                                                                                                                                                |
| `app/Http/Requests/VolunteerProfileRequest.php`                                               | Volunteer profile validation rules                                                                                                                                                                                           |
| `app/Http/Requests/BadgeRequest.php`                                                          | Badge CRUD validation rules with icon upload                                                                                                                                                                                 |
| `app/Http/Requests/RewardRequest.php`                                                         | Reward CRUD validation rules with uniqueness and stock checks                                                                                                                                                                |
| `app/Http/Requests/TierMilestoneRequest.php`                                                  | Tier milestone CRUD validation rules                                                                                                                                                                                         |
| `app/Http/Middleware/CheckRole.php`                                                           | Role-based route access enforcement                                                                                                                                                                                          |
| `app/Enums/Role.php`                                                                          | Role enum constants                                                                                                                                                                                                          |
| `app/Notifications/BadgeUnlockedNotification.php`                                             | Notification on badge earn                                                                                                                                                                                                   |
| `app/Notifications/PointsEarnedNotification.php`                                              | Notification on points earned                                                                                                                                                                                                |
| `app/Notifications/ReferralBonusNotification.php`                                             | Notification on referral bonus                                                                                                                                                                                               |
| `app/Notifications/TierUpgradedNotification.php`                                              | Notification on tier upgrade                                                                                                                                                                                                 |
| `app/Notifications/WithdrawalRequestNotification.php`                                         | Notification to treasurer on withdrawal request                                                                                                                                                                              |
| `app/Observers/EventObserver.php`                                                             | Auto-close past events; transition `confirmed` → `pending_review` attendance (note: `booted()` retrieved hook was removed from Event model; status sync now handled by scheduled command)                                    |
| `app/Observers/EventVolunteerObserver.php`                                                    | Volunteer pivot model observer                                                                                                                                                                                               |
| `app/Policies/DonationPolicy.php`                                                             | Donation authorization                                                                                                                                                                                                       |
| `app/Policies/EventPolicy.php`                                                                | Event authorization (includes `join()` method)                                                                                                                                                                               |
| `app/Policies/WithdrawalRequestPolicy.php`                                                    | Withdrawal request authorization                                                                                                                                                                                             |
| `app/Rules/UniqueEventLocation.php`                                                           | Custom validation rule for unique event locations                                                                                                                                                                            |
| `app/Transports/ResendTransport.php`                                                          | Custom SwiftMailer transport for Resend HTTP API (bypasses Railway SMTP port blocks)                                                                                                                                         |
| `app/Models/User.php`                                                                         | User model with roles, gamification relationships, avatar support (`getAvatarUrlAttribute`, `getInitialsAttribute`), `hide_from_leaderboard`, `age`, `address`                                                               |
| `app/Models/Donation.php`                                                                     | Donation entity and relationship                                                                                                                                                                                             |
| `app/Models/WithdrawalRequest.php`                                                            | Withdrawal workflow entity with fund_purpose, balance validation helpers                                                                                                                                                     |
| `app/Models/WithdrawalDocument.php`                                                           | Supporting document model with `withdrawalRequest()`, `uploader()` relationships, file helpers                                                                                                                               |
| `app/Models/Event.php`                                                                        | Event entity with JSON casts, attendance review helpers, capacity scopes, `effective_status` accessor (real-time status without DB writes), null-safe `isPast()`                                                             |
| `app/Models/EventVolunteer.php`                                                               | Event-user pivot model with attendance tracking                                                                                                                                                                              |
| `app/Models/VolunteerProfile.php`                                                             | Volunteer profile entity                                                                                                                                                                                                     |
| `app/Models/MemberPoints.php`                                                                 | Gamification point summary model                                                                                                                                                                                             |
| `app/Models/PointTransaction.php`                                                             | Gamification transaction audit trail                                                                                                                                                                                         |
| `app/Models/Badge.php`                                                                        | Badge master record                                                                                                                                                                                                          |
| `app/Models/BadgeEarning.php`                                                                 | User badge earning pivot                                                                                                                                                                                                     |
| `app/Models/ZakatAkad.php`                                                                    | Zakat akad (contract) record linked to donations                                                                                                                                                                             |
| `app/Models/FundPurpose.php`                                                                  | Fund purpose suggestions with `active()` and `ordered()` scopes                                                                                                                                                              |
| `app/Models/Reward.php`                                                                       | Reward catalog model with image fields, stock quantity tracking, `isAvailable()` method                                                                                                                                      |
| `app/Models/RewardRedemption.php`                                                             | Reward redemption workflow model with `isPriorityRegistration()`, `isCertificate()`, `isConsumed()`, `consumeForEvent()` methods                                                                                             |
| `app/Models/TierMilestone.php`                                                                | Gamification tier definitions with localized names and icon display                                                                                                                                                          |
| `app/Services/RecommendationService.php`                                                      | Volunteer-event matching: `userHasCriteria()` detection, `getFallbackEvents()` (no-criteria fallback with MySQL CASE ordering), `getScoredRecommendations()` (weighted scoring: location/skills/languages/hobbies/interests) |
| `app/Services/ExportService.php`                                                              | CSV/PDF export generation (donations, events, attendance, financial, gamification)                                                                                                                                           |
| `app/Services/GamificationService.php`                                                        | Points, badges, tiers, referrals (`generateReferralCode()`, `processReferral()`), reward redemption (auto-fulfills Certificate of Appreciation)                                                                              |
| `app/Services/LeaderboardService.php`                                                         | Leaderboard scoring and caching                                                                                                                                                                                              |
| `app/Services/CertificateService.php`                                                         | PDF certificate generation for claimed rewards (auto-generated for Certificate of Appreciation)                                                                                                                              |
| `app/Services/ReceiptNumberService.php`                                                       | Auto-incrementing receipt number generation for donations                                                                                                                                                                    |
| `app/Services/RegistrationCodeService.php`                                                    | Registration code generation and validation for admin/treasurer roles                                                                                                                                                        |
| `app/Models/ReceiptNumberSequence.php`                                                        | Receipt number sequence tracking (prefix + year + counter)                                                                                                                                                                   |
| `app/Models/Setting.php`                                                                      | Key-value application settings (registration codes)                                                                                                                                                                          |
| `config/roles.php`                                                                            | Role permissions and special registration codes                                                                                                                                                                              |
| `database/migrations/2014_10_12_000000_create_users_table.php`                                | User table schema                                                                                                                                                                                                            |
| `database/migrations/2019_08_19_000000_create_failed_jobs_table.php`                          | Failed jobs logging                                                                                                                                                                                                          |
| `database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`               | Sanctum API tokens                                                                                                                                                                                                           |
| `database/migrations/2026_01_26_151012_add_role_and_phone_to_users_table.php`                 | Role and phone columns                                                                                                                                                                                                       |
| `database/migrations/2026_01_26_151030_create_donations_table.php`                            | Donation table schema                                                                                                                                                                                                        |
| `database/migrations/2026_01_26_151041_create_withdrawal_requests_table.php`                  | Withdrawal request schema                                                                                                                                                                                                    |
| `database/migrations/2026_01_26_151052_create_volunteer_profiles_table.php`                   | Volunteer profile schema                                                                                                                                                                                                     |
| `database/migrations/2026_01_26_151104_create_events_table.php`                               | Event table schema                                                                                                                                                                                                           |
| `database/migrations/2026_01_26_151115_create_event_volunteer_table.php`                      | Event-volunteer pivot schema                                                                                                                                                                                                 |
| `database/migrations/2026_03_03_102447_add_role_phone_fix_to_users_table.php`                 | Role/phone migration fix                                                                                                                                                                                                     |
| `database/migrations/2026_03_04_093833_add_user_id_to_donations_table.php`                    | Donations user FK                                                                                                                                                                                                            |
| `database/migrations/2026_03_06_102831_add_age_address_to_users_table.php`                    | Age and address columns on users                                                                                                                                                                                             |
| `database/migrations/2026_03_07_094830_enhance_events_with_criteria.php`                      | Event criteria enhancements                                                                                                                                                                                                  |
| `database/migrations/2026_03_07_094815_enhance_volunteer_profiles_with_criteria.php`          | Volunteer profile enhancements                                                                                                                                                                                               |
| `database/migrations/2026_04_06_000001_add_attendance_fields_to_event_volunteer_table.php`    | Attendance tracking extension                                                                                                                                                                                                |
| `database/migrations/2026_04_06_114932_add_status_to_events_table.php`                        | Event status column                                                                                                                                                                                                          |
| `database/migrations/2026_04_06_115648_update_past_events_status.php`                         | Past events status update seeder                                                                                                                                                                                             |
| `database/migrations/2026_04_07_000000_create_notifications_table.php`                        | Notifications table                                                                                                                                                                                                          |
| `database/migrations/2026_04_11_000001_create_gamification_tables.php`                        | Gamification table creation                                                                                                                                                                                                  |
| `database/migrations/2026_04_11_000002_add_gamification_fields_to_existing_tables.php`        | Gamification fields on core tables                                                                                                                                                                                           |
| `database/migrations/2026_04_18_201005_add_rejection_reason_to_withdrawal_requests_table.php` | Add rejection_reason column to withdrawal_requests                                                                                                                                                                           |
| `database/migrations/2026_05_09_000001_add_image_fields_to_rewards_table.php`                 | Add image, image_svg, description_my, stock_quantity to rewards                                                                                                                                                              |
| `database/migrations/2026_05_09_000002_add_avatar_to_users_table.php`                         | Add avatar column to users table                                                                                                                                                                                             |
| `database/migrations/*_add_donor_info_to_donations_table.php`                                 | Add donor_name, donor_ic, donor_phone, donor_email, donor_address to donations                                                                                                                                               |
| `database/migrations/*_add_fund_purpose_to_donations_table.php`                               | Add fund_purpose column + migrate old category data                                                                                                                                                                          |
| `database/migrations/*_add_verification_fields_to_donations_table.php`                        | Add status, reference, verified_by, verified_at to donations                                                                                                                                                                 |
| `database/migrations/*_create_zakat_akads_table.php`                                          | Create zakat_akads table for akad recording                                                                                                                                                                                  |
| `database/migrations/*_add_type_to_withdrawal_requests_table.php`                             | Add type enum (zakat/sadaqah/waqf) to withdrawal_requests                                                                                                                                                                    |
| `database/migrations/*_create_fund_purposes_table.php`                                        | Create fund_purposes table for admin-managed purpose suggestions                                                                                                                                                             |
| `database/migrations/2026_05_16_145129_add_used_for_event_id_to_reward_redemptions_table.php` | Add `used_for_event_id` FK to reward_redemptions for priority event tracking                                                                                                                                                 |
| `database/migrations/2026_05_21_142221_add_fund_purpose_to_withdrawal_requests_table.php`     | Add `fund_purpose` VARCHAR(100) NOT NULL to withdrawal_requests (raw SQL ALTER TABLE)                                                                                                                                        |
| `database/migrations/2026_05_21_201247_create_withdrawal_documents_table.php`                 | Create withdrawal_documents table with FK cascade delete                                                                                                                                                                     |
| `app/Console/Commands/GenerateReferralCodes.php`                                              | Artisan command for bulk-generating referral codes for existing members                                                                                                                                                      |
| `app/Console/Commands/GenerateRegistrationCodes.php`                                          | Artisan command to generate secure random admin/treasurer registration codes                                                                                                                                                 |
| `app/Console/Commands/SeedMosqueRewards.php`                                                  | Artisan command to reset and seed mosque-appropriate reward catalog (14 rewards)                                                                                                                                             |
| `app/Console/Commands/ClosePastEvents.php`                                                    | Scheduled command to auto-close past events (hourly via Laravel Scheduler)                                                                                                                                                   |
| `app/Console/Commands/CleanupDonorIc.php`                                                     | Command to clean up and encrypt donor IC numbers                                                                                                                                                                             |
| `app/Console/Commands/GenerateMosqueTestData.php`                                             | Command to generate realistic test data for the mosque system                                                                                                                                                                |
| `app/Console/Commands/UpdatePendingAttendanceReviews.php`                                     | Command to update attendance records needing review                                                                                                                                                                          |
| `app/Console/Commands/ValidateTestData.php`                                                   | Command to validate generated test data integrity                                                                                                                                                                            |
| `database/seeders/GenerateUserAvatars.php`                                                    | Generate avatar images for all users using GD library                                                                                                                                                                        |
| `resources/views/gamification/certificate.blade.php`                                          | PDF certificate template (A4 landscape, bilingual, with Bismillah & Quran verse)                                                                                                                                             |
| `app/Transports/ResendTransport.php`                                                          | Custom SwiftMailer HTTP API transport for Resend (production mail on Railway)                                                                                                                                                |
| `resources/views/auth/resend-verification.blade.php`                                          | Public email verification resend form                                                                                                                                                                                        |
| `package.json`                                                                                | Node toolchain and Laravel Mix build scripts                                                                                                                                                                                 |

#### Additional Files (Previously Undocumented)

##### Console Commands

| File                                                      | Purpose                                                                                                  |
| --------------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| `app/Console/Commands/InsertBulkSamples.php`              | Artisan command for bulk sample data insertion                                                           |
| `app/Console/Commands/InsertCuratedSamples.php`           | Artisan command for curated sample data insertion                                                        |
| `app/Console/Commands/UpdatePendingAttendanceReviews.php` | Command to update attendance records needing review (replaces earlier `MarkPendingAttendance` reference) |

##### Notifications

| File                                                  | Purpose                                         |
| ----------------------------------------------------- | ----------------------------------------------- |
| `app/Notifications/DonationNotification.php`          | Donation-related notification                   |
| `app/Notifications/EventNotification.php`             | Event-related notification                      |
| `app/Notifications/PointsAdjustmentNotification.php`  | Admin points adjustment notification            |
| `app/Notifications/RewardFulfillmentNotification.php` | Reward fulfillment notification                 |
| `app/Notifications/ResetPassword.php`                 | Laravel default password reset notification     |
| `app/Notifications/VerifyEmail.php`                   | Laravel default email verification notification |

##### Middleware

| File                                             | Purpose                                                                                          |
| ------------------------------------------------ | ------------------------------------------------------------------------------------------------ |
| `app/Http/Middleware/PreventBackButtonCache.php` | Custom middleware that sets `Cache-Control: no-store` headers on auth routes (see §11.1, fix #1) |

##### Exceptions

| File                         | Purpose                          |
| ---------------------------- | -------------------------------- |
| `app/Exceptions/Handler.php` | Global Laravel exception handler |

##### Database Seeders

| File                                           | Purpose                                             |
| ---------------------------------------------- | --------------------------------------------------- |
| `database/seeders/CuratedSampleSeeder.php`     | Curated sample data seeder                          |
| `database/seeders/DemoDataSeeder.php`          | Demo data seeder                                    |
| `database/seeders/EventSeeder.php`             | Event data seeder                                   |
| `database/seeders/GamificationSeeder.php`      | Gamification tables seeder (badges, rewards, tiers) |
| `database/seeders/RealisticPopulateSeeder.php` | Realistic mosque scenario data seeder               |
| `database/seeders/UserSeeder.php`              | Default user accounts seeder                        |

##### Additional Migrations

| File                                                                                          | Purpose                                                                                      |
| --------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `database/migrations/2026_05_18_140724_encrypt_existing_donor_ics.php`                        | Data migration: encrypt existing donor IC numbers                                            |
| `database/migrations/2026_05_20_135033_add_maker_checker_to_withdrawal_requests_table.php`    | Adds maker-checker columns (`maker_checked_by`, `maker_checked_at`) to `withdrawal_requests` |
| `database/migrations/2026_05_28_000002_add_rejection_fields_to_withdrawal_requests_table.php` | Adds rejection audit fields to `withdrawal_requests`                                         |

---

## 11. Security Audit & Hardening Log

### Audit Date: May 2026

This section documents security issues found and fixes applied during proactive security review.

### 11.1 Fixed Issues

| #   | Severity | Category                       | Issue                                                                                     | Location                           | Fix Applied                                                                                                                        |
| --- | -------- | ------------------------------ | ----------------------------------------------------------------------------------------- | ---------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- |
| 1   | Medium   | **Session / Cache**            | Browser back button showed cached authenticated pages after logout                        | All auth routes                    | Added `PreventBackButtonCache` middleware with `Cache-Control: no-store` headers. Registered in `Kernel.php` on `web` group.       |
| 2   | Medium   | **Session / Auth**             | Authenticated users could access `/login` and `/register` pages                           | `routes/web.php`                   | Applied `guest` middleware to login/register routes. Updated `RouteServiceProvider::HOME` from `/home` to `/`.                     |
| 3   | Medium   | **XSS (Cross-Site Scripting)** | Notification messages rendered raw HTML via `{!! !!}` allowing potential script injection | `notifications/index.blade.php:76` | Applied `strip_tags()` whitelist allowing only `<strong>`, `<em>`, `<span>`, `<br>` — all other tags stripped.                     |
| 4   | Low      | **Dual Response Handling**     | Join event endpoint only returned JSON but landing page used HTML form submission         | `VolunteerController::joinEvent()` | Added `joinResponse()` helper that detects AJAX via `expectsJson()` and returns appropriate response type.                         |
| 5   | Low      | **CSRF on AJAX**               | AJAX join requests needed proper CSRF token handling                                      | Landing page join form             | Alpine.js `joinEvent()` component sends `X-CSRF-TOKEN` header from form's hidden CSRF input. Toast notifications on success/error. |

### 11.2 Remaining Items for Production (Not Yet Fixed)

These are configuration or design items that should be addressed before deploying to production.

| #   | Severity       | Category             | Issue                                                                       | Location                                               | Recommended Fix                                                                                                |
| --- | -------------- | -------------------- | --------------------------------------------------------------------------- | ------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------- | ------ | ------------------------ |
| 6   | **Production** | **Environment**      | `APP_DEBUG=true` and `APP_ENV=local` expose stack traces and debug info     | `.env`                                                 | Set `APP_DEBUG=false` and `APP_ENV=production` in production environment.                                      |
| 7   | Low            | **Session**          | Sessions persist after browser close (`expire_on_close: false`)             | `config/session.php:36`                                | Consider setting `expire_on_close: true` for sensitive apps, or implement idle timeout with re-authentication. |
| 8   | Low            | **Input Validation** | `WithdrawalController::reject()` does not validate `rejection_reason` input | `app/Http/Controllers/WithdrawalController.php:81-104` | Add validation: `'rejection_reason' => 'required                                                               | string | max:500'` before saving. |

### 11.3 Verified Secure

These areas were audited and found to have no issues:

- **CSRF Protection** — All forms (POST/PUT/PATCH/DELETE) include `@csrf` or send `X-CSRF-TOKEN` header for AJAX
- **IDOR / Ownership** — All user-scoped queries use `Auth::user()->relation()` pattern
- **Role Middleware** — Admin/treasurer routes consistently protected with `role:` middleware
- **Mass Assignment** — All `create()`/`update()` calls use FormRequest-validated data
- **Rate Limiting** — Login (5/min) and Register (10/min) have throttle middleware
- **SQL Injection** — `selectRaw`/`DB::raw` usage contains no user-supplied input
- **XSS in other views** — Only `notifications/index.blade.php` used `{!! !!}` on user-controllable data; gamification views use `{!! !!}` only for controlled SVG/icon data

---

## 12. Event Management Performance & Bug Fixes (May 2026)

### 12.1 Issues Identified and Resolved

Five issues were identified on the `/events/manage` route and resolved:

| #   | Issue                                     | Severity | Root Cause                                                                                                 | Fix                                                                                                                          |
| --- | ----------------------------------------- | -------- | ---------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------- |
| 1   | **N+1 Query Problem**                     | Critical | `$evt->volunteers()->count()` called in view loop — 1 query per event                                      | Added `withCount()` in controller; view uses `$evt->volunteers_count`                                                        |
| 2   | **Auto-Update Status on Every Page Load** | High     | `Event::booted()` with `retrieved` hook wrote to DB on every model retrieval                               | Removed `booted()` hook; replaced with Laravel Scheduler (`events:close-past` hourly) + `effective_status` computed property |
| 3   | **Inconsistent Volunteer Count**          | Medium   | View used `volunteers()->count()` (all), but model accessor `volunteerCount` filtered by attendance status | `withCount()` now applies same `whereIn('event_volunteer.attendance_status', [...])` filter                                  |
| 4   | **Sorting Mismatch**                      | Low      | "Volunteers" column sorted by `created_at` instead of volunteer count                                      | Changed sort key to `volunteers_count` (the alias from `withCount`)                                                          |
| 5   | **Potential Null `event_date`**           | Medium   | `isPast()` and `->format()` called without null check — crashes on null dates                              | Added null guards in `isPast()` and Blade templates                                                                          |

### 12.2 Detailed Changes

#### Issue 1: N+1 Query Fix

**Files:** `app/Http/Controllers/EventController.php`, `resources/views/events/index.blade.php`

```php
// Before (EventController.php)
$events = Event::orderBy($sortEvent, $directionEvent)->paginate(10);

// After
$events = Event::withCount([
    "volunteers" => function ($query) {
        $query->whereIn("event_volunteer.attendance_status", [
            "confirmed",
            "pending_review",
            "completed",
        ]);
    },
])
    ->orderBy($sortEvent, $directionEvent)
    ->paginate(10);
```

```blade
{{-- Before (index.blade.php) --}}
@php $volunteerCount = $evt->volunteers()->count(); @endphp

{{-- After --}}
@php $volunteerCount = $evt->volunteers_count; @endphp
```

**Result:** Query count reduced from 1+N to 1 per page load.

#### Issue 2: Remove Side-Effect `booted()` Hook

**Files:** `app/Models/Event.php`, `app/Console/Kernel.php`, `app/Console/Commands/ClosePastEvents.php`, `resources/views/events/index.blade.php`

**Removed from Event.php:**

```php
// DELETED — caused DB writes on every model retrieval
protected static function booted()
{
    static::retrieved(function (self $event) {
        if ($event->status !== 'closed' && $event->status !== 'cancelled' && $event->isPast()) {
            $event->update(['status' => 'closed']);
        }
    });
}
```

**Added `effective_status` accessor to Event.php:**

```php
public function getEffectiveStatusAttribute(): string
{
    if ($this->status === 'cancelled') return 'cancelled';
    if ($this->isPast()) return 'closed';
    if ($this->isFull()) return 'closed';
    return $this->status;
}
```

**Optimized ClosePastEvents command (batch update instead of loop):**

```php
$count = Event::where("status", "!=", "closed")
    ->where("status", "!=", "cancelled")
    ->where("event_date", "<", now())
    ->update(["status" => "closed"]);
```

**Scheduler changed from `daily()` to `hourly()`:**

```php
$schedule->command("events:close-past")->hourly();
```

**View updated:** All `$evt->status` references in badge rendering changed to `$evt->effective_status` (8 locations in `index.blade.php`).

**Additional hardening:**

- `canJoin()` now includes `!$this->isPast()` check
- `canOpen()` now blocks past events from being reopened
- `scopeJoinable()` added `where('event_date', '>', now())` filter

#### Issue 3: Consistent Volunteer Count

**File:** `app/Http/Controllers/EventController.php`

The `withCount()` callback now filters by the same attendance statuses as the `volunteerCount` accessor:

```php
$query->whereIn("event_volunteer.attendance_status", [
    "confirmed",
    "pending_review",
    "completed",
]);
```

This ensures `$evt->volunteers_count` in the view matches `$evt->volunteerCount` from the model.

#### Issue 4: Sorting Mismatch

**Files:** `app/Http/Controllers/EventController.php`, `resources/views/events/index.blade.php`

- `allowedSorts` changed from `['event_date', 'title', 'status', 'created_at']` to `['event_date', 'title', 'status', 'volunteers_count']`
- Blade sort link changed from `sort_event => 'created_at'` to `sort_event => 'volunteers_count'`
- `edit()` method also updated with `withCount()` to support sorting when editing

#### Issue 5: Null `event_date` Guard

**Files:** `app/Models/Event.php`, `resources/views/events/index.blade.php`

```php
// Event.php
public function isPast(): bool
{
    if (!$this->event_date) {
        return false;
    }
    return $this->event_date->isPast();
}
```

```blade
{{-- index.blade.php (table + mobile views) --}}
{{ $evt->event_date ? $evt->event_date->format('d M Y') : 'No date' }}
```

### 12.3 Reminder: 24-Hour Attendance Review Logic

> **IMPORTANT:** The `needsReview()` method in `Event.php` was changed from 24-hour delay to **immediate review** for testing purposes:
>
> ```php
> // Event.php:48-55
> // Original delayed review logic:
> // return $this->event_date->addHours(24)->isPast() && $this->status !== 'cancelled';
>
> // Immediate review logic for testing: show yellow button as soon as the event is past.
> return $this->isPast() && $this->status !== "cancelled";
> ```
>
> **Action needed before production:** Restore the original 24-hour delay logic by uncommenting the original line and removing the immediate review line. The backend command (`attendance:update-pending-reviews` / `UpdatePendingAttendanceReviews`) and `scopeNeedsAttendanceReview()` still use the 24-hour logic, so there is currently an inconsistency between UI (immediate) and backend (24 hours).

---

## 13. Untracked Development Artifacts

This section tracks files present in the repository that are **not loaded by the application** and are intended for local development / debugging only. They are not part of the runtime, but are documented here so a future maintainer is aware of their existence.

| File               | Status                                                            | Notes                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| ------------------ | ----------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `routes/test2.php` | **Undocumented dev route** — not loaded by `RouteServiceProvider` | Contains a `GET /_test-mail` debug endpoint that instantiates the Resend mail transport (`app("mail.manager")->createTransport(["transport" => "resend", "api_key" => "re_test"])`) and returns the resolved transport class name. Used to verify the Resend HTTP API transport wiring (see §9.23) on a live server without sending real email. **Recommendation:** delete the file, or move it under a `routes/_dev/` directory that is explicitly gitignored, before production deployment. **Status (production):** already removed in the production deployment (`a7870ef refactor(routes): remove test email route`). |
