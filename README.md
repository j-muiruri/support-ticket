# Support Ticket Management System

A RESTful API-based support ticket management system built with Laravel, featuring role-based access control, JWT authentication, and an admin dashboard.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Admin Dashboard](#admin-dashboard)
- [Testing Guide](#testing-guide)
- [Default Credentials](#default-credentials)
- [Troubleshooting](#troubleshooting)

## ✨ Features

- **User Authentication** - Secure registration and login with JWT tokens (Laravel Sanctum)
- **Role-Based Access Control** - USER and ADMIN roles with different permissions
- **Ticket Management** - Create, view, update, and filter support tickets
- **Comment System** - Add comments to tickets (public and internal)
- **Admin Dashboard** - Web interface for managing tickets
- **Auto-Generated Ticket IDs** - Format: TKT-10001, TKT-10002, etc.
- **Security** - Password hashing, SQL injection prevention, input validation

## 🔧 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0 or PostgreSQL >= 13
- Node.js & NPM (optional, for frontend assets)

## 📥 Installation

### 1. Clone or Create Laravel Project

```bash
# Option A: If starting fresh
composer create-project laravel/laravel support-ticket-system
cd support-ticket-system

# Option B: If you have the code
cd support-ticket-system
composer install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketing_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Install Laravel Sanctum

```bash
php artisan install:api
```

## 🗄️ Database Setup

### 1. Create Database

```bash
# MySQL
mysql -u root -p
CREATE DATABASE ticketing_system;
EXIT;
```

### 2. Run Migrations

```bash
php artisan migrate
```

Expected migrations:
- `create_users_table` - User authentication and roles
- `create_tickets_table` - Support tickets
- `create_comments_table` - Ticket comments
- `create_personal_access_tokens_table` - API tokens (Sanctum)

### 3. Seed Admin Users

```bash
php artisan db:seed
```

This creates 3 admin accounts (see [Default Credentials](#default-credentials) below).

## 🚀 Running the Application

### Start the Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

### Access Points

- **API Base URL**: `http://localhost:8000/api/v1`
- **Admin Dashboard**: `http://localhost:8000/admin`

## 📡 API Endpoints

### Authentication

#### Register User
```bash
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "mobile": "254123456789",
    "password": "SecurePass123"
}

Response: 201 Created
{
    "user": { ... },
    "token": "1|xxxxxxxxxxxxx"
}
```

**Note**: All registrations default to USER role. Admins must be created via seeder or database.

#### Login
```bash
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "SecurePass123"
}

Response: 200 OK
{
    "user": { ... },
    "token": "2|xxxxxxxxxxxxx"
}
```

#### Logout
```bash
POST /api/v1/auth/logout
Authorization: Bearer {token}

Response: 200 OK
{
    "message": "Logged out successfully"
}
```

### Tickets

#### Create Ticket (Authenticated Users)
```bash
POST /api/v1/tickets
Authorization: Bearer {token}
Content-Type: application/json

{
    "subject": "Cannot access my account",
    "description": "Getting error 403 when trying to login",
    "priority": "HIGH",
    "category": "ACCOUNT_ACCESS"
}

Response: 201 Created
{
    "id": "TKT-10001",
    "subject": "Cannot access my account",
    "description": "Getting error 403 when trying to login",
    "status": "OPEN",
    "priority": "HIGH",
    "category": "ACCOUNT_ACCESS",
    "created_by": "john@example.com",
    "assigned_to": null,
    "created_at": "2026-01-20T13:58:39Z"
}
```

**Valid Values**:
- **priority**: LOW, MEDIUM, HIGH, URGENT
- **category**: ACCOUNT_ACCESS, BILLING, TECHNICAL, FEATURE_REQUEST, OTHER

#### List Tickets
```bash
# Users see only their tickets
# Admins see all tickets
GET /api/v1/tickets
Authorization: Bearer {token}

# Filter by status
GET /api/v1/tickets?status=OPEN
Authorization: Bearer {token}
```

**Valid Status Values**: OPEN, IN_PROGRESS, RESOLVED, CLOSED

#### Get Single Ticket
```bash
GET /api/v1/tickets/TKT-10001
Authorization: Bearer {token}

Response: 200 OK
{
    "id": "TKT-10001",
    "subject": "Cannot access my account",
    "description": "Getting error 403 when trying to login",
    "status": "OPEN",
    "priority": "HIGH",
    "category": "ACCOUNT_ACCESS",
    "created_by": "john@example.com",
    "assigned_to": null,
    "created_at": "2026-01-20T13:58:39Z",
    "comments": []
}
```

#### Update Ticket Status (Admin Only)
```bash
PATCH /api/v1/admin/tickets/TKT-10001
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "status": "IN_PROGRESS",
    "assigned_to": "admin@example.com",
    "internal_note": "Investigating the 403 error"
}

Response: 200 OK
```

### Comments

#### Add Comment
```bash
POST /api/v1/tickets/TKT-10001/comments
Authorization: Bearer {token}
Content-Type: application/json

{
    "content": "We have identified the issue",
    "is_internal": false
}

Response: 201 Created
{
    "id": 1,
    "content": "We have identified the issue",
    "is_internal": false,
    "user": "admin@example.com",
    "created_at": "2026-01-20T14:30:00Z"
}
```

**Note**: Only admins can set `is_internal: true`

## 🖥️ Admin Dashboard

### Access the Dashboard

1. Navigate to `http://localhost:8000/admin`
2. Login with admin credentials (see below)
3. View and manage all tickets

### Dashboard Features

- **View All Tickets** - See all support tickets in the system
- **Filter by Status** - Filter tickets by OPEN, IN_PROGRESS, RESOLVED, CLOSED
- **View Details** - Click on any ticket to see full details and comments
- **Update Status** - Change ticket status via dropdown
- **Assign Tickets** - Assign tickets to admin users by email
- **Add Comments** - Add public or internal comments to tickets
- **Add Internal Notes** - Admin-only notes when updating tickets

## 🧪 Testing Guide

### Using cURL

#### 1. Register a User
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "mobile": "254700000999",
    "password": "TestPass123"
  }'
```

Save the returned `token` for subsequent requests.

#### 2. Create a Ticket
```bash
curl -X POST http://localhost:8000/api/v1/tickets \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "subject": "Password reset not working",
    "description": "I clicked the reset link but nothing happens",
    "priority": "MEDIUM",
    "category": "TECHNICAL"
  }'
```

#### 3. Login as Admin
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "Admin@123"
  }'
```

Save the admin `token`.

#### 4. View All Tickets (Admin)
```bash
curl -X GET http://localhost:8000/api/v1/tickets \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE"
```

#### 5. Update Ticket (Admin)
```bash
curl -X PATCH http://localhost:8000/api/v1/admin/tickets/TKT-10001 \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "IN_PROGRESS",
    "assigned_to": "admin@example.com"
  }'
```

#### 6. Add Comment
```bash
curl -X POST http://localhost:8000/api/v1/tickets/TKT-10001/comments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Thank you for looking into this!",
    "is_internal": false
  }'
```

### Using Postman

1. Import the endpoints into Postman
2. Create an environment with variable `base_url = http://localhost:8000/api/v1`
3. Create variable `token` to store authentication tokens
4. Set `Authorization` header to `Bearer {{token}}`
5. Test each endpoint sequentially

### Test Workflow

**As a Regular User:**
1. Register → Get token
2. Create ticket → Get ticket ID
3. View your tickets → See only your tickets
4. Add comment to your ticket
5. Try to update ticket status → Should fail (403 Forbidden)

**As an Admin:**
1. Login as admin → Get admin token
2. View all tickets → See all tickets in system
3. Update ticket status → Success
4. Assign ticket to yourself
5. Add internal comment → Only visible to admins
6. Add public comment → Visible to everyone

## 🔑 Default Credentials

### Admin Accounts (Created by Seeder)

| Name | Email | Password | Role |
|------|-------|----------|------|
| Super Admin | admin@example.com | Admin@123 | ADMIN |
| John Admin | john.admin@example.com | Admin@123 | ADMIN |
| Jane Admin | jane.admin@example.com | Admin@123 | ADMIN |

**Security Note**: Change these passwords in production!

### Creating Additional Admins

```bash
php artisan tinker

User::create([
    'name' => 'New Admin',
    'email' => 'newadmin@example.com',
    'mobile' => '254700000001',
    'password' => Hash::make('SecurePassword123'),
    'role' => 'ADMIN'
]);
```

## 🔍 Troubleshooting

### Issue: "Column not found" error

**Solution**: Run migrations
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Issue: "Unauthenticated" error

**Solution**: Check your token
- Ensure you're passing the token in the `Authorization: Bearer {token}` header
- Token may have expired, login again to get a new token

### Issue: "Undefined" showing in dashboard

**Solution**: API response format mismatch
- Ensure TicketController uses the updated `formatTicketResponse()` method
- Check browser console for errors
- Clear browser cache

### Issue: Admin endpoints return 403

**Solution**: Check user role
```bash
php artisan tinker
User::where('email', 'your@email.com')->first()->role;
```

Should return "ADMIN". If not, update:
```bash
$user = User::where('email', 'your@email.com')->first();
$user->role = 'ADMIN';
$user->save();
```

### Issue: Can't view ticket details

**Solution**: Ensure routes accept both ID formats
- Controller should handle both `TKT-10001` and numeric IDs
- Check the updated TicketController `show()` method

### Database Reset

If you need to start fresh:
```bash
php artisan migrate:fresh --seed
```

**Warning**: This will delete all data!

## 📚 Project Structure

```
support-ticket-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── TicketController.php
│   │   │       └── CommentController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/
│   │       ├── RegisterRequest.php
│   │       ├── LoginRequest.php
│   │       ├── CreateTicketRequest.php
│   │       ├── UpdateTicketRequest.php
│   │       └── CommentRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Ticket.php
│   │   └── Comment.php
│   └── Exceptions/
│       └── Handler.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_tickets_table.php
│   │   └── 0001_01_01_000002_create_comments_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── AdminSeeder.php
├── resources/
│   └── views/
│       └── admin-dashboard.blade.php
├── routes/
│   ├── api.php
│   └── web.php
└── README.md
```

## 🎯 Assignment Checklist

- ✅ RESTful API design with proper HTTP methods and status codes
- ✅ JWT/Session-based authentication (Laravel Sanctum)
- ✅ Role-based access control (USER and ADMIN)
- ✅ Auto-generated ticket IDs (TKT-10001 format)
- ✅ Database design with proper relationships and constraints
- ✅ Input validation using Form Requests
- ✅ Password hashing (bcrypt)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Comments system with internal/public visibility
- ✅ Admin dashboard interface
- ✅ Proper error handling with consistent JSON responses
- ✅ Clean code architecture (Controllers, Models, Requests)

## 📄 License

This project was created for a technical interview assessment.

## 👨‍💻 Author

Created as part of a Backend Developer practical interview assignment.

---

**Need Help?** Check the troubleshooting section or review the API endpoint examples above.