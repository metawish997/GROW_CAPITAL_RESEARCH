# Grow Capitals Research

## Overview
Grow Capitals Research is a Laravel 13 API-first web platform built with **Laravel Sanctum**. All backend logic is exposed via RESTful JSON APIs — enabling seamless integration with future mobile applications (iOS/Android) using the exact same endpoints.

---

## Tech Stack
| Layer | Technology |
|---|---|
| Backend | Laravel 13 (PHP) |
| Auth API | Laravel Sanctum (Token-based) |
| Database | MySQL |
| Frontend | Blade (HTML/CSS/JS) |
| Payments | Razorpay (DB-stored creds) |
| KYC | Digio API (DB-stored creds) |
| Email OTP | Dynamic SMTP via DB |
| SMS | Configurable SMS Panel (DB-stored) |

---

## Project Structure

### 1. Authentication
- **User Login/Register**: Email OTP-based (passwordless). A 6-digit OTP is sent to the user's email and verified to log in or register.
- **Admin Login**: Separate portal with Email + Password. Credentials: `admin@example.com` / `11111111`.

### 2. User Portal
- **Pages**: Login (OTP flow), User Dashboard
- **Components**: Header (top nav), Footer (user-facing)

### 3. Admin Portal
- **Pages**: Admin Login, Admin Dashboard, API Settings
- **Components**: Header (top nav), Sidebar (persistent admin navigation)

### 4. Backend & API Integrations (Database Stored Credentials)
The backend stores the following credentials **inside the `app_settings` database table** — managed via the Admin panel. **DO NOT store these in the `.env` file**. This allows dynamic updates without redeploying:
- **SMTP Configuration**: For sending OTP emails and transactional notifications.
- **SMS Panel API**: For sending SMS OTPs and alerts.
- **Digio API**: For KYC, e-signing, and document verification.
- **Razorpay**: Payment gateway for financial transactions.

### 5. API-First Architecture (Laravel Sanctum)
All backend logic is exclusively built as RESTful APIs using **Laravel Sanctum** for authentication, making every endpoint reusable for future mobile apps.

---

## Generated Files

### Migrations
- `database/migrations/0001_01_01_000000_create_users_table.php` — Base users table
- `database/migrations/2026_08_20_000001_update_users_table_add_role_otp.php` — Adds `role`, `otp`, `otp_expires_at`, `mobile`
- `database/migrations/2026_08_20_000002_create_app_settings_table.php` — Settings table for credentials

### Models
- `app/Models/User.php` — Updated with `HasApiTokens`, role, OTP fields
- `app/Models/AppSetting.php` — Key-value settings model with `getGroup()` / `setGroup()` helpers

### Seeders
- `database/seeders/AdminSeeder.php` — Seeds `admin@example.com` with role `admin`
- `database/seeders/DatabaseSeeder.php` — Calls `AdminSeeder`

### Controllers (API)
- `app/Http/Controllers/Api/UserAuthController.php` — `sendOtp`, `verifyOtp`, `me`, `logout`
- `app/Http/Controllers/Api/AdminAuthController.php` — `login`, `me`, `logout`
- `app/Http/Controllers/Api/AppSettingController.php` — CRUD for SMTP, SMS, Digio, Razorpay settings

### Middleware
- `app/Http/Middleware/AdminMiddleware.php` — Restricts routes to `role=admin` users

### Routes
- `routes/api.php` — All API endpoints
- `routes/web.php` — Blade view routes

### Views (Blade)
- `resources/views/auth/login.blade.php` — User OTP Login page
- `resources/views/admin/login.blade.php` — Admin Login page (Email + Password)
- `resources/views/admin/dashboard.blade.php` — Admin Dashboard
- `resources/views/admin/settings.blade.php` — API Settings (SMTP, SMS, Digio, Razorpay)

---

## API Endpoints

### User Auth (Public)
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/user/send-otp` | Send OTP to email |
| POST | `/api/user/verify-otp` | Verify OTP & get token |

### User Auth (Protected — Bearer Token)
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/user/me` | Get logged-in user profile |
| POST | `/api/user/logout` | Logout user |

### Admin Auth (Public)
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/admin/login` | Admin login with email + password |

### Admin Auth (Protected — Admin Token)
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/me` | Get admin profile |
| POST | `/api/admin/logout` | Logout admin |
| GET | `/api/admin/settings` | Get all groups summary |
| GET | `/api/admin/settings/{group}` | Get settings for a group (smtp/sms/digio/razorpay) |
| POST | `/api/admin/settings/{group}` | Save settings for a group |

---

## Admin Default Credentials
```
Email:    admin@example.com
Password: 11111111
```

---

## Setup Commands
*(Run these commands manually in your terminal)*

**1. Install dependencies:**
```bash
composer install
npm install
```

**2. Run migrations:**
```bash
php artisan migrate
```

**3. Seed the admin user:**
```bash
php artisan db:seed
```

**4. Start development servers:**
```bash
# Terminal 1: Backend
php artisan serve

# Terminal 2: Frontend assets
npm run dev
```

**5. Install Laravel Sanctum (if not installed):**
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

## URL Routes
| URL | Description |
|---|---|
| `/login` | User OTP Login page |
| `/admin/login` | Admin Login page |
| `/admin/dashboard` | Admin Dashboard |
| `/admin/settings` | API Credentials Settings |


## Overview
Grow Capitals Research is a comprehensive web application featuring dedicated portals for Users and Administrators. This document outlines the project requirements, components, integrations, and the roadmap for development.

## Project Structure & Requirements

### 1. Authentication
*   **Login & Registration**: A secure authentication system utilizing Email OTP (One-Time Password) for both login and new user registration. This ensures a passwordless, seamless onboarding process.

### 2. User Portal
*   **Pages**: Main user dashboard and authentication pages.
*   **Components**:
    *   **Header**: Top navigation bar and user profile access.
    *   **Footer**: Standard user-facing footer with relevant links, support, and information.

### 3. Admin Portal
*   **Pages**: Administrative dashboard to manage platform operations.
*   **Components**:
    *   **Header**: Admin top navigation.
    *   **Sidebar**: Persistent sidebar navigation specifically tailored for administrative routing and tasks.

### 4. Backend & API Integrations (Database Stored Credentials)
The backend will directly manage and securely store the following critical configurations and credentials **inside the Database** via an Admin panel. **DO NOT store these in the `.env` file**. This allows dynamic updates without redeploying or altering environment variables:
*   **SMTP Configuration**: For sending emails (e.g., Email OTPs, transactional notifications).
*   **SMS Panel API**: For sending SMS alerts or mobile OTPs.
*   **Digio API**: For KYC, e-signing, or document verification services.
*   **Razorpay**: Payment gateway credentials for processing financial transactions securely.

### 5. API-First Architecture (Laravel Sanctum)
*   **Mobile-Ready APIs**: All backend logic and functionality will be exclusively developed as RESTful APIs using **Laravel Sanctum** for secure authentication. This API-first approach ensures that the web platform and any future mobile applications can seamlessly consume the exact same backend endpoints.

---

## Development Setup & Commands

*(As requested, please run these commands manually in your terminal when you are ready to proceed with development)*

**1. Frontend & Backend Dependencies**
Ensure all packages are installed:
```bash
composer install
npm install
```

**2. Database Setup**
Update your `.env` file with your database credentials, then run the database migrations:
```bash
php artisan migrate
```

**3. Run the Development Servers**
Start the Laravel local development server and the frontend asset bundler (Vite):
```bash
# Terminal 1: Run the backend server
php artisan serve

# Terminal 2: Run the frontend asset compiler
npm run dev
```

## Next Steps for Development
1.  **UI Layouts**: Build the foundational views/components for the Header, User Footer, and Admin Sidebar.
2.  **OTP Authentication Flow**: Implement the backend logic to generate, send via SMTP, and verify Email OTPs for the unified login/registration system.
3.  **Credential Management**: Create a `settings` database table and backend services to store and retrieve credentials for SMTP, SMS, Digio, and Razorpay dynamically (instead of using `.env`).
