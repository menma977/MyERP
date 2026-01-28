<?php

namespace App\Models\Companies;

use App\Abstracts\ModelWithCompanyAbstract;
use App\Http\Resources\CompanyResource;
use App\Models\FileBucket;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseResource(CompanyResource::class)]
class Company extends ModelWithCompanyAbstract
{
    use HasUlids, SoftDeletes;

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
     * @return MorphOne<FileBucket, $this>
     */
    public function logo(): MorphOne
    {
        return $this->morphOne(FileBucket::class, 'model', 'model_type', 'model_id');
    }
}
