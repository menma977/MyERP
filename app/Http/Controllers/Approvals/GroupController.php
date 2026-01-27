<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalGroupResource;
use App\Models\Approval\ApprovalGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    /**
     * Group Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, ApprovalGroup>|LengthAwarePaginator<int, ApprovalGroup>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $group = ApprovalGroup::when($request->input('search'), function ($build) use ($request) {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalGroupResource::collection($group->get());
        }

        if ($request->input('type') === 'count') {
            return $group->count();
        }

        return ApprovalGroupResource::collection($group->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Group Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, group: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $group = new ApprovalGroup;
        $group->name = $request->input('name');
        $group->save();

        return [
            'message' => trans('messages.success.store', ['target' => $group->name], App::getLocale()),
            'group' => $group->toResource(),
        ];
    }

    /**
     * ApprovalGroup Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ApprovalGroup::with([
            'contributors',
            'contributors.user',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * ApprovalGroup Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, group: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $group = ApprovalGroup::where('id', $request->route('id'))->firstOrFail();
        $group->name = $request->input('name');
        $group->save();

        return [
            'message' => trans('messages.success.update', ['target' => $group->name], App::getLocale()),
            'group' => $group->toResource(),
        ];
    }

    /**
     * ApprovalGroup Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, group: JsonResource}
     */
    public function delete(Request $request): array
    {
        $group = ApprovalGroup::where('id', $request->route('id'))->firstOrFail();
        if ($group->contributors()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $group->name, 'target' => 'Contributor'], App::getLocale()),
            ]);
        }
        $group->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => $group->name], App::getLocale()),
            'group' => $group->toResource(),
        ];
    }

    /**
     * Group Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, group: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var ApprovalGroup $group */
        $group = ApprovalGroup::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $group->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => $group->name], App::getLocale()),
            'group' => $group->toResource(),
        ];
    }

    /**
     * Group Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, group: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var ApprovalGroup $group */
        $group = ApprovalGroup::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $group->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => $group->name], App::getLocale()),
            'group' => $group->toResource(),
        ];
    }
}
