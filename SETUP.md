# Event Management System - Setup Guide

## Overview

This is a complete Event Management System built with Laravel 12, featuring:

- **Two User Roles**: Admin and User (Attendee)
- **Event Management**: Full CRUD operations for events
- **Registration System**: Users can register for events with approval workflow
- **Attendance Tracking**: Admin can mark attendance and generate reports
- **Email Notifications**: Automatic emails for registration approval/decline
- **Export Functionality**: Export registrations and attendance lists to PDF/CSV
- **QR Code Tickets**: Generate QR codes for event tickets

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL or SQLite
- Laravel 12

## Installation Steps

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_management
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed Database

This will create an admin user and a test user:

```bash
php artisan db:seed
```

**Default Admin Credentials:**
- Email: `admin@example.com`
- Password: `password`

**Default User Credentials:**
- Email: `user@example.com`
- Password: `password`

### 6. Create Storage Link

Create a symbolic link for storing uploaded files:

```bash
php artisan storage:link
```

### 7. Configure Mail Settings (for email notifications)

Update your `.env` file with mail configuration. For development, you can use Mailtrap or Laravel's log driver:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@eventmanagement.com
MAIL_FROM_NAME="${APP_NAME}"
```

For local testing without a real SMTP server, use:

```env
MAIL_MAILER=log
```

### 8. Build Frontend Assets

```bash
npm run build
```

Or for development with hot reload:

```bash
npm run dev
```

### 9. Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Package Configuration

### DomPDF Configuration

Publish the DomPDF config (if needed):

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Excel Export Configuration

Publish the Excel config (if needed):

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag="config"
```

### QR Code Configuration

If you need to configure QR codes, you may need to publish the config (if the package supports it).

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── User/           # User dashboard controller
│   │   ├── EventController.php
│   │   ├── RegistrationController.php
│   │   └── ExportController.php
│   └── Middleware/
│       └── EnsureUserIsAdmin.php
├── Mail/                    # Email classes
├── Models/                  # Eloquent models
└── Providers/

resources/
└── views/
    ├── admin/              # Admin views
    ├── user/               # User views
    ├── events/             # Public event views
    ├── emails/             # Email templates
    ├── exports/            # Export PDF views
    └── tickets/            # QR ticket views

database/
├── migrations/             # Database migrations
└── seeders/               # Database seeders
```

## Features

### Admin Features

1. **Dashboard**
   - View statistics (total events, users, registrations, attendance rate)
   - Recent events and registrations

2. **Event Management**
   - Create, edit, delete events
   - Upload banner images
   - Search and filter events
   - View event details with registrations

3. **User Management**
   - View all users
   - Edit user details
   - Change user roles (only admins)
   - Activate/deactivate users

4. **Registration Management**
   - View all registrations
   - Approve/decline registrations
   - Filter by status or event

5. **Attendance Management**
   - Mark attendance for events
   - View attendance statistics per event
   - Export attendance lists

6. **Exports**
   - Export registrations to PDF/CSV
   - Export attendance lists to PDF/CSV

### User Features

1. **Browse Events**
   - View all upcoming events
   - Search and filter events
   - View event details

2. **Registration**
   - Register for events
   - View registration status
   - Cancel registrations (before event date)
   - Download QR code tickets (for approved registrations)

3. **Dashboard**
   - View personal statistics
   - View all registrations
   - View attendance history

## Missing Views to Create

The following views still need to be created (some templates are provided, but you may need to create them):

1. **Admin Event Management:**
   - `resources/views/admin/events/create.blade.php` - Event creation form
   - `resources/views/admin/events/edit.blade.php` - Event edit form
   - `resources/views/admin/events/index.blade.php` - Events list (CRUD table)
   - `resources/views/admin/events/show.blade.php` - Event details with registrations

2. **Admin User Management:**
   - `resources/views/admin/users/index.blade.php` - Users list
   - `resources/views/admin/users/create.blade.php` - Create user form
   - `resources/views/admin/users/edit.blade.php` - Edit user form
   - `resources/views/admin/users/show.blade.php` - User details

3. **Admin Registration Management:**
   - `resources/views/admin/registrations/index.blade.php` - Registrations list
   - `resources/views/admin/registrations/show.blade.php` - Registration details

4. **Admin Attendance Management:**
   - `resources/views/admin/attendances/index.blade.php` - Attendance list
   - `resources/views/admin/attendances/manage.blade.php` - Mark attendance page
   - `resources/views/admin/attendances/statistics.blade.php` - Attendance statistics

5. **Email Templates:**
   - `resources/views/emails/registration-approved.blade.php`
   - `resources/views/emails/registration-declined.blade.php`

6. **Export Views:**
   - `resources/views/exports/registrations-pdf.blade.php`
   - `resources/views/exports/attendance-pdf.blade.php`

7. **Ticket Views:**
   - `resources/views/tickets/qr.blade.php` - QR code ticket view

## Routes

### Public Routes
- `GET /` - Home/Events listing
- `GET /events` - Events listing
- `GET /events/{event}` - Event details

### Authenticated Routes
- `GET /dashboard` - Dashboard (redirects based on role)
- `GET /user/dashboard` - User dashboard
- `POST /events/{event}/register` - Register for event
- `POST /registrations/{registration}/cancel` - Cancel registration
- `GET /registrations/{registration}/ticket` - View QR ticket

### Admin Routes
All admin routes are prefixed with `/admin` and require admin middleware:
- `GET /admin/dashboard` - Admin dashboard
- Resource routes for events, users, registrations, attendances
- Export routes for PDF/CSV generation

## Middleware

- `auth` - Requires authentication
- `verified` - Requires email verification (Laravel Breeze)
- `admin` - Requires admin role (custom middleware)

## Models and Relationships

### User Model
- Has many Registrations
- Has many Attendances
- Has many ActivityLogs (as admin)

### Event Model
- Has many Registrations
- Has many Attendances

### Registration Model
- Belongs to User
- Belongs to Event

### Attendance Model
- Belongs to User
- Belongs to Event

### ActivityLog Model
- Belongs to User (admin)

## Database Tables

- `users` - User accounts with role and status
- `events` - Event information
- `registrations` - Event registrations
- `attendances` - Attendance records
- `activity_logs` - Admin activity tracking

## Troubleshooting

### Images not showing
Run `php artisan storage:link` to create the symbolic link.

### Email not sending
Check your `.env` mail configuration. For testing, use `MAIL_MAILER=log` to log emails to `storage/logs/laravel.log`.

### QR Code not working
Ensure the `simple-qrcode/simple-qrcode` package is properly installed. You may need to adjust the QR code generation in `ExportController`.

### Permission denied errors
Ensure storage directories have proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

## Security Notes

1. **Change default passwords** immediately after setup
2. **Use strong passwords** in production
3. **Enable email verification** in production
4. **Configure proper mail settings** for production
5. **Use HTTPS** in production
6. **Review file upload security** for event images

## Additional Notes

- The system uses Laravel Breeze for authentication (Blade stack)
- Tailwind CSS is used for styling
- The system is designed to be responsive
- All admin actions are logged in the `activity_logs` table

## Support

For issues or questions, please check:
1. Laravel documentation: https://laravel.com/docs
2. Laravel Breeze documentation: https://laravel.com/docs/starter-kits#laravel-breeze

## License

This project is open-sourced software licensed under the MIT license.
