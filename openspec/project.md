# Project Context

## Purpose

MyERP is a comprehensive Enterprise Resource Planning (ERP) system designed to streamline business operations. The system manages core business processes including inventory management, purchase and
sales workflows, financial transactions, vendor relationships, and approval processes. The backend provides a RESTful API for managing these business domains with proper authentication, authorization,
and audit trails.

## Tech Stack

- Framework: Laravel 12
- PHP: 8.4+
- Database: MySQL
- Authentication: Laravel Sanctum
- Authorization/RBAC: Spatie Laravel Permission
- Queue: database driver
- Cache/Session: database
- Testing: Pest (pestphp/pest)
- Code Quality & Dev Tools: Laravel Pint, PHPStan/Larastan, barryvdh/laravel-ide-helper, Laradumps, Laravel Pail
- Local Dev Environment: Laravel Sail (optional)

## Project Conventions

### Code Style

- Follow Laravel's standard coding conventions with PSR-4 autoloading
- Use explicit variable names (no single-letter variables)
- No inline comments like `//` - use proper PHPDoc blocks
- Controllers handle validation, business logic, and JSON responses
- Models use traits for audit tracking (CreatedByTrait, UpdatedByTrait, DeletedByTrait)
- Services are used for complex business logic
- Enums are used for standardized values (ApprovalStatus, ApprovalType, etc.)

### Architecture Patterns

- Domain-driven design with models organized by business domain (Approval, Items, Purchases, Sales, Transactions, Vendors)
- Explicit routing without using `Route::apiResource()` or `Route::resource()`
- Controller-based validation using `Validator::make()` instead of FormRequest classes
- Manual JSON responses instead of API Resource classes
- Repository pattern used when database access becomes repetitive
- Observer pattern for automatic audit trail management
- Abstract base classes for common model functionality
- Service layer for complex business logic

### Testing Strategy

- Pest for unit and feature testing
- Test organization follows Laravel's standard structure
- Tests should cover all controller methods with various scenarios
- Service classes should have dedicated unit tests
- Model relationships and business rules should be tested
- Integration tests for complex workflows (e.g., purchase order to invoice)

### Git Workflow

- Feature branch workflow with descriptive branch names
- Commit messages should follow conventional commits format
- Pull requests required for code review before merging
- All tests must pass before merging
- Code must pass PHPStan analysis before merging

## Domain Context

### Business Domains

1. **Approval System**: Manages approval workflows, groups, and processes for other business domains
2. **Items**: Handles inventory management, stock tracking, batches, and bills of materials
3. **Purchases**: Manages purchase requests, procurements, orders, receipts, invoices, and returns
4. **Sales**: Handles sales orders, invoices, goods issues, and returns
5. **Transactions**: Manages financial ledgers and payment requests
6. **Vendors**: Manages vendor information, accounts payable, and payments

### Key Business Processes

- Purchase Request → Purchase Procurement → Purchase Order → Good Receipt → Purchase Invoice → Payment
- Sales Order → Sales Invoice → Good Issue → Payment
- Item management with stock tracking and batch control
- Multi-level approval workflows for various business processes
- Financial ledger management for all transactions

## Important Constraints

- All controllers must follow the validation → business logic → JSON response pattern
- Error handling must use exceptions, not manual error responses
- Authorization must use Spatie Permission with middleware or explicit checks
- No API Resource classes or FormRequest classes
- All routes must be explicitly defined, not using resource routing
- All changes must follow PHPStan rules and pass static analysis
- Audit trails must be maintained for all data changes

## External Dependencies

- Laravel Sanctum for API authentication
- Spatie Laravel Permission for role-based access control
- MySQL as the primary database
- Database drivers for queue, cache, and session management
- Frontend applications that consume the API (not part of this backend project)
