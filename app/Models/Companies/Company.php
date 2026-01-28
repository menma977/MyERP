<?php

namespace App\Models\Companies;

use App\Abstracts\ModelAbstract;
use App\Http\Resources\CompanyResource;
use App\Models\Customer\Customer;
use App\Models\FileBucket;
use App\Models\User;
use Database\Factories\Companies\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $ulid
 * @property string $name
 * @property string $code
 * @property string|null $phone
 * @property string $email
 * @property string|null $website
 * @property string|null $address
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Companies\CompanyHasUser> $hasUsers
 * @property-read int|null $has_users_count
 * @property-read FileBucket|null $logo
 * @property-read User|null $updatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static CompanyFactory factory($count = null, $state = [])
 * @method static Builder<static>|Company newModelQuery()
 * @method static Builder<static>|Company newQuery()
 * @method static Builder<static>|Company onlyTrashed()
 * @method static Builder<static>|Company query()
 * @method static Builder<static>|Company whereAddress($value)
 * @method static Builder<static>|Company whereCode($value)
 * @method static Builder<static>|Company whereCompanyId($value)
 * @method static Builder<static>|Company whereCreatedAt($value)
 * @method static Builder<static>|Company whereCreatedBy($value)
 * @method static Builder<static>|Company whereDeletedAt($value)
 * @method static Builder<static>|Company whereDeletedBy($value)
 * @method static Builder<static>|Company whereEmail($value)
 * @method static Builder<static>|Company whereId($value)
 * @method static Builder<static>|Company whereName($value)
 * @method static Builder<static>|Company wherePhone($value)
 * @method static Builder<static>|Company whereUlid($value)
 * @method static Builder<static>|Company whereUpdatedAt($value)
 * @method static Builder<static>|Company whereUpdatedBy($value)
 * @method static Builder<static>|Company whereWebsite($value)
 * @method static Builder<static>|Company withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Company withUsers()
 * @method static Builder<static>|Company withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[UseResource(CompanyResource::class)]
class Company extends ModelAbstract
{
    /** @use HasFactory<\Database\Factories\Companies\CompanyFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ulid',
        'name',
        'code',
        'phone',
        'email',
        'website',
        'address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    /**
     * @return HasMany<CompanyHasUser, $this>
     */
    public function hasUsers(): HasMany
    {
        return $this->hasMany(CompanyHasUser::class, 'company_id');
    }

    /**
     * @return HasManyThrough<User, CompanyHasUser, $this>
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, CompanyHasUser::class, 'company_id', 'id', 'id', 'user_id');
    }

    /**
     * @return HasOne<Customer, $this>
     */
    public function customerDefault(): HasOne
    {
        return $this->hasOne(Customer::class, 'company_id')->where('is_default', true)->withDefault(['name' => 'No Default Customer']);
    }

    /**
     * @return HasMany<Customer, $this>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'company_id')->where('is_default', false);
    }

    /**
     * @return MorphOne<FileBucket, $this>
     */
    public function logo(): MorphOne
    {
        return $this->morphOne(FileBucket::class, 'model', 'model_type', 'model_id');
    }
}
