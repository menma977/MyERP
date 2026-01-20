---
trigger: always_on
---

# Laravel Development Reference Guide

This document serves as a reference guide for Laravel development in this project. For detailed guidelines on specific topics, refer to the corresponding files in the `laravel/` directory.
Any of your actions must follow PHP Stan level 8 or above to provide standard code.
Don`t use any kind of in line comment like '//', this kind of thing is forbidden.
Don`t use single variable like $a,$b,$q, etc. this kind of thing is forbidden.
IF not ask you to make something, then stop and don't do any action, like example: I ask you to create a purchase, but I do not ask you to make a purchase component, then don't make a purchase
component.

## Step of work

1. read guidelines.
2. execute the command given by the user.
3. run command: vendor\bin\phpstan analyze file_path
4. if it has an error, fix it.
5. repeat until there are no more errors.

## Quick Reference

### When working with Controllers

**Reference:** [`laravel/controller.md`](laravel/controller.md)

- Controller structure and patterns
- Validation implementation
- Response formatting
- PHPStan documentation requirements

### When working with Models

**Reference:** [`laravel/model.md`](laravel/model.md)

- Model structure and conventions
- Relationship definitions
- Trait usage
- Observer implementation

### When defining Routes

**Reference:** [`laravel/route.md`](laravel/route.md)

- Route organization
- Explicit route definitions
- Middleware application
- API versioning

### When working with Database

**Reference:** [`laravel/database.md`](laravel/database.md)

- Migration structure
- Table design
- Seeding
- Query optimization

### When implementing Validation

**Reference:** [`laravel/rule.md`](laravel/rule.md)

- Validation rules
- Custom validation
- Error handling
- ValidationWithoutTrashed usage

### When designing REST APIs

**Reference:** [`laravel/rest-api.md`](laravel/rest-api.md)

- Endpoint naming conventions
- HTTP methods
- Response structures
- API versioning

### When formatting JSON Responses

**Reference:** [`laravel/json-response.md`](laravel/json-response.md)

- Response structure
- Success/error formats
- Status codes
- Response helpers

### When ensuring Security

**Reference:** [`laravel/security.md`](laravel/security.md)

- Authentication
- Authorization
- Input validation
- Data protection

### When optimizing Performance

**Reference:** [`laravel/performance.md`](laravel/performance.md)

- Database optimization
- Caching strategies
- Memory management
- Queue usage

### When using PHPStan

**Reference:** [`laravel/phpstan.md`](laravel/phpstan.md)

- Static analysis
- Error resolution
- Configuration
- Best practices

### When working with Modules

**Reference:** [`laravel/module-system.md`](laravel/module-system.md)

- Module structure
- Development workflow
- Inter-module communication
- Module commands

### When structuring the Project

**Reference:** [`laravel/project-structure.md`](laravel/project-structure.md)

- Directory organization
- File naming conventions
- Module structure
- Best practices

### When implementing DevOps

**Reference:** [`laravel/devops.md`](laravel/devops.md)

- Technology stack
- Git workflow
- Deployment
- Monitoring

### When using Docker

**Reference:** [`laravel/docker.md`](laravel/docker.md)

- Configuration
- Commands
- Best practices
- Troubleshooting

### When following Best Practices

**Reference:** [`laravel/best-practices.md`](laravel/best-practices.md)

- Code organization
- Error handling
- Testing
- Documentation

## Key Project Standards

### Core Philosophy

- **No API Resource Routes** - Use explicit route definitions
- **No FormRequest classes** - All validation in controllers
- **No API Resource classes** - Return JSON manually
- **Full Controller Flow** - Validate → Logic → Response

### Required Workflow

1. Always run PHPStan before committing
2. Follow PSR-12 coding standards
3. Use proper type hints and documentation
4. Validate all inputs
5. Return consistent JSON responses

### Module Structure

Each module should contain:

- Controllers (with inline validation)
- Models (with proper traits and relationships)
- Routes (explicit definitions)
- Migrations (following naming conventions)
- Tests (for critical functionality)

## Common Tasks

### Creating a New Controller

1. Reference: [`laravel/controller.md`](laravel/controller.md)
2. Follow the standard controller structure
3. Implement all CRUD operations
4. Add proper PHPDoc documentation
5. Include validation rules

### Creating a New Model

1. Reference: [`laravel/model.md`](laravel/model.md)
2. Use proper traits (CreatedByTrait, etc.)
3. Define relationships
4. Add scopes for common queries
5. Document properties with PHPDoc

### Creating API Routes

1. Reference: [`laravel/route.md`](laravel/route.md)
2. Use explicit route definitions
3. Group-related routes
4. Apply appropriate middleware
5. Follow naming conventions

### Creating Migrations

1. Reference: [`laravel/database.md`](laravel/database.md)
2. Follow naming conventions
3. Include standard columns
4. Add proper indexes
5. Implement both up() and down() methods

## Quick Links

- [Controller Guidelines](laravel/controller.md)
- [Model Guidelines](laravel/model.md)
- [Route Guidelines](laravel/route.md)
- [Database Guidelines](laravel/database.md)
- [Validation Guidelines](laravel/rule.md)
- [REST API Guidelines](laravel/rest-api.md)
- [JSON Response Guidelines](laravel/json-response.md)
- [Security Guidelines](laravel/security.md)
- [Performance Guidelines](laravel/performance.md)
- [PHPStan Guidelines](laravel/phpstan.md)
- [Module System Guidelines](laravel/module-system.md)
- [Project Structure Guidelines](laravel/project-structure.md)
- [DevOps Guidelines](laravel/devops.md)
- [Docker Guidelines](laravel/docker.md)
- [Best Practices](laravel/best-practices.md)
