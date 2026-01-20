---
trigger: always_on
---

# Laravel Development Rules & Best Practices

## 1. Core Philosophy (STRICT)

-   **No API Resource Routes**: Never use `Route::apiResource()` or `Route::resource()`. Use explicit routes.
-   **No FormRequest Classes**: All validation lives **directly in the controller** via `$request->validate()`.
-   **Encapsulated Logic**: Controllers handle validation, business logic (or service delegation), and response formatting.
-   **Standard Actions**: Controllers and routes must support: `index`, `show`, `store`, `update`, `delete` (soft), `restore`, `destroy` (force).
-   **PHPStan Level 8**: Strict type safety, explicit return types in signatures, and detailed PHPDoc (including array shapes) are mandatory.
-   **No Inline Comments**: Do not use inline comments (`// ...`) in the code. content should be self-explanatory.

---

## 2. Controller Guidelines

### Structure & Standards

-   **Methods**: Implement the 7 standard actions.
-   **Return Types**:
    -   Must be declared in method signature.
    -   PHPDoc must specify exact types (e.g., `Collection<int, User>`, `array{message: string}`).
-   **Response**: Return arrays or Collections/Models directly; framework handles JSON conversion.

### Example Controller

```php
class CompanyController extends Controller
{
    /**
     * @return LengthAwarePaginator<int, Company>|Collection<int, Company>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $companies = Company::when($request->input('search'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->input('search') . '%');
        })->orderBy('id', 'desc');

        return $request->input('type') === 'collection'
            ? $companies->get()
            : $companies->paginate($request->input('per_page', 10));
    }

    /**
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Company::class, 'name')],
            'code' => ['required', 'string', 'max:255', new ValidationWithoutTrashed(Company::class, 'code')],
        ]);

        $company = new Company();
        $company->name = $request->input('name');
        $company->code = $request->input('code');
        $company->save();

        return ['message' => trans('messages.success.store', ['target' => 'Company'])];
    }
}
```

---

## 3. Model Guidelines

### Naming & Structure

-   **Tables**: snake_case, plural (e.g., `user_profiles`).
-   **Models**: PascalCase, singular (e.g., `UserProfile`).
-   **Directory**: `Modules/{Module}/App/Models`.

### Requirements

1.  **Traits**:
    -   `CreatedByTrait`, `UpdatedByTrait`, `DeletedByTrait` (Audit fields).
    -   `HasUlids` (IDs), `SoftDeletes`.
    -   `LogsActivity` / `UseActivityLog`.
2.  **Observers**: Register via `#[ObservedBy([...])]` attribute.
3.  **Attributes**:
    -   `$fillable`: Must be defined (prevent mass assignment).
    -   `$casts`: Standardize types (`datetime`, `boolean`, `decimal:2`).
4.  **Relationships**: Explicitly defined with return types (e.g., `BelongsTo`).

### Example Model

```php
#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
class YourModel extends Model
{
    use CreatedByTrait, UpdatedByTrait, DeletedByTrait, HasUlids, SoftDeletes;

    protected $fillable = ['code', 'name', 'is_active', 'created_by', 'updated_by', 'deleted_by'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime'
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## 4. Route Guidelines

### Standards

-   **Explicit Routes**: Define every route individually.
-   **Naming**: `module.submodule.action` (e.g., `purchase.request.store`).
-   **Structure**: Group by prefix and name.
-   **Middleware**: Apply at group level or route level using canonical names (e.g., `can:permission.name`).

### Standard Route Set

```php
Route::prefix('user')->name('user.')->group(function () {
    Route::get('index', [UserController::class, 'index'])->name('index');
    Route::get('show/{id}', [UserController::class, 'show'])->name('show');
    Route::post('store', [UserController::class, 'store'])->name('store');
    Route::put('update/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [UserController::class, 'delete'])->name('delete');
    Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
    Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('approve/{id}', [UserController::class, 'approve'])->name('approve');
    Route::post('reject/{id}', [UserController::class, 'reject'])->name('reject');
});
```

---

## 5. Validation Rules

### Policy

-   **Location**: Inside controller methods only.
-   **Method**: `$request->validate([...])`.
-   **Unique Check**: Use `ValidationWithoutTrashed` to ignore soft-deleted records.

### Patterns

-   **Create**:
    ```php
    'code' => ['required', new ValidationWithoutTrashed(Model::class, 'code')]
    ```
-   **Update** (ignore current ID):
    ```php
    'code' => ['required', new ValidationWithoutTrashed(Model::class, 'code', $request->route('id'))]
    ```
-   **Common Rules**:
    -   `string`, `max:255`
    -   `numeric`, `min:0`
    -   `date_format:H:i:s`
    -   `exists:table,id`

---

## 6. General Best Practices

### Code Quality

-   **Service Classes**: Extract complex logic from controllers to Services.
-   **N+1 Prevention**: Always use eager loading (`with()`).
-   **Strict Typing**: Type hint all parameters and return values.
-   **Linting**: Run PHPStan and Laravel Pint before committing.

### Differences from Standard Laravel

| Feature             | Project Rule                                          |
| :------------------ | :---------------------------------------------------- |
| **Route::resource** | ❌ **FORBIDDEN** - Use explicit routes                |
| **FormRequest**     | ❌ **FORBIDDEN** - Use inline `$request->validate()`  |
| **API Resources**   | ❌ **FORBIDDEN** - Return arrays/models directly      |
| **Controller**      | **Full Responsibility** (Validate -> Logic -> Return) |
