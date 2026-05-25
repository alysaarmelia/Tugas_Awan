# IaaS Cloud Portal

A production-ready cloud infrastructure service portal (IaaS) built with **CodeIgniter 4.7**, simulating AWS-like cloud services using MiniStack behind the scenes. Users manage virtual compute resources through a modern web dashboard.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Database Schema](#database-schema)
- [Running the Application](#running-the-application)

---

## Overview

The IaaS Cloud Portal allows users to:

- Register and authenticate using **JWT** (access + refresh tokens)
- Choose from three **subscription tiers** (Free, Pro, Enterprise)
- **Rent additional storage** in 1–100 GB increments
- View and manage their **Access Key / Secret Key** credentials
- Track all account activity through **paginated activity logs**
- Auto-provision **MiniStack** storage buckets and credentials on registration

---

## Features

| Feature | Description |
|---|---|
| **JWT Authentication** | Stateless auth with access + refresh tokens (HS256) |
| **Subscription Tiers** | Free (1 GB), Pro (50 GB / $9.99), Enterprise (500 GB / $49.99) |
| **Storage Rental** | Rent 1–100 GB extra at $0.10/GB/month |
| **Credential Management** | Auto-generated AK/SK, masked display, reveal, copy, regenerate |
| **Activity Logging** | Full audit trail with pagination and action-type filtering |
| **MiniStack Integration** | Auto-creates isolated buckets per user (with mock fallback) |
| **SPA Frontend** | Vanilla JS + Tailwind CSS, hash-based routing, no build step |
| **API Versioning** | All endpoints under `/api/v1/` |
| **Consistent Responses** | All API responses use `{ status, message, data, errors, meta }` |

---

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Backend Framework | CodeIgniter | 4.7 |
| PHP Version | PHP | 8.2+ |
| Database | PostgreSQL | 14+ |
| Authentication | Custom JWT (HS256, native PHP) | — |
| Frontend | Vanilla JavaScript + Tailwind CSS (CDN) | ES Modules |
| Icons | Font Awesome | 6.5 |
| Fonts | Inter (Google Fonts) | — |
| CLI | CodeIgniter Spark | — |

---

## Project Structure

```
amer-muzan/
├── app/
│   ├── Config/
│   │   ├── App.php              # baseURL, JWT secret, timezone
│   │   ├── Autoload.php         # Autoloads App\* and helpers
│   │   ├── Database.php         # MySQLi connection config
│   │   ├── Exceptions.php       # API JSON exception handler
│   │   ├── Filters.php         # JWT + CORS filter aliases
│   │   ├── Routes.php           # All frontend + API routes
│   │   └── Services.php         # DI service registrations
│   ├── Controllers/
│   │   ├── BaseController.php   # Web pages base controller
│   │   ├── Home.php            # Page controllers (dashboard, storage, etc.)
│   │   └── Api/
│   │       ├── BaseApiController.php  # Response envelope, auth helpers
│   │       └── V1/
│   │           ├── AuthController.php        # /api/v1/auth/*
│   │           ├── UserController.php         # /api/v1/user/*
│   │           ├── StorageController.php       # /api/v1/storage/*
│   │           ├── CredentialsController.php    # /api/v1/credentials/*
│   │           └── LogsController.php         # /api/v1/logs/*
│   ├── Database/
│   │   └── Migrations/           # 5 migration files (timestamped)
│   ├── Entities/                 # Entity classes (User, Subscription, etc.)
│   ├── Filters/
│   │   ├── JwtAuthFilter.php    # Bearer token validation
│   │   └── CorsFilter.php       # CORS preflight + headers
│   ├── Helpers/
│   │   └── auth_helper.php      # is_authenticated(), get_current_user_id()
│   ├── Libraries/
│   │   ├── ApiExceptionHandler.php  # JSON error responses for /api/*
│   │   ├── JwtLibrary.php          # HS256 JWT (native PHP)
│   │   └── MiniStackClient.php     # MiniStack API + mock fallback
│   ├── Models/                    # 5 model classes
│   ├── Services/
│   │   ├── AuthService.php        # Register, login, logout, token refresh
│   │   ├── UserService.php         # Profile, subscriptions, tier management
│   │   ├── StorageService.php      # Quota calc, rental logic
│   │   ├── CredentialsService.php  # AK/SK generation, reveal, regenerate
│   │   └── LoggingService.php      # Paginated logs, action filtering
│   └── Views/
│       ├── auth/index.php          # Login / Register SPA page
│       ├── layouts/main.php        # Master layout (sidebar + header)
│       └── pages/                  # Dashboard, storage, credentials, etc.
├── public/
│   └── js/
│       ├── app.js                  # API client, Auth, Router, Toast, Init
│       └── pages/                  # Dashboard, storage, credentials, logs, sub
├── env                            # Environment template
└── README.md
```

---

## Installation

### Prerequisites

- **PHP 8.2+** with extensions: `intl`, `mbstring`, `json`, `pgsql`, `libcurl`
- **PostgreSQL 14+** (running on localhost:5432)
- **Composer** (for CodeIgniter dependencies)

### Steps

```bash
# 1. Navigate to project
cd amer-muzan

# 2. Install dependencies
composer install

# 3. Copy environment file
cp env .env

# 4. Create the database (requires psql in PATH, or use pgAdmin/DBeaver)
psql -U postgres -c "CREATE DATABASE iaas_portal;"

# 5. Run migrations (creates all 5 tables)
php spark migrate

# 6. Start the development server
php spark serve
```

Visit [http://localhost:8080](http://localhost:8080)

> **Note:** PostgreSQL 17 must be running on localhost:5432. Connect with pgAdmin/DBeaver at `localhost:5432` user `postgres`.

Visit [http://localhost:8080](http://localhost:8080)

---

## Configuration

### Environment Variables (`.env`)

```env
# Application
CI_ENVIRONMENT = development
app.baseURL    = 'http://localhost:8080'

# Database
database.default.hostname = localhost
database.default.database = iaas_portal
database.default.username = postgres
database.default.password = postgres
database.default.DBDriver = Postgre
database.default.port     = 5432

# JWT
jwt.secret           = "your-very-long-random-secret-key"
jwt.access_token_ttl  = 3600        # 1 hour
jwt.refresh_token_ttl  = 2592000     # 30 days

# MiniStack (optional — mock used if unavailable)
ministack.base_url = 'http://localhost:5000/api/v1'
ministack.timeout  = 10
```

### API Response Envelope

Every API response follows this consistent JSON envelope:

```json
{
  "status":  "success",
  "message": "User profile.",
  "data":    { ... },
  "errors":  null,
  "meta":    { ... }       // only on paginated endpoints
}
```

Error responses:

```json
{
  "status":  "error",
  "message": "Validation failed.",
  "data":    null,
  "errors":  { "email": "Invalid email format." }
}
```

---

## API Documentation

**Base URL:** `http://localhost:8080/api/v1`

**Authentication:** All protected endpoints require:
```
Authorization: Bearer <access_token>
```

### Auth Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| `POST` | `/api/v1/auth/register` | No | Register account |
| `POST` | `/api/v1/auth/login` | No | Login, get tokens |
| `POST` | `/api/v1/auth/refresh` | No | Refresh access token |
| `POST` | `/api/v1/auth/logout` | Yes | Logout |

#### Register
```bash
POST /api/v1/auth/register
Content-Type: application/json

{
  "username":         "johndoe",
  "email":            "john@example.com",
  "password":         "SecurePass123",
  "confirm_password": "SecurePass123"
}
```
**Response (201):**
```json
{ "status": "success", "message": "Registration successful.", "data": { "user_id": 1 }, "errors": null }
```

#### Login
```bash
POST /api/v1/auth/login
Content-Type: application/json

{ "email": "john@example.com", "password": "SecurePass123" }
```
**Response (200):**
```json
{
  "status":  "success",
  "message": "Login successful.",
  "data":    {
    "access_token":  "eyJ...",
    "refresh_token": "eyJ...",
    "token_type":    "bearer",
    "expires_in":    3600
  }
}
```

### User Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| `GET` | `/api/v1/user/me` | Yes | Current user profile |
| `GET` | `/api/v1/user/subscription` | Yes | Subscription details |
| `POST` | `/api/v1/user/subscription` | Yes | Select/change tier |
| `GET` | `/api/v1/user/subscription/tiers` | Yes | All available tiers |

#### Set Subscription
```bash
POST /api/v1/user/subscription
Authorization: Bearer <token>
Content-Type: application/json

{ "tier": "pro" }
```
**Response (200):**
```json
{
  "status":  "success",
  "message": "Subscription set successfully.",
  "data":    { "subscription": { "tier": "pro", "status": "active", "quota_gb": 50, "price_usd": 9.99 } }
}
```

### Storage Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| `GET` | `/api/v1/storage` | Yes | Get quota & usage |
| `POST` | `/api/v1/storage/rent` | Yes | Rent additional storage |

#### Get Storage
```bash
GET /api/v1/storage
Authorization: Bearer <token>
```
**Response (200):**
```json
{
  "status":  "success",
  "message": "OK",
  "data":    {
    "base_quota_gb":    50,
    "rented_gb":        15,
    "total_quota_gb":   65,
    "used_gb":          12,
    "remaining_gb":     53,
    "usage_percent":     18.46,
    "subscription_tier": "pro"
  }
}
```

#### Rent Storage
```bash
POST /api/v1/storage/rent
Authorization: Bearer <token>
Content-Type: application/json

{ "amount_gb": 10 }
```
**Response (200):**
```json
{
  "status":  "success",
  "message": "Storage rental successful.",
  "data":    { "amount_gb": 10, "cost_usd": 1.00, "new_total_quota_gb": 75 }
}
```

### Credentials Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| `GET` | `/api/v1/credentials` | Yes | Get masked AK/SK |
| `POST` | `/api/v1/credentials/regenerate` | Yes | Regenerate keys |

#### Get Credentials
```bash
GET /api/v1/credentials
Authorization: Bearer <token>
```
**Response (200):**
```json
{
  "status":  "success",
  "message": "OK",
  "data":    {
    "access_key":        "AK•••••••••••••••XXXX",
    "secret_key":        "SK•••••••••••••••XYZ",
    "bucket_name":       "user_1_20250115000000",
    "created_at":        "2025-01-15T10:00:00Z",
    "last_regenerated":  null
  }
}
```

### Activity Logs Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| `GET` | `/api/v1/logs` | Yes | Paginated activity logs |

#### Get Logs
```bash
GET /api/v1/logs?page=1&limit=20&action_type=storage_rented
Authorization: Bearer <token>
```
**Response (200):**
```json
{
  "status":  "success",
  "message": "Activity logs retrieved.",
  "data": [
    {
      "id":           1,
      "action":       "storage_rented",
      "action_label": "Storage Rented",
      "details":      "Rented 10 GB additional storage at $0.10/GB",
      "status":      "completed",
      "created_at":  "2025-01-15T12:00:00Z"
    }
  ],
  "errors": null,
  "meta": {
    "current_page": 1,
    "per_page":     20,
    "total":        45,
    "page_count":   3
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|---|---|
| `200` | Success |
| `201` | Created |
| `204` | No content |
| `400` | Bad request |
| `401` | Unauthorized (invalid/missing token) |
| `403` | Forbidden |
| `404` | Not found |
| `409` | Conflict (duplicate email/username) |
| `422` | Validation error |
| `500` | Server error |

---

## Database Schema

### 5 Tables Created by Migrations

```
users ─────────────────────────────┐
  id (PK, auto_increment)        │
  username (unique, 50)           │   subscriptions ────────────────► users(id)
  email (unique, 255)             │     user_id (FK, unique)       │
  password_hash                   │     tier (enum: free/pro/      │
  created_at                     │       enterprise)               │
  updated_at                     │     status (active/cancelled/  │
                                 │       expired)                │
                                 │     start_date                │
                                 │     end_date                  │
                                 └───────────────────────────────┘

storage_rentals ────────────────► users(id)
  id                             │   user_credentials ────────────► users(id)
  user_id (FK)                   │     user_id (FK, unique)        │
  gb_amount (INT)                │     access_key (unique)         │
  price_per_gb (DECIMAL)         │     secret_key                  │
  created_at                     │     bucket_name (unique)        │
                                 │     created_at                 │
                                 │     last_regenerated            │
                                 └─────────────────────────────────┘

activity_logs ────────────────► users(id)
  id                             │
  user_id (FK, indexed)          │
  action (enum: 8 types)        │
  details (TEXT)                 │
  status (completed/failed/       │
    pending)                     │
  created_at (indexed)           │
```

### Indexes for 50k+ Scalability

| Table | Index | Purpose |
|---|---|---|
| `users` | `idx_users_email` | Fast email lookup on login |
| `users` | `idx_users_username` | Fast username lookup |
| `subscriptions` | `idx_subscriptions_user_id` | User subscription lookup |
| `subscriptions` | `idx_subscriptions_status` | Filter active subs |
| `storage_rentals` | `idx_storage_rentals_user_id` | User rentals |
| `storage_rentals` | `idx_storage_rentals_created_at` | Recent rentals first |
| `user_credentials` | `idx_user_credentials_access_key` | Key lookup |
| `activity_logs` | `idx_activity_logs_user_id` | User logs |
| `activity_logs` | `idx_activity_logs_action` | Filter by action |
| `activity_logs` | `idx_activity_logs_created_at` | Recent logs first |

---

## Running the Application

```bash
# Start development server
php spark serve
# → http://localhost:8080

# Run migrations
php spark migrate

# Rollback last migration
php spark migrate:rollback

# Seed the database
php spark db:seed SeedName

# Run tests
php spark test

# List all routes
php spark routes
```

### MiniStack Note

MiniStack is optional. If the MiniStack API is unavailable at `localhost:5000`, the portal automatically falls back to mock credential generation — all other features work normally.

---

## Activity Log Actions

| Action | Triggered By |
|---|---|
| `user_registered` | New account registration |
| `subscription_selected` | First subscription tier selection |
| `subscription_changed` | Tier upgrade/downgrade |
| `storage_rented` | Additional storage rented |
| `credentials_generated` | AK/SK created on registration |
| `credentials_regenerated` | AK/SK regenerated by user |
| `login` | Successful login |
| `logout` | Logout |

---

*Built with CodeIgniter 4.7 — Production-ready MVP*
