<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
</p>

# MyERP - Enterprise Resource Planning System (BACKEND ONLY)

A comprehensive ERP system built with Laravel 12, designed to streamline business operations including purchasing, inventory management, sales, and financial transactions.

## 🚀 Features

### Core Modules

- **Authentication & Authorization**
    - Laravel Sanctum for API authentication
    - Spatie Laravel Permission for role-based access control
    - Multi-level user roles and permissions

- **Purchase Management**
    - Purchase Requests with approval workflows
    - Purchase Orders and Procurement management
    - Purchase Invoices and Returns
    - Complete audit trail with soft deletes

- **Inventory Management**
    - Item master data management
    - Stock tracking with batch management
    - Good Receipts and Goods Issue
    - Real-time stock history

- **Vendor Management**
    - Vendor profile management
    - Account payable tracking
    - Invoice and payment processing

- **Transaction Management**
    - Payment requests and processing
    - Ledger management
    - Financial transaction tracking

- **Approval Workflows**
    - Configurable approval flows
    - Multi-step approval processes
    - Event-driven approval system
    - Contributor-based approvals

### Technical Features

- **Modular Architecture**
    - Clean separation of concerns
    - Independent module development
    - Scalable and maintainable codebase

- **API-First Design**
    - RESTful API endpoints
    - Consistent JSON responses
    - Comprehensive error handling
    - API versioning support

- **Data Integrity**
    - Comprehensive validation
    - Database constraints
    - Soft delete support
    - Audit logging with activity tracking

- **Performance Optimized**
    - Eager loading to prevent N+1 queries
    - Database indexing
    - Query optimization
    - Caching strategies

## 🛠️ Technology Stack

### Backend

- **Framework**: Laravel 12.0
- **PHP Version**: 8.4+
- **Database**: MySQL 8.0
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission

### Development Tools

- **Static Analysis**: PHPStan (Level 8)
- **Code Style**: Laravel Pint
- **Testing**: PHPUnit 11.x
- **Debugging**: Laradumps
- **IDE Support**: Laravel IDE Helper

## 📋 Requirements

- PHP 8.4 or higher
- MySQL 8.0 or higher
- Composer 2.0 or higher
- Node.js 18.0 or higher
- Git

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone git@github.com:menma977/MyERP.git
cd MyERP
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myerp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Build Assets

```bash
npm run build
```

### 7. Start Development Server

```bash
composer run dev
```

## 🏗️ Project Structure

```
MyERP/
├── app/
│   ├── Http/Controllers/          # API Controllers
│   │   ├── Purchases/           # Purchase management
│   │   ├── Items/              # Inventory management
│   │   ├── Transactions/       # Financial transactions
│   │   ├── Approvals/          # Approval workflows
│   │   └── Auth/               # Authentication
│   ├── Models/                  # Eloquent Models
│   │   ├── Purchases/          # Purchase models
│   │   ├── Items/              # Inventory models
│   │   ├── Transactions/       # Transaction models
│   │   ├── Approvals/          # Approval models
│   │   └── Vendors/           # Vendor models
│   ├── Services/               # Business logic services
│   ├── Traits/                 # Reusable traits
│   ├── Observers/             # Model observers
│   └── Rules/                 # Custom validation rules
├── database/
│   ├── migrations/             # Database migrations
│   └── factories/             # Model factories
├── routes/
│   └── api/v1/              # API routes by module
```

## 🔧 Development Guidelines

### Code Standards

- Follow PSR-12 coding standards
- Use PHPStan Level 8 for static analysis
- Apply Laravel Pint for code formatting
- Write comprehensive PHPDoc documentation

### API Development

- Use explicit route definitions (no resource routes)
- Implement inline validation in controllers
- Return consistent JSON responses
- Include proper HTTP status codes

### Database Design

- Use descriptive table names (snake_case, plural)
- Include audit fields (created_by, updated_by, deleted_by)
- Implement soft deletes for data retention
- Add proper indexes for performance

### Testing

- Write feature tests for API endpoints
- Test validation rules separately
- Use database transactions in tests
- Mock external dependencies

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api/v1
```

### Authentication

All API endpoints require authentication using Laravel Sanctum tokens.

### Standard Endpoints

Each module follows the standard REST API pattern:

- `GET /index` - List all resources
- `GET /show/{id}` - Get specific resource
- `POST /store` - Create new resource
- `PUT /update/{id}` - Update resource
- `DELETE /delete/{id}` - Soft delete resource
- `POST /restore/{id}` - Restore soft deleted resource
- `DELETE /destroy/{id}` - Permanently delete resource

### Workflow Endpoints

Approval-enabled modules include additional endpoints:

- `POST /approve/{id}` - Approve request
- `POST /reject/{id}` - Reject request
- `POST /cancel/{id}` - Cancel pending request
- `POST /rollback/{id}` - Rollback completed request
- `POST /force/{id}` - Force execute action

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test

```bash
php artisan test --filter PurchaseRequestTest
```

### Generate Coverage Report

```bash
php artisan test --coverage
```

## 🔍 Static Analysis

### Run PHPStan

```bash
./vendor/bin/phpstan analyse
```

### Fix Code Style

```bash
./vendor/bin/pint
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and static analysis
5. Submit a pull request

### Development Workflow

1. Follow the coding standards defined in `.kilocode/rules/`
2. Ensure all PHPStan errors are resolved
3. Write tests for new functionality
4. Update documentation as needed

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🗺️ Roadmap

### Upcoming Features

- [ ] Sales Management Module
- [ ] Advanced Reporting Dashboard
- [ ] Multi-tenant Support
- [ ] Real-time Notifications
- [ ] Mobile API Support

### Technical Improvements

- [ ] API Rate Limiting
- [ ] Advanced Caching
- [ ] Queue System Implementation
- [ ] Performance Monitoring
- [ ] Security Enhancements
