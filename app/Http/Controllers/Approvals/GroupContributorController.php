<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalGroupContributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
     * @return ApprovalGroupContributor[]|Collection<int, ApprovalGroupContributor>|LengthAwarePaginator<int, ApprovalGroupContributor>
     */
    public function index(Request $request)
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
            return $groupContributors->get();
        }

        return $groupContributors->paginate($request->input('per_page', 10));
    }

    /**
     * Group Contributor Store
     *
     * Store a newly created resource in storage.
     *
     * @return array<string, string>
     */
    public function store(Request $request)
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
        ];
    }

    /**
     * Group Contributor Show
     *
     * Show the specified resource.
     *
     * @return ApprovalGroupContributor|Collection<int, ApprovalGroupContributor>
     */
    public function show(Request $request)
    {
        return ApprovalGroupContributor::with([
            'group',
            'user',
        ])->findOrFail($request->route('id'));
    }

    /**
     * Group Contributor Update
     *
     * Update the specified resource in storage.
     *
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $groupContributors = ApprovalGroupContributor::findOrFail($request->route('id'));
        if ($groupContributors instanceof ApprovalGroupContributor) {
            $groupContributors->approval_group_id = $request->route('group_id');
            $groupContributors->user_id = $request->input('user_id');
            $groupContributors->save();
        }

        $user = User::find($request->input('user_id'));
        $userName = ($user instanceof User) ? $user->name : 'User';

        return [
            'message' => trans('messages.success.update', ['target' => $userName], App::getLocale()),
        ];
    }

    /**
     * Group Contributor Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $groupContributors = ApprovalGroupContributor::findOrFail($request->route('id'));

        $userName = 'User';
        if ($groupContributors instanceof ApprovalGroupContributor) {
            $user = $groupContributors->user;
            $userName = $user->name;

            $groupContributors->delete();
        }

        return [
            'message' => trans('messages.success.delete', ['target' => $userName], App::getLocale()),
        ];
    }
}
