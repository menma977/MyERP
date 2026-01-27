<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class PermissionController extends Controller
{
    /**
     * Permission Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, Permission>|Collection<int, Permission>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $permissions = Permission::when($request->input('search'), function ($query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query
                    ->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('label', 'like', '%'.$request->input('search').'%')
                    ->orWhere('group', 'like', '%'.$request->input('search').'%');
            });
        })->when($request->input('group'), function ($query) use ($request) {
            return $query->where('group', $request->input('group'));
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return PermissionResource::collection($permissions->get());
        }

        return PermissionResource::collection($permissions->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Permission Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return Permission::where(
            'id',
            FakeIdTranslationService::model(new Permission)->key($request->route('id'))->translateUlid()
        )->firstOrFail()->toResource();
    }

    /**
     * Permission Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, permission: JsonResource}
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
            'permission' => $permission->toResource(),
        ];
    }

    /**
     * Permission Update
     *
     * @return array{message: string, permission: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name,'.FakeIdTranslationService::model(new Permission)->key($request->route('id'))->translateUlid(),
            ],
            'label' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
            'guard_name' => ['required', 'string', 'max:255', 'in:web,api,sanctum'],
        ]);

        /** @var Permission $permission */
        $permission = Permission::findOrFail(FakeIdTranslationService::model(new Permission)->key($request->route('id'))->translateUlid());

        $permission->name = $request->input('name');
        $permission->label = $request->input('label');
        $permission->group = $request->input('group');
        $permission->guard_name = $request->input('guard_name');
        $permission->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Permission'], App::getLocale()),
            'permission' => $permission->toResource(),
        ];
    }

    /**
     * Permission Delete (soft delete if the model uses SoftDeletes)
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, permission: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var Permission $permission */
        $permission = Permission::findOrFail(FakeIdTranslationService::model(new Permission)->key($request->route('id'))->translateUlid());
        $permission->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Permission'], App::getLocale()),
            'permission' => $permission->toResource(),
        ];
    }
}
