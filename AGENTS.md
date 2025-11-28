# Development Guidelines (Custom API Style)

## Project Overview

### About MyERP Backend

This is a Laravel 12-based backend for MyERP. The application uses explicit routing and controller-based validation with a simple, conventional Laravel directory structure (no external module system).

### Technology Stack

- Framework: Laravel 12
- PHP: 8.4+
- Database: MySQL
- Authentication: Laravel Sanctum
- Authorization/RBAC: Spatie Laravel Permission
- Queue: database driver
- Cache/Session: database
- Testing: PHPUnit (phpunit/phpunit)
- Code Quality & Dev Tools: Laravel Pint, PHPStan/Larastan, barryvdh/laravel-ide-helper, Laradumps, Laravel Pail
- Local Dev Environment: Laravel Sail (optional)

Not used in this project (unless explicitly added later):

- nwidart/laravel-modules
- Real-time (Reverb)
- API docs generators
- Document generation libraries

### Project Structure

Standard Laravel structure. API routes live under `routes/api/v1/` and are split by domain when desired (e.g., `routes/api/v1/vendors.php`, `routes/api/v1/transactions.php`).

Example relevant paths:

```
app/
├── Http/
│   └── Controllers/
routes/
└── api/
    └── v1/
        ├── api.php
        ├── vendors.php
        └── transactions.php
```

### Key Practices

- before do anything you must follow this guideline and follow phpstan rule
- Custom API style: prefer inline validation and manual JSON responses in controllers
- Explicit routes: avoid `apiResource()` for fine-grained control
- Keep controllers responsible for: validation → business logic (delegate to Services if complex) → JSON response
- Error handling in controllers MUST use exceptions (no `return response()` for errors) — use `ValidationException::withMessages([...])` for validation failures
- Authorization: use Spatie Permission roles/permissions via middleware (e.g., `role`, `permission`) or explicit checks; never return manual 403 JSON — throw proper exceptions per Error Handling
  Policy

---

## Core Philosophy

* **No API Resource Routes** – Never use `Route::apiResource()` or `Route::resource()`.
  Use explicit routes (`Route::get`, `Route::post`, etc.) for full control.

* **No FormRequest / Resource classes** – Validation and responses live **directly in the controller**.

* **Full Controller Flow** – Each controller method handles:

    1. Request validation
    2. Business logic (via Service if complex)
    3. JSON response

* **Dynamic Routing Standard** – Always supports these base actions:

  ```
  index, show, store, update, delete, restore, destroy
  ```

  `delete` → soft delete
  `restore` → undo soft delete
  `destroy` → permanent delete

---

## Routing Standard

### Example Route Set (routes/api/v1/users.php)

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('user')->name('user.')->group(function () {
    Route::get('index', [UserController::class, 'index']);
    Route::get('show/{id}', [UserController::class, 'show']);
    Route::post('store', [UserController::class, 'store']);
    Route::put('update/{id}', [UserController::class, 'update']);
    Route::delete('delete/{id}', [UserController::class, 'delete']);
});
```

> Each route serves one clear purpose.
> Avoid automatic route generation for clarity and auditability.

---

## Controller Guidelines

### Example: `UserController`

```php
<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * User Index
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::when($request->input('search'), function ($query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('email', 'like', '%' . $request->input('search') . '%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $users->get();
        }

        return $users->paginate($request->input('per_page', 10));
    }

    /**
     * User Store
     *
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return [
          'message' => 'User created successfully.'
        ];
    }

    /**
     * User Show
     *
     * Show the specified resource.
     */
    public function show(Request $request)
    {
        return User::findOrFail($request->route('id'));
    }

    /**
     * User Update
     *
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->route('id')],
        ]);

        $user = User::find($request->route('id'));
        if (!$user) {
            throw ValidationException::withMessages([
              'email' => ['Email already exists.'],
            ]);
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return [
          'message' => 'User updated successfully.'
        ];
    }

    /**
     * User Delete (soft delete if model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->route('id'));
        $user->delete();

        return [
          'message' => 'User deleted successfully.'
        ];
    }
}
```

---

## Project Structure (Simplified)

```
app/
├── Http/
│   └── Controllers/
├── Models/
routes/
└── api/
    └── v1/
        ├── api.php
        ├── vendors.php
        └── transactions.php
```

- No `Requests/`: Validation inline using `Validator::make()`
- No `Resources/`: Return JSON manually
- Services: Optional for complex business logic
- Repositories: Optional, only if database access is repetitive

---

## REST API Naming Convention

| Method | URL                       | Action | Description       |
|--------|---------------------------|--------|-------------------|
| GET    | /api/v1/users/index       | index  | List all users    |
| GET    | /api/v1/users/show/{id}   | show   | Get specific user |
| POST   | /api/v1/users/store       | store  | Create a new user |
| PUT    | /api/v1/users/update/{id} | update | Update user       |
| DELETE | /api/v1/users/delete/{id} | delete | Soft delete user  |

---

## Validation Policy

* Validate with `$request->validate()` inside controllers.
* Laravel will automatically convert validation failures into a 422 JSON response.
    - The response body should be `{ "errors": { ... } }`.
* Validation messages should be concise, e.g.:

  ```json
  {
    "errors": {
      "email": ["Email already exists."]
    }
  }
  ```

---

## Error Handling Policy

- Never use `return response()` (or any manual `response()->json(..., 4xx/5xx)`) for errors inside controllers.
- Always throw exceptions and let Laravel handle the HTTP response formatting:
    - Validation errors: `throw ValidationException::withMessages([...])`.
    - Not found: rely on `Model::findOrFail()` which throws `ModelNotFoundException` (becomes 404 JSON).
    - Forbidden/Unauthorized or other HTTP errors: throw appropriate `HttpException` (e.g., `abort(403)` which throws an exception under the hood).
- Success payloads may still return arrays/JSON manually per the JSON Response Policy.

---

## Authorization Policy (Spatie Permission)

- Roles and permissions are managed using Spatie Laravel Permission.
- Prefer route or controller middleware for access control:

  ```php
  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\Api\V1\UserController;

  Route::prefix('user')->name('user.')->middleware(['auth:sanctum', 'permission:users.view'])
      ->group(function () {
          Route::get('index', [UserController::class, 'index']);
      });
  ```

- Inside controllers, when necessary, you may assert permissions explicitly, but on failure follow Error Handling Policy (e.g., `abort(403)`):

  ```php
  if (! $request->user()->can('users.update')) {
      abort(403, 'Forbidden');
  }
  ```

- Do not return manual error responses for authorization failures; always throw exceptions.

---

## JSON Response Policy

Every endpoint should follow this pattern:

| Type                                | Example                                   |
|-------------------------------------|-------------------------------------------|
| ✅ **Success (Single)**              | `{ "data": { "id": 1, "name": "John" } }` |
| ✅ **Success (Collection)**          | `{ "data": [{...}, {...}] }`              |
| ❌ **No HTML, no Resource wrapping** | Never use Blade or API Resource           |
| ❌ **No Redirects**                  | Always respond with JSON                  |

---

## Summary of Deviations from Laravel Defaults

| Feature                 | Default Laravel | Junie Style                      |
|-------------------------|-----------------|----------------------------------|
| Resource Controllers    | ✅ Used          | ❌ Not used                       |
| FormRequest             | ✅ Used          | ❌ Inline validation only         |
| API Resource            | ✅ Used          | ❌ Return JSON manually           |
| `apiResource()` Routing | ✅ Used          | ❌ Explicit routes only           |
| Controller Layers       | Thin            | Full (validate → logic → return) |

---

## Example Route File Template (new domain)

When creating a new domain file under `routes/api/v1/xyz.php`:

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\XyzController;

Route::prefix('v1/xyz')->group(function () {
    Route::get('index', [XyzController::class, 'index']);
    Route::get('show/{id}', [XyzController::class, 'show']);
    Route::post('store', [XyzController::class, 'store']);
    Route::put('update/{id}', [XyzController::class, 'update']);
    Route::delete('delete/{id}', [XyzController::class, 'delete']);
});
```

---
