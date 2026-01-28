<?php

namespace App\Abstracts;

use App\Models\Scopes\WithCompanyScope;
use App\Observers\CreatedByObserver;
use App\Observers\DeletedByObserver;
use App\Observers\UpdatedByObserver;
use App\Traits\CreatedByTrait;
use App\Traits\DeletedByTrait;
use App\Traits\UpdatedByTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class]), ScopedBy([WithCompanyScope::class])]
abstract class ModelWithCompanyAbstract extends Model
{
    use CreatedByTrait, DeletedByTrait, UpdatedByTrait;

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    #[Scope]
    protected function withUsers(Builder $query): Builder
    {
        return $query->with([
            'createdBy',
            'updatedBy',
            'deletedBy',
        ]);
    }
}
