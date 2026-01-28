<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $executable
 * @property string $description
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|SecurityCheck newModelQuery()
 * @method static Builder<static>|SecurityCheck newQuery()
 * @method static Builder<static>|SecurityCheck query()
 * @method static Builder<static>|SecurityCheck whereCreatedAt($value)
 * @method static Builder<static>|SecurityCheck whereDescription($value)
 * @method static Builder<static>|SecurityCheck whereExecutable($value)
 * @method static Builder<static>|SecurityCheck whereId($value)
 * @method static Builder<static>|SecurityCheck whereStatus($value)
 * @method static Builder<static>|SecurityCheck whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SecurityCheck extends Model
{
    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'executable',
        'description',
        'status',
    ];

    /**
     * Update an existing security check status or create it if missing.
     */
    public static function check(string $executable, bool $currentStatus): self
    {
        $descriptionMap = [
            'clamav' => 'open-source antivirus engine',
            'ghostscript' => 'Ghostscript PDF sanitizer',
        ];

        $description = $descriptionMap[$executable] ?? ucfirst($executable);

        $cacheKey = "security_check:$executable";

        if ($cached = Cache::get($cacheKey)) {
            if (isset($cached['status']) && isset($cached['description'])) {
                if ($cached['status'] === $currentStatus && $cached['description'] === $description) {
                    $model = new self($cached);
                    $model->exists = isset($cached['id']);

                    return $model;
                }
            }
        }

        $model = self::firstWhere('executable', $executable);

        if ($model) {
            if ($model->status === $currentStatus && $model->description === $description) {
                Cache::put($cacheKey, $model->toArray(), now()->addHour());

                return $model;
            }

            $model->status = $currentStatus;
            $model->description = $description;
            $model->save();
            Cache::put($cacheKey, $model->toArray(), now()->addHour());

            return $model;
        }

        $model = self::create([
            'executable' => $executable,
            'description' => $description,
            'status' => $currentStatus,
        ]);

        Cache::put($cacheKey, $model->toArray(), now()->addHour());

        return $model;
    }
}
