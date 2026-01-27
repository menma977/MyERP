<?php

namespace App\Models\Scopes;

use App\Models\User;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ApprovalAbstractScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder<Model>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        $request = request();
        $isAll = $request->boolean('all') && Auth::user()?->canAny(['user.super.index', 'user.super.developer.index']);

        $userId = $request->input('user_id');
        if (! is_string($userId) && ! is_int($userId)) {
            $userId = null;
        }

        $userIdKey = $userId !== null ? (string) $userId : '';
        if (! $userId) {
            $userId = FakeIdTranslationService::model(new User)->key($userIdKey)->translateUlid();
        }

        $builder->when(! $isAll && ! $request->has('user_id'), function (Builder $query) {
            return $query->where('created_by', Auth::id());
        })->when(! $isAll && $request->has('user_id'), function (Builder $query) use ($userId) {
            return $query->where(function (Builder $query) use ($userId) {
                return $query->whereHas('event.components.contributors', function (Builder $query) use ($userId) {
                    return $query->where('user_id', $userId);
                })->orWhere('created_by', $userId);
            });
        })->when(function () use ($request) {
            return $request->boolean('is_approved') || $request->boolean('is_canceled') || $request->boolean('is_rejected') || $request->boolean('is_rollback');
        }, function (Builder $query) use ($request) {
            $query->where(function (Builder $query) use ($request) {
                if ($request->boolean('is_approved')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('approved_at'));
                }
                if ($request->boolean('is_canceled')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('cancelled_at'));
                }
                if ($request->boolean('is_rejected')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('rejected_at'));
                }
                if ($request->boolean('is_rollback')) {
                    $query->orWhereHas('event', fn (Builder $query) => $query->whereNotNull('rollback_at'));
                }
            });
        });
    }
}
