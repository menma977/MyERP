<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class RoleController extends Controller
{
    /**
     * Role Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, Role>|LengthAwarePaginator<int, Role>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $roles = Role::when($request->input('search'), function ($query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('guard_name', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $roles->get();
        }

        return $roles->paginate($request->input('per_page', 10));
    }

    /**
     * Role Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): Role
    {
        return Role::where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Role Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     *
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name' => ['required', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        $role = new Role;
        $role->name = $request->input('name');
        $role->guard_name = $request->input('guard_name');
        $role->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Role'], App::getLocale()),
        ];
    }

    /**
     * Role Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$request->route('id')],
            'guard_name' => ['required', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        /** @var Role $role */
        $role = Role::findOrFail($request->route('id'));

        $role->name = $request->input('name');
        $role->guard_name = $request->input('guard_name');
        $role->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Role'], App::getLocale()),
        ];
    }

    /**
     * Role Delete (soft delete if the model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var Role $role */
        $role = Role::findOrFail($request->route('id'));
        $role->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Role'], App::getLocale()),
        ];
    }
}
