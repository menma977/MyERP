<?php

namespace App\Http\Controllers;

use App;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionController extends Controller
{
    /**
     * Permission Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, Permission>|LengthAwarePaginator<int, Permission>
     */
    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $permissions = Permission::when($request->input('search'), function ($query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('label', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('group', 'like', '%' . $request->input('search') . '%');
            });
        })
            ->when($request->input('group'), function ($query) use ($request) {
                return $query->where('group', $request->input('group'));
            })
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return $permissions->get();
        }

        return $permissions->paginate($request->input('per_page', 10));
    }

    /**
     * Permission Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): Permission
    {
        return Permission::where('id', $request->route('id'))->firstOrFail();
    }

    /**
     * Permission Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'label' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
            'guard_name' => ['required', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        $permission = new Permission;
        $permission->name = $request->input('name');
        $permission->label = $request->input('label');
        $permission->group = $request->input('group');
        $permission->guard_name = $request->input('guard_name');
        $permission->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Permission'], App::getLocale()),
        ];
    }

    /**
     * Permission Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $request->route('id')],
            'label' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
            'guard_name' => ['required', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        /** @var Permission $permission */
        $permission = Permission::findOrFail($request->route('id'));

        $permission->name = $request->input('name');
        $permission->label = $request->input('label');
        $permission->group = $request->input('group');
        $permission->guard_name = $request->input('guard_name');
        $permission->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Permission'], App::getLocale()),
        ];
    }

    /**
     * Permission Delete (soft delete if the model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string}
     */
    public function delete(Request $request): array
    {
        /** @var Permission $permission */
        $permission = Permission::findOrFail($request->route('id'));
        $permission->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Permission'], App::getLocale()),
        ];
    }
}
