<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class RoleController extends Controller
{
    /**
     * Role Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, Role>|Collection<int, Role>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $roles = Role::when($request->input('search'), function ($query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('guard_name', 'like', '%'.$request->input('search').'%');
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return RoleResource::collection($roles->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $roles->count();
        }

        return RoleResource::collection($roles->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Role Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return Role::where(
            'id',
            FakeIdTranslationService::model(new Role)->key($request->route('id'))->translateUlid()
        )->firstOrFail()->toResource();
    }

    /**
     * Role Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, role: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name' => ['nullable', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        $role = new Role;
        $role->name = $request->input('name');
        $role->guard_name = $request->input('guard_name', 'sanctum');
        $role->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Role'], App::getLocale()),
            'role' => $role->toResource(),
        ];
    }

    /**
     * Role Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, role: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,'.FakeIdTranslationService::model(new Role)->key($request->route('id'))->translateUlid(),
            ],
            'guard_name' => ['nullable', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        /** @var Role $role */
        $role = Role::findOrFail(FakeIdTranslationService::model(new Role)->key($request->route('id'))->translateUlid());

        $role->name = $request->input('name');
        $role->guard_name = $request->input('guard_name', 'sanctum');
        $role->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Role'], App::getLocale()),
            'role' => $role->toResource(),
        ];
    }

    /**
     * Role Delete (soft delete if the model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, role: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var Role $role */
        $role = Role::findOrFail(FakeIdTranslationService::model(new Role)->key($request->route('id'))->translateUlid());
        $role->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Role'], App::getLocale()),
            'role' => $role->toResource(),
        ];
    }
}
