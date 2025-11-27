<?php

namespace App\Models\Transactions;

use App\Abstracts\ModelAbstract;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Represents a Ledger Component in the system.
 *
 * @property string $id
 * @property string $ledger_id
 * @property string $in
 * @property string $out
 * @property string $total
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read \App\Models\Transactions\Ledger $ledger
 * @property-read User|null $updatedBy
 * @method static Builder<static>|LedgerComponent newModelQuery()
 * @method static Builder<static>|LedgerComponent newQuery()
 * @method static Builder<static>|LedgerComponent onlyTrashed()
 * @method static Builder<static>|LedgerComponent query()
 * @method static Builder<static>|LedgerComponent whereCreatedAt($value)
 * @method static Builder<static>|LedgerComponent whereCreatedBy($value)
 * @method static Builder<static>|LedgerComponent whereDeletedAt($value)
 * @method static Builder<static>|LedgerComponent whereDeletedBy($value)
 * @method static Builder<static>|LedgerComponent whereId($value)
 * @method static Builder<static>|LedgerComponent whereIn($value)
 * @method static Builder<static>|LedgerComponent whereLedgerId($value)
 * @method static Builder<static>|LedgerComponent whereOut($value)
 * @method static Builder<static>|LedgerComponent whereTotal($value)
 * @method static Builder<static>|LedgerComponent whereUpdatedAt($value)
 * @method static Builder<static>|LedgerComponent whereUpdatedBy($value)
 * @method static Builder<static>|LedgerComponent withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|LedgerComponent withoutTrashed()
 * @mixin Eloquent
 */
class LedgerComponent extends ModelAbstract
{
	use HasUlids, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'ledger_id',
		'in',
		'out',
		'total',
		'created_by',
		'updated_by',
		'deleted_by',
		'deleted_at',
	];

	/**
	 * @return BelongsTo<Ledger, $this>
	 */
	public function ledger(): BelongsTo
	{
		return $this->belongsTo(Ledger::class);
	}
}
