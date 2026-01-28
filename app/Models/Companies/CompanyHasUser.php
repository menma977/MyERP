<?php

namespace App\Models\Companies;

use App\Abstracts\ModelWithCompanyAbstract;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property int $company_id
 * @property int $user_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Companies\Company $company
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 * @property-read User $user
 *
 * @method static Builder<static>|CompanyHasUser newModelQuery()
 * @method static Builder<static>|CompanyHasUser newQuery()
 * @method static Builder<static>|CompanyHasUser onlyTrashed()
 * @method static Builder<static>|CompanyHasUser query()
 * @method static Builder<static>|CompanyHasUser whereCompanyId($value)
 * @method static Builder<static>|CompanyHasUser whereCreatedAt($value)
 * @method static Builder<static>|CompanyHasUser whereCreatedBy($value)
 * @method static Builder<static>|CompanyHasUser whereDeletedAt($value)
 * @method static Builder<static>|CompanyHasUser whereDeletedBy($value)
 * @method static Builder<static>|CompanyHasUser whereId($value)
 * @method static Builder<static>|CompanyHasUser whereUpdatedAt($value)
 * @method static Builder<static>|CompanyHasUser whereUpdatedBy($value)
 * @method static Builder<static>|CompanyHasUser whereUserId($value)
 * @method static Builder<static>|CompanyHasUser withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|CompanyHasUser withUsers()
 * @method static Builder<static>|CompanyHasUser withoutTrashed()
 *
 * @mixin \Eloquent
 */
class CompanyHasUser extends ModelWithCompanyAbstract
{
    use HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
