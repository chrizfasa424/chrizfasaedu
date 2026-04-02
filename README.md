# ChrizFasa EMS - Education Management System

A comprehensive, multi-tenant school management platform built with Laravel, designed specifically for Nigerian educational institutions.

## Features

### Academic Modules
- Student lifecycle management (admission to alumni)
- Class & arm management (KG, Primary, JSS, SSS)
- Nigerian curriculum-aligned subjects
- Timetable management
- Daily attendance tracking
- WAEC-style grading system (A1-F9)
- Continuous Assessment (CA1, CA2, CA3 + Exam)
- Automated result computation & ranking
- PDF report card generation
- Behaviour & discipline tracking

### Admission Module
- Online admission application
- Document upload & screening
- Admission status workflow
- Automatic admission number generation
- One-click enrollment (creates student, user, parent records)

### Financial Modules
- Fee structure per class/session/term
- Nigerian fee categories (Tuition, Development Levy, ICT, PTA, etc.)
- Invoice generation (bulk & individual)
- Part-payment support
- Paystack & Flutterwave integration
- Bank transfer logging
- Scholarship & discount management
- Late fee penalties
- Financial reporting & dashboards

### Staff Management
- Staff registration with roles
- Salary management
- Staff attendance
- Performance tracking

### Portal Access
- **Student Portal**: Results, timetable, attendance, fee status
- **Parent Portal**: Monitor children, payments, notifications

### Communication
- SMS notifications (Termii, Twilio)
- Email notifications
- Announcements & notice board
- Broadcast messaging

### Additional Modules
- Library management
- Transport & bus routes
- Hostel/boarding management
- Health & medical records
- Asset/inventory management
- CMS (website pages, news, events, gallery)

### Security & Compliance
- Role-based access control (RBAC)
- Comprehensive audit logs
- Nigerian Data Protection Act (NDPA) aligned
- Login monitoring
- Database backup system

### SaaS / Multi-School
- Multi-tenant architecture
- School onboarding system
- Subscription billing
- Per-school domain support
- Usage limits per plan

### API
- RESTful API with Sanctum authentication
- Mobile app ready
- Paystack webhook support

## Tech Stack

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL
- **Auth**: Laravel Sanctum
- **Roles**: Spatie Permission
- **PDF**: DomPDF
- **Excel**: Maatwebsite Excel
- **Payments**: Paystack, Flutterwave
- **SMS**: Termii, Twilio
- **Queue**: Laravel Horizon

## Quick Start

```bash
# Clone the project
git clone <repo-url> ems
cd ems

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Start development server
php artisan serve
npm run dev
```

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@chrizfasa.ng | password |
| School Admin | admin@greenfieldacademy.ng | password |
| Principal | principal@greenfieldacademy.ng | password |

## Project Structure

```
app/
├── Enums/          # AdmissionStatus, GradeLevel, PaymentStatus, Term, UserRole
├── Http/
│   ├── Controllers/
│   │   ├── Academic/       # Sessions, Classes, Students, Subjects, Attendance, Timetable
│   │   ├── Admission/      # Online applications, screening, enrollment
│   │   ├── Communication/  # Announcements, messages
│   │   ├── Examination/    # Results, report cards, score entry
│   │   ├── Financial/      # Fees, invoices, payments (Paystack/Flutterwave)
│   │   ├── Health/         # Medical records
│   │   ├── Hostel/         # Room allocation
│   │   ├── Inventory/      # Assets, maintenance
│   │   ├── Library/        # Books, borrowing
│   │   ├── MultiSchool/    # SaaS tenant management
│   │   ├── Portal/         # Student & parent portals
│   │   ├── Reporting/      # Financial & academic dashboards
│   │   ├── Staff/          # Staff management
│   │   ├── System/         # Settings, configuration
│   │   └── Transport/      # Routes, allocation
│   └── Middleware/
├── Models/         # 25+ Eloquent models
├── Services/       # ResultService, InvoiceService, PaystackService, SmsService
└── Traits/         # BelongsToSchool, HasAuditTrail, GeneratesAdmissionNumber

config/
├── ems.php         # Nigerian grading, states, fee categories, subscription plans
└── services.php    # Paystack, Flutterwave, Termii, Twilio config

database/
├── migrations/     # 10 migration files covering all tables
└── seeders/        # Demo school, roles, classes (KG-SSS3), 30 Nigerian subjects
```

## License

Proprietary
