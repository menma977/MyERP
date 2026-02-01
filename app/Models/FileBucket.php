<?php

namespace App\Models;

use App\Abstracts\ModelWithCompanyAbstract;
use App\Http\Resources\FileBucketResource;
use Database\Factories\FileBucketFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Represents a file stored in the system.
 *
 * @property string $id
 * @property int|null $company_id
 * @property string $model_type
 * @property string $model_id
 * @property string $name
 * @property string|null $path
 * @property string|null $mime_type
 * @property string|null $mime
 * @property string|null $extension
 * @property numeric $size
 * @property Collection<string, mixed>|null $data
 * @property Collection<int, string>|null $tags
 * @property string|null $finished_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Model $model
 * @property-read \App\Models\User|null $updatedBy
 * @property-read mixed $url
 *
 * @method static FileBucketFactory factory($count = null, $state = [])
 * @method static Builder<static>|FileBucket newModelQuery()
 * @method static Builder<static>|FileBucket newQuery()
 * @method static Builder<static>|FileBucket onlyTrashed()
 * @method static Builder<static>|FileBucket query()
 * @method static Builder<static>|FileBucket whereCompanyId($value)
 * @method static Builder<static>|FileBucket whereCreatedAt($value)
 * @method static Builder<static>|FileBucket whereCreatedBy($value)
 * @method static Builder<static>|FileBucket whereData($value)
 * @method static Builder<static>|FileBucket whereDeletedAt($value)
 * @method static Builder<static>|FileBucket whereDeletedBy($value)
 * @method static Builder<static>|FileBucket whereExtension($value)
 * @method static Builder<static>|FileBucket whereFinishedAt($value)
 * @method static Builder<static>|FileBucket whereId($value)
 * @method static Builder<static>|FileBucket whereMime($value)
 * @method static Builder<static>|FileBucket whereMimeType($value)
 * @method static Builder<static>|FileBucket whereModelId($value)
 * @method static Builder<static>|FileBucket whereModelType($value)
 * @method static Builder<static>|FileBucket whereName($value)
 * @method static Builder<static>|FileBucket wherePath($value)
 * @method static Builder<static>|FileBucket whereSize($value)
 * @method static Builder<static>|FileBucket whereTags($value)
 * @method static Builder<static>|FileBucket whereUpdatedAt($value)
 * @method static Builder<static>|FileBucket whereUpdatedBy($value)
 * @method static Builder<static>|FileBucket withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|FileBucket withUsers()
 * @method static Builder<static>|FileBucket withoutTrashed()
 *
 * @mixin Eloquent
 */
#[UseResource(FileBucketResource::class)]
class FileBucket extends ModelWithCompanyAbstract
{
    /** @use HasFactory<FileBucketFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'model_type',
        'model_id',
        'name',
        'path',
        'mime_type',
        'mime',
        'extension',
        'size',
        'data',
        'tags',
        'finished_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'size' => 'decimal:4',
            'tags' => AsCollection::class,
            'data' => AsCollection::class,
        ];
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->path ? Storage::url($this->path) : null,
        );
    }
}
