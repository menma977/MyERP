<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class UserController extends Controller
{
    /**
     * User Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, User>|Collection<int, User>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $users = User::when($request->input('search'), function ($query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('username', 'like', '%'.$request->input('search').'%')
                    ->orWhere('email', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return UserResource::collection($users->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $users->count();
        }

        return UserResource::collection($users->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * User Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return User::findOrFail(FakeIdTranslationService::model(new User)->key($request->route('id'))->translateUlid())->toResource();
    }

    /**
     * User Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, user: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User;
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'User'], App::getLocale()),
            'user' => $user->toResource(),
        ];
    }

    /**
     * User Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, user: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.FakeIdTranslationService::model(new User)->key($request->route('id'))->translateUlid()],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.FakeIdTranslationService::model(new User)->key($request->route('id'))->translateUlid()],
        ]);

        /** @var User $user */
        $user = User::findOrFail(FakeIdTranslationService::model(new User)->key($request->route('id'))->translateUlid());
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'User'], App::getLocale()),
            'user' => $user->toResource(),
        ];
    }

    /**
     * User Delete (soft delete if model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, user: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var User $user */
        $user = User::findOrFail(FakeIdTranslationService::model(new User)->key($request->route('id'))->translateUlid());
        $user->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'User'], App::getLocale()),
            'user' => $user->toResource(),
        ];
    }

    /**
     * User Restore
     *
     * Restore a soft deleted resource.
     *
     * @return array{message: string, user: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var User $user */
        $user = User::onlyTrashed()->findOrFail(FakeIdTranslationService::model(new User)->key($request->route('id'))->translateUlid());
        $user->restore();

        return [
            'message' => trans('messages.success.update', ['target' => 'User'], App::getLocale()),
            'user' => $user->toResource(),
        ];
    }
}
