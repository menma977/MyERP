<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class UserController extends Controller
{
    /**
     * User Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, User>|LengthAwarePaginator<int, User>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
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
            return $users->get();
        }

        return $users->paginate($request->input('per_page', 10));
    }

    /**
     * User Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): User
    {
        return User::where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * User Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
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
        ];
    }

    /**
     * User Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$request->route('id')],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->route('id')],
        ]);

        /** @var User $user */
        $user = User::findOrFail($request->route('id'));
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'User'], App::getLocale()),
        ];
    }

    /**
     * User Delete (soft delete if model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var User $user */
        $user = User::findOrFail($request->route('id'));
        $user->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'User'], App::getLocale()),
        ];
    }

    /**
     * User Restore
     *
     * Restore a soft deleted resource.
     *
     * @return array{message: string}
     */
    public function restore(Request $request): array
    {
        /** @var User $user */
        $user = User::onlyTrashed()->findOrFail($request->route('id'));
        $user->restore();

        return [
            'message' => trans('messages.success.update', ['target' => 'User'], App::getLocale()),
        ];
    }
}
