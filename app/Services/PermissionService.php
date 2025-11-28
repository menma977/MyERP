<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    /**
     * Retrieve and format the permissions for a specified user.
     *
     * This method collects permissions associated with the user's role and individual permissions.
     * It categorizes the permissions by groups, builds a structured collection indicating what actions
     * (e.g., index, store, update, delete) the user can perform, and includes additional permissions
     * that do not follow standard naming conventions.
     *
     * @param  User  $user  The user whose permissions are to be retrieved.
     * @param  string|null  $filter  Optional filter for permission groups
     * @return Collection<int, array{name: string, can: Collection<string, bool>}>
     */
    public static function grab(User $user, ?string $filter): Collection
    {
        $permissionCollection = collect();

        /** @var Collection<int, Role> $roles */
        $roles = $user->roles()->get();

        $listPermission = Permission::when($filter, function (Builder $query) use ($filter) {
            return $query->where('group', 'like', "$filter%");
        })->get()->groupBy('group');

        foreach ($listPermission as $permission) {
            $name = $permission->first()->group ?? '-';

            $collection = collect($permission);

            $index = $collection->filter(function (Permission $item) {
                return Str::contains($item->name, '.index');
            })->filter(function (Permission $item) use ($roles) {
                return (bool) $roles->filter(fn (Role $role) => $role->permissions->contains('name', $item->name))->count();
            })->isNotEmpty();

            $store = $collection->filter(function (Permission $item) {
                return Str::contains($item->name, '.store');
            })->filter(function (Permission $item) use ($roles) {
                return (bool) $roles->filter(fn ($role) => $role->hasPermissionTo($item->name))->count();
            })->isNotEmpty();

            $show = $collection->filter(function (Permission $item) {
                return Str::contains($item->name, '.show');
            })->filter(function (Permission $item) use ($roles) {
                return (bool) $roles->filter(fn ($role) => $role->hasPermissionTo($item->name))->count();
            })->isNotEmpty();

            $update = $collection->filter(function (Permission $item) {
                return Str::contains($item->name, '.update');
            })->filter(function (Permission $item) use ($roles) {
                return (bool) $roles->filter(fn ($role) => $role->hasPermissionTo($item->name))->count();
            })->isNotEmpty();

            $delete = $collection->filter(function (Permission $item) {
                return Str::contains($item->name, '.delete');
            })->filter(function (Permission $item) use ($roles) {
                return (bool) $roles->filter(fn ($role) => $role->hasPermissionTo($item->name))->count();
            })->isNotEmpty();

            $canCollection = collect(['index' => $index, 'store' => $store, 'show' => $show, 'update' => $update, 'delete' => $delete]);

            $permissionCollection->push([
                'name' => $name,
                'can' => $canCollection,
            ]);
        }

        return $permissionCollection;
    }
}
