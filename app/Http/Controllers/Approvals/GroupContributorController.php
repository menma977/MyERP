<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalGroupContributorResource;
use App\Models\Approval\ApprovalGroupContributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class GroupContributorController extends Controller
{
    /**
     * Group Contributor Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, ApprovalGroupContributor>|LengthAwarePaginator<int, ApprovalGroupContributor>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $groupContributors = ApprovalGroupContributor::with([
            'group',
            'user',
        ])->when($request->input('search'), function ($build) use ($request) {
            return $build->whereHas('user', function ($build) use ($request) {
                return $build->where('name', 'like', '%'.$request->input('search').'%');
            });
        })->when($request->route('group_id'), function ($build) use ($request) {
            return $build->where('approval_group_id', $request->route('group_id'));
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalGroupContributorResource::collection($groupContributors->get());
        }

        if ($request->input('type') === 'count') {
            return $groupContributors->count();
        }

        return ApprovalGroupContributorResource::collection($groupContributors->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Group Contributor Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, group_contributor: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $group = ApprovalGroupContributor::where('approval_group_id', $request->route('group_id'))
            ->where('user_id', $request->input('user_id'))
            ->first();
        if ($group) {
            throw ValidationException::withMessages([
                'user_id' => ['The user has already been added to the group.'],
            ]);
        }

        $groupContributors = new ApprovalGroupContributor;
        $groupContributors->approval_group_id = $request->route('group_id');
        $groupContributors->user_id = $request->input('user_id');
        $groupContributors->save();

        $user = User::find($request->input('user_id'));
        $userName = ($user instanceof User) ? $user->name : 'User';

        return [
            'message' => trans('messages.success.store', ['target' => $userName], App::getLocale()),
            'group_contributor' => $groupContributors->toResource(),
        ];
    }

    /**
     * Group Contributor Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ApprovalGroupContributor::with([
            'group',
            'user',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Group Contributor Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, group_contributor: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $groupContributors = ApprovalGroupContributor::where('id', $request->route('id'))->firstOrFail();
        $groupContributors->approval_group_id = $request->route('group_id');
        $groupContributors->user_id = $request->input('user_id');
        $groupContributors->save();

        $user = $groupContributors->user;

        return [
            'message' => trans('messages.success.update', ['target' => $user->name], App::getLocale()),
            'group_contributor' => $groupContributors->toResource(),
        ];
    }

    /**
     * Group Contributor Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, group_contributor: JsonResource}
     */
    public function delete(Request $request): array
    {
        $groupContributors = ApprovalGroupContributor::where('id', $request->route('id'))->firstOrFail();

        $user = $groupContributors->user;
        $userName = $user->name;

        $groupContributors->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => $userName], App::getLocale()),
            'group_contributor' => $groupContributors->toResource(),
        ];
    }

    /**
     * Group Contributor Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, group_contributor: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var ApprovalGroupContributor $contributor */
        $contributor = ApprovalGroupContributor::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $contributor->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => $contributor->user->name], App::getLocale()),
            'group_contributor' => $contributor->toResource(),
        ];
    }

    /**
     * Group Contributor Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, group_contributor: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var ApprovalGroupContributor $contributor */
        $contributor = ApprovalGroupContributor::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $userName = $contributor->user->name;
        $contributor->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => $userName], App::getLocale()),
            'group_contributor' => $contributor->toResource(),
        ];
    }
}
