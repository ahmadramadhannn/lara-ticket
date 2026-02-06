# 🎫 LaraTicket - Bus Ticket Booking Platform

A comprehensive bus ticket booking and management system built with Laravel 11 and Filament 3. This platform enables bus operators to manage their services while providing customers with an easy-to-use ticket booking experience.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Architecture](#-system-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [Role System](#-role-system)
- [Security Features](#-security-features)
- [Testing](#-testing)
- [API Documentation](#-api-documentation)
- [Project Structure](#-project-structure)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### For Customers (Buyers)
- 🔍 **Search & Book**: Search bus schedules by route, date, and time
- 💺 **Seat Selection**: Interactive seat selection interface
- 💳 **Multiple Payment Options**: Support for various payment methods
- 📱 **E-Tickets**: Digital tickets with QR codes
- 📧 **Email Notifications**: Booking confirmations and reminders
- 🎟️ **Ticket Management**: View and manage bookings

### For Bus Operators (Company Admins)
- 🚌 **Fleet Management**: Manage buses and seat configurations
- 📅 **Schedule Management**: Create and manage bus schedules
- 👥 **Terminal Admin Management**: Invite and manage terminal staff
- 📊 **Reports & Analytics**: Track bookings and revenue
- 🔔 **Real-time Notifications**: Get notified of new bookings

### For Terminal Admins
- ✅ **Ticket Verification**: Verify tickets at departure/arrival terminals
- 🚦 **Departure Management**: Confirm bus departures
- 📍 **Arrival Confirmation**: Mark bus arrivals
- 📊 **Terminal Dashboard**: View terminal-specific operations

### For Super Admins
- 🏢 **Operator Management**: Approve/reject bus operator registrations
- 🗺️ **Route & Terminal Management**: Manage system-wide routes and terminals
- 👤 **User Management**: Full user and role management
- 📜 **Activity Logging**: Complete audit trail of all actions
- 🛡️ **Security Features**: Advanced safety guards and protections

---

## 🛠 Tech Stack

### Backend
- **Laravel 11** - PHP Framework
- **PHP 8.2+** - Programming Language
- **SQLite** (Development) / **MySQL/PostgreSQL** (Production) - Database
- **Filament 3** - Admin Panel Framework

### Frontend
- **Blade Templates** - Templating Engine
- **Tailwind CSS** - CSS Framework
- **Alpine.js** - JavaScript Framework (via Filament)
- **Livewire 3** - Full-stack Framework (via Filament)

### Security
- **Google reCAPTCHA v3** - Bot Protection
- **Laravel Sanctum** - API Authentication (if needed)
- **Laravel's Built-in CSRF Protection**
- **Rate Limiting** - DDoS Protection

### Development Tools
- **Vite** - Asset Bundling
- **Pest/PHPUnit** - Testing Framework
- **Laravel Pint** - Code Style Fixer

---

## 🏗 System Architecture

### Role-Based Access Control (RBAC)

The system implements a granular role-based access control with four primary roles:

```
┌─────────────────┐
│   Super Admin   │ - Platform administrators
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼────────┐
│Company│ │Terminals  │
│ Admin │ │& Routes   │
└───┬───┘ └───────────┘
    │
┌───▼──────────┐
│Terminal Admin│ - Assigned by Company Admin
└───┬──────────┘
    │
┌───▼────┐
│ Buyer  │ - Regular customers
└────────┘
```

### Panels Structure

```
/super-admin   → Super Admin Panel
/company-admin → Company Admin Panel (Bus Operators)
/terminal-admin→ Terminal Admin Panel
/              → Customer Booking Interface
```

---

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (for development) or MySQL/PostgreSQL (for production)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd lara-ticket-app
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Setup

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit `.env` file:

**For SQLite (Development):**
```env
DB_CONNECTION=sqlite
# DB_DATABASE will use database/database.sqlite by default
```

**For MySQL (Production):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lara_ticket
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations & Seeders

```bash
# Create database file (SQLite only)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### Step 6: Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### Step 7: Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

---

## ⚙️ Configuration

### Required Environment Variables

```env
# Application
APP_NAME="LaraTicket"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=sqlite

# Mail (for notifications)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@laraticket.com"
MAIL_FROM_NAME="${APP_NAME}"

# reCAPTCHA v3 (for operator registration)
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

### Getting reCAPTCHA Keys

1. Visit [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Register a new site with **reCAPTCHA v3**
3. Add your domain(s)
4. Copy the **Site Key** and **Secret Key** to your `.env` file

### Storage Configuration

For document uploads (operator registrations), configure storage:

```bash
# Create symbolic link for public storage (if needed in future)
php artisan storage:link
```

Documents are stored in `storage/app/private/operator-documents/` for security.

---

## 💾 Database Setup

### Default Seeded Users

After running `php artisan db:seed`, you'll have these accounts:

| Role | Email | Password | Access |
|------|-------|----------|--------|
| Super Admin | `superadmin@tiketbus.id` | `password` | `/super-admin` |
| Company Admin | `operator@example.com` | `password` | `/company-admin` |
| Terminal Admin | `verifier@example.com` | `password` | `/terminal-admin` |
| Buyer | `buyer@example.com` | `password` | `/` (customer site) |

**⚠️ Important**: Change these passwords in production!

### Database Schema Overview

**Core Tables:**
- `users` - All user accounts
- `bus_operators` - Bus companies
- `terminals` - Bus terminals/stations
- `routes` - Routes between terminals
- `buses` - Bus fleet information
- `schedules` - Bus schedules
- `tickets` - Customer bookings
- `payments` - Payment transactions

**Relationship Tables:**
- `terminal_user` - Terminal admin assignments with permissions
- `operator_documents` - Business verification documents

**Audit Tables:**
- `activity_logs` - Complete audit trail

---

## 👥 Role System

### 1. Super Admin (`super_admin`)

**Responsibilities:**
- Platform-wide administration
- Approve/reject bus operator registrations
- Manage system routes and terminals
- Full user management
- Access activity logs

**Access:**
- Panel: `/super-admin`
- Resources: Users, Bus Operators, Routes, Terminals, Activity Logs

### 2. Company Admin (`company_admin`)

**Responsibilities:**
- Manage company's bus fleet
- Create and manage schedules
- Invite and manage terminal admins
- View company bookings and reports

**Access:**
- Panel: `/company-admin`
- Resources: Buses, Schedules, Terminal Admins, Bookings
- **Restriction**: Must have approved `BusOperator` status

**Multiple Admins**: Multiple company admins can be associated with a single bus operator.

### 3. Terminal Admin (`terminal_admin`)

**Responsibilities:**
- Verify tickets at assigned terminals
- Confirm bus departures
- Confirm bus arrivals
- Monitor terminal operations

**Access:**
- Panel: `/terminal-admin`
- **Permissions** (per terminal assignment):
  - `can_manage_schedules` - View/edit schedules
  - `can_verify_tickets` - Verify passenger tickets
  - `can_confirm_arrivals` - Mark bus arrivals

**Assignment**: Terminal admins are invited by company admins and assigned to specific terminals with granular permissions.

### 4. Buyer (`buyer`)

**Responsibilities:**
- Search for bus schedules
- Book tickets
- Make payments
- View and manage their bookings

**Access:**
- Public website: `/`
- Profile & booking management
- No admin panel access

---

## 🔐 Security Features

### Super Admin Safety Guards

**Delete Protection:**
- ✅ Super admins cannot delete themselves
- ✅ Cannot delete the last active super admin
- ✅ Enhanced confirmation dialogs for critical actions

**Activity Logging:**
- All user management actions are logged
- Includes IP address, user agent, and change details
- Old/new values stored for audit compliance

### Operator Registration Security

**Multi-Layer Protection:**
1. **reCAPTCHA v3** - Invisible bot detection (score threshold: 0.5)
2. **Rate Limiting** - Max 3 registration attempts per hour per IP
3. **Document Verification** - Required business license upload
4. **Email Verification** - Must verify email before approval
5. **Enhanced Validation**:
   - Indonesian phone number format: `+62xxxxxxxxxx` or `08xxxxxxxxxx`
   - Minimum 100-character company description
   - Unique contact information (email, phone)
   - Alphanumeric operator codes only

**Rejection Tracking:**
- Rejection reasons stored in database
- Timestamp of rejection
- Automatic user status change to 'suspended'
- Activity log entry created

### Data Protection

- **Private Document Storage**: Upload documents stored outside public directory
- **CSRF Protection**: All forms protected by Laravel's CSRF tokens
- **SQL Injection Prevention**: Eloquent ORM with prepared statements
- **XSS Protection**: Blade template escaping by default

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/          # Integration tests
│   ├── Auth/         # Authentication tests
│   ├── Booking/      # Booking flow tests
│   └── Admin/        # Admin panel tests
└── Unit/             # Unit tests
    └── Models/       # Model tests
```

### Manual Testing Checklist

**Super Admin Features:**
- [ ] Cannot delete own account
- [ ] Cannot delete last admin
- [ ] Activity logs created for all actions
- [ ] Can approve/reject operator registrations

**Operator Registration:**
- [ ] reCAPTCHA verification works
- [ ] Rate limiting blocks after 3 attempts
- [ ] Business license upload required
- [ ] Phone format validation works
- [ ] Email uniqueness enforced

**Booking Flow:**
- [ ] Search returns correct schedules
- [ ] Seat selection works
- [ ] Payment processing works
- [ ] Ticket generation works
- [ ] Email notifications sent

---

## 🗂 Project Structure

```
lara-ticket-app/
├── app/
│   ├── Filament/
│   │   ├── SuperAdmin/          # Super admin panel resources
│   │   ├── CompanyAdmin/        # Company admin panel resources
│   │   └── TerminalAdmin/       # Terminal admin panel resources
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/            # Authentication controllers
│   │   │   ├── Booking/         # Booking controllers
│   │   │   └── Operator/        # Operator registration
│   │   └── Middleware/          # Custom middleware
│   ├── Models/                  # Eloquent models
│   ├── Providers/
│   │   └── Filament/            # Filament panel providers
│   └── Rules/                   # Custom validation rules
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── resources/
│   ├── views/                   # Blade templates
│   └── css/                     # Stylesheets
├── routes/
│   ├── web.php                  # Web routes
│   └── api.php                  # API routes (if needed)
└── tests/                       # Test files
```

### Key Files

- `app/Models/User.php` - User model with role checking methods
- `app/Models/BusOperator.php` - Bus operator model
- `app/Models/ActivityLog.php` - Audit logging model
- `app/Rules/RecaptchaValidation.php` - reCAPTCHA validation rule
- `config/services.php` - Third-party service configuration

---

## 📚 API Documentation

*(Future: Add API documentation if you implement API endpoints for mobile apps)*

Currently, the system is a monolithic web application. If you need to build a mobile app or integrate with external services, you can:

1. Enable Laravel Sanctum for API authentication
2. Create API controllers in `app/Http/Controllers/Api/`
3. Define routes in `routes/api.php`
4. Document endpoints using tools like Scribe or Swagger

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

### Development Workflow

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Commit your changes**: `git commit -m 'feat: add amazing feature'`
4. **Push to branch**: `git push origin feature/amazing-feature`
5. **Open a Pull Request**

### Commit Message Convention

We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add new feature
fix: bug fix
docs: documentation changes
style: code style changes (formatting)
refactor: code refactoring
test: adding tests
chore: maintenance tasks
```

### Code Style

- Follow **PSR-12** coding standards
- Run Laravel Pint before committing: `./vendor/bin/pint`
- Write tests for new features
- Update documentation as needed

---

## 🐛 Troubleshooting

### Common Issues

**Issue: Migration fails with "database locked"**
```bash
# SQLite issue - stop all artisan serve processes
killall php
php artisan migrate
```

**Issue: reCAPTCHA always fails**
```bash
# Check environment variables are set
php artisan config:clear
php artisan cache:clear
# Verify keys in .env
```

**Issue: File upload fails**
```bash
# Check storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**Issue: Assets not loading**
```bash
# Rebuild assets
npm run build
# Clear view cache
php artisan view:clear
```

---

## 📝 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **Laravel** - The PHP framework
- **Filament** - The admin panel framework
- **Tailwind CSS** - The CSS framework
- **Google reCAPTCHA** - Bot protection

---

## 📞 Support

For issues, questions, or suggestions:

- **GitHub Issues**: [Create an issue](../../issues)
- **Discussions**: [Start a discussion](../../discussions)

---

## 🗺️ Roadmap

Future enhancements planned:

- [ ] Mobile app (React Native / Flutter)
- [ ] Real-time booking updates (WebSockets)
- [ ] Payment gateway integration (Midtrans, Xendit)
- [ ] Multi-language support
- [ ] Advanced analytics dashboard
- [ ] SMS notifications
- [ ] Loyalty program
- [ ] Promo code system
- [ ] Review and rating system

---

**Built with ❤️ using Laravel & Filament**
