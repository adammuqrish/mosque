# Mosque Management System - Technical Documentation

## 1. System Overview

### What is this application?

A **Mosque Management System** built for Malaysian mosques (Masjid) to manage donations, withdrawals, volunteer events, and financial transparency for the congregation (Jemaah).

### Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.3+ / Laravel 8.x |
| **Frontend** | Blade Templates, Vanilla JS, CSS |
| **Database** | MySQL |
| **Authentication** | Laravel Sanctum (API), Session-based (Web) |
| **Build Tool** | Laravel Mix (Webpack) |

---

## 2. Architecture & Design

### Folder Structure

```
mosque/
├── app/
│   ├── Console/           # Artisan commands
│   ├── Exceptions/        # Error handling
│   ├── Http/
│   │   ├── Controllers/  # 8 Controllers
│   │   └── Middleware/    # Auth, CSRF, etc.
│   ├── Models/            # 6 Eloquent Models
│   └── Providers/         # Service providers
├── bootstrap/             # App bootstrap
├── config/                # Laravel configs (db, mail, etc.)
├── database/
│   ├── factories/         # Model factories
│   ├── migrations/        # 15 migrations
│   └── seeders/           # Initial data
├── public/                # Web root
├── resources/
│   └── views/             # Blade templates
├── routes/
│   ├── api.php            # API routes (Sanctum)
│   └── web.php            # Web routes
├── storage/               # Logs, cache, uploads
├── tests/                 # PHPUnit tests
├── vendor/                 # Composer packages
├── .env                    # Environment config
├── composer.json           # PHP dependencies
└── package.json            # JS dependencies
```

### Data Flow

```
HTTP Request
    ↓
Routes (web.php / api.php)
    ↓
Middleware (auth, csrf, etc.)
    ↓
Controller
    ↓
Model (Eloquent ORM)
    ↓
MySQL Database
    ↓
Response (Blade View / JSON)
```

### Design Patterns Used

| Pattern | Usage |
|---------|-------|
| **MVC** | Controllers → Models → Views |
| **Repository-like** | Controllers interact directly with Models |
| **Active Record** | Eloquent ORM for all DB operations |
| **Service Layer** | Business logic embedded in Controllers |
| **Middleware** | Request filtering (auth, CSRF) |

---

## 3. Key Features & Functionality

### 3.1 User Authentication & Roles

| Role | Code | Permissions |
|------|------|-------------|
| `admin` | `ADMIN123` | Full access, manage donations, events, withdrawals |
| `treasurer` | `TREASURER123` | Approve/reject withdrawals, view reports |
| `member` | (default) | Join events, view profile, see transparency |

**Registration Flow**: Users register with a special code to get elevated roles.

### 3.2 Donation Management

**Model**: `Donation`
```
Fields: id, user_id, amount, category, source, description, donation_date
```

**Controller**: `DonationController`
- `index()` - List all donations
- `store(Request)` - Record new donation (validates: amount, category, source, date)

**Categories**: General, Zakat, Sedekah, etc.
**Sources**: cash, online

### 3.3 Withdrawal Requests

**Model**: `WithdrawalRequest`
```
Fields: id, requested_by, amount, purpose, status, approved_by, approved_at
Status: pending, approved, rejected
```

**Controller**: `WithdrawalController`
- `index()` - List all requests with requester/approver info
- `store(Request)` - Admin creates withdrawal request
- `approve(id)` - Treasurer approves request
- `reject(id)` - Treasurer rejects request

### 3.4 Event Management & Volunteer Matching

**Model**: `Event`
```
Fields: id, title, description, event_date, location, max_volunteers,
        required_skills[], required_hobbies[], required_languages[],
        event_location, location_radius, health_requirement
```

**Model**: `EventVolunteer` (pivot table)
```
Fields: event_id, user_id, status, joined_at
```

**Controller**: `EventController`
- `index()` - List events
- `store(Request)` - Create event with JSON fields
- `destroy(id)` - Delete event

**Smart Recommendation Logic** (`DashboardController`):
- Matches volunteer skills/languages with event requirements
- Location-based matching
- Excludes already-joined events

### 3.5 Volunteer Profiles

**Model**: `VolunteerProfile`
```
Fields: user_id, skills[], availability[], hobbies[], interests[],
        languages[], experience, location, health_status, long_term_availability
```

**Controller**: `ProfileController`
- `index()` - View profile + recommendations
- `updateInfo(Request)` - Update name, phone, age, address
- `updateSkills(Request)` - Update skills, hobbies, languages, etc.
- `updatePassword(Request)` - Change password with current password verification

### 3.6 Financial Transparency

**Controller**: `VolunteerController` (`transparency()`)
- Today's donations
- Monthly donations
- Yearly donations
- Date range filter for expenses
- Approved withdrawal list

### 3.7 Financial Reports

**Controller**: `ReportController`
- Filter by month/year
- Total donations vs withdrawals
- Balance calculation
- Requester and approver tracking

---

## 4. API / Interface Documentation

### Web Routes

| Method | URI | Controller@Method | Description |
|--------|-----|-------------------|-------------|
| GET | `/` | DashboardController@index | Dashboard with events & recommendations |
| GET | `/login` | AuthController@showLoginForm | Login page |
| POST | `/login` | AuthController@login | Process login |
| POST | `/logout` | AuthController@logout | Logout |
| GET | `/register` | AuthController@showRegisterForm | Registration page |
| POST | `/register` | AuthController@register | Process registration |
| GET | `/donations` | DonationController@index | List donations |
| POST | `/donations` | DonationController@store | Record donation |
| GET | `/withdrawals` | WithdrawalController@index | List withdrawal requests |
| POST | `/withdrawals` | WithdrawalController@store | Create request |
| POST | `/withdrawals/{id}/approve` | WithdrawalController@approve | Approve request |
| POST | `/withdrawals/{id}/reject` | WithdrawalController@reject | Reject request |
| POST | `/volunteer/profile/update` | VolunteerController@updateProfile | Update volunteer profile |
| POST | `/events/{id}/join` | VolunteerController@joinEvent | Join event |
| GET | `/volunteer/my-events` | VolunteerController@myEvents | My joined events |
| GET | `/transparency` | VolunteerController@transparency | Financial transparency |
| GET | `/events/manage` | EventController@index | Manage events |
| POST | `/events` | EventController@store | Create event |
| DELETE | `/events/{id}` | EventController@destroy | Delete event |
| GET | `/reports` | ReportController@index | Financial reports |
| GET | `/profile` | ProfileController@index | User profile |
| POST | `/profile/update-info` | ProfileController@updateInfo | Update personal info |
| POST | `/profile/update-skills` | ProfileController@updateSkills | Update skills |
| POST | `/profile/update-password` | ProfileController@updatePassword | Change password |

### API Routes (Sanctum)

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api/user` | Get authenticated user |

---

## 5. Database Schema

### Users
```
users: id, name, email, password, phone, role, age, address, timestamps
```

### Donations
```
donations: id, user_id (FK), amount, category, source, description, donation_date, timestamps
```

### Withdrawal Requests
```
withdrawal_requests: id, requested_by (FK→users), amount, purpose, status,
                     approved_by (FK→users), approved_at, timestamps
```

### Volunteer Profiles
```
volunteer_profiles: id, user_id (FK), skills (JSON), availability (JSON),
                    hobbies (JSON), interests (JSON), languages (JSON),
                    experience, location, health_status, long_term_availability, timestamps
```

### Events
```
events: id, title, description, event_date, location, max_volunteers,
        required_skills (JSON), required_hobbies (JSON),
        required_languages (JSON), event_location, location_radius,
        health_requirement, timestamps
```

### Event-Volunteer (Pivot)
```
event_volunteer: id, event_id (FK), user_id (FK), status, joined_at, UNIQUE(event_id, user_id)
```

---

## 6. Setup & Installation

### Prerequisites
- PHP 7.3+
- Composer
- MySQL 5.7+ or 8.0
- Node.js (for frontend assets)

### Installation Steps

```bash
# 1. Clone and install dependencies
composer install
npm install

# 2. Create .env file
cp .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Create MySQL database
mysql -u root -p
CREATE DATABASE mosque;
EXIT;

# 5. Update .env with database credentials
DB_DATABASE=mosque
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Run migrations and seeders
php artisan migrate
php artisan db:seed

# 7. Build assets
npm run dev

# 8. Start server
php artisan serve
```

### Default Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@mosque.com | password |
| Treasurer | treasurer@mosque.com | password |
| Member | ali@mosque.com | password |
| Member | siti@mosque.com | password |

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | Laravel |
| `APP_ENV` | Environment | local |
| `APP_KEY` | Application key | (generated) |
| `APP_DEBUG` | Debug mode | true |
| `DB_CONNECTION` | Database driver | mysql |
| `DB_HOST` | Database host | 127.0.0.1 |
| `DB_PORT` | Database port | 3306 |
| `DB_DATABASE` | Database name | mosque |
| `DB_USERNAME` | Database user | root |
| `DB_PASSWORD` | Database password | (empty) |
| `SESSION_DRIVER` | Session storage | file |
| `CACHE_DRIVER` | Cache driver | file |

---

## 7. Known Limitations & TODOs

### 7.1 Security Concerns

- [ ] **Hardcoded role codes**: `ADMIN123` and `TREASURER123` are hardcoded in `AuthController.php:74-76`
- [ ] **No role-based middleware**: Routes don't enforce authorization (e.g., Treasurer routes accessible to all)
- [ ] **No CSRF exemption handling**: All routes use default CSRF protection
- [ ] **No input sanitization**: Rich text descriptions could contain XSS

### 7.2 Incomplete Features

- [ ] **No event editing**: Only create and delete for events
- [ ] **No volunteer attendance tracking**: Event status (confirmed/completed/absent) not used
- [ ] **No notification system**: No email/SMS alerts for withdrawals
- [ ] **No export functionality**: Reports can't be exported to PDF/Excel
- [ ] **No audit logging**: No record of who changed what

### 7.3 Technical Debt

- [ ] **Duplicate recommendation logic**: Same algorithm in `DashboardController` and `ProfileController`
- [ ] **JSON string handling**: Manual `json_decode()` checks scattered in controllers
- [ ] **No API versioning**: `/api/` routes have no versioning
- [ ] **Blade-only frontend**: No API-first approach limits mobile app potential
- [ ] **Laravel 8.x**: Consider upgrading to Laravel 10+ for security updates

### 7.4 Missing Validations

- [ ] No maximum amount validation for donations
- [ ] No duplicate email/phone check at registration
- [ ] No event capacity check when joining

---

## 8. File Reference

| File | Purpose |
|------|---------|
| `app/Http/Controllers/AuthController.php` | Login, logout, registration with role assignment |
| `app/Http/Controllers/DashboardController.php` | Main dashboard with event recommendations |
| `app/Http/Controllers/DonationController.php` | Donation CRUD |
| `app/Http/Controllers/WithdrawalController.php` | Withdrawal request & approval workflow |
| `app/Http/Controllers/EventController.php` | Event management |
| `app/Http/Controllers/VolunteerController.php` | Volunteer profiles & transparency |
| `app/Http/Controllers/ReportController.php` | Financial reports |
| `app/Http/Controllers/ProfileController.php` | User profile management |
| `app/Models/User.php` | User model with relationships |
| `app/Models/Event.php` | Event model with JSON casts |
| `app/Models/Donation.php` | Donation model |
| `app/Models/WithdrawalRequest.php` | Withdrawal request model |
| `app/Models/VolunteerProfile.php` | Volunteer profile model |
| `app/Models/EventVolunteer.php` | Pivot model |
| `routes/web.php` | All web routes |
| `database/migrations/*` | Database schema |
| `database/seeders/*` | Initial test data |

---

*Document generated from codebase analysis on Laravel 8.x Mosque Management System*
