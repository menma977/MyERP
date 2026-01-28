<?php

namespace App\Models\Scopes;

use App\Models\User;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class WithCompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @noinspection DuplicatedCode */
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

        $user = User::where('id', $userId)->first();

        if (! $isAll) {
            if ($user instanceof User) {
                $builder->whereIn('company_id', $user->hasCompany()->pluck('company_id')->toArray());
            } else {
                $builder->where('company_id', 0);
            }
        }
    }
}
