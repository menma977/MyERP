<?php

namespace App\Models;

use App\Http\Resources\PersonalAccessTokenResource;
use App\Models\Companies\Company;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * @property string $id
 * @property int|null $company_id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property array<array-key, mixed>|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 *
 * @method static Builder<static>|PersonalAccessToken newModelQuery()
 * @method static Builder<static>|PersonalAccessToken newQuery()
 * @method static Builder<static>|PersonalAccessToken query()
 * @method static Builder<static>|PersonalAccessToken whereAbilities($value)
 * @method static Builder<static>|PersonalAccessToken whereCreatedAt($value)
 * @method static Builder<static>|PersonalAccessToken whereExpiresAt($value)
 * @method static Builder<static>|PersonalAccessToken whereId($value)
 * @method static Builder<static>|PersonalAccessToken whereLastUsedAt($value)
 * @method static Builder<static>|PersonalAccessToken whereName($value)
 * @method static Builder<static>|PersonalAccessToken whereToken($value)
 * @method static Builder<static>|PersonalAccessToken whereTokenableId($value)
 * @method static Builder<static>|PersonalAccessToken whereTokenableType($value)
 * @method static Builder<static>|PersonalAccessToken whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[UseResource(PersonalAccessTokenResource::class)]
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasUlids;

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
