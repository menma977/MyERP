<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
     * @return ApprovalGroup[]|Collection<int, ApprovalGroup>|LengthAwarePaginator<int, ApprovalGroup>
     */
    public function index(Request $request)
    {
        $group = ApprovalGroup::when($request->input('search'), function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $group->get();
        }

        return $group->paginate($request->input('per_page', 10));
    }

    /**
     * Group Store
     *
     * Store a newly created resource in storage.
     *
     * @return array<string, string>
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $group = new ApprovalGroup;
        $group->name = $request->input('name');
        $group->save();

        return [
            'message' => trans('messages.success.store', ['target' => $group->name], App::getLocale()),
        ];
    }

    /**
     * ApprovalGroup Show
     *
     * Show the specified resource.
     *
     * @return ApprovalGroup|Collection<int, ApprovalGroup>
     */
    public function show(Request $request)
    {
        return ApprovalGroup::with([
            'contributors',
            'contributors.user',
        ])->findOrFail($request->route('id'));
    }

    /**
     * ApprovalGroup Update
     *
     * Update the specified resource in storage.
     *
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $group = ApprovalGroup::findOrFail($request->route('id'));
        if ($group instanceof ApprovalGroup) {
            $group->name = $request->input('name');
            $group->save();
        }

        $groupName = ($group instanceof ApprovalGroup) ? $group->name : 'Group';

        return [
            'message' => trans('messages.success.update', ['target' => $groupName], App::getLocale()),
        ];
    }

    /**
     * ApprovalGroup Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $group = ApprovalGroup::findOrFail($request->route('id'));
        if ($group instanceof ApprovalGroup && $group->contributors()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $group->name, 'target' => 'Contributor'], App::getLocale()),
            ]);
        }
        if ($group instanceof ApprovalGroup) {
            $group->delete();
        }

        $groupName = ($group instanceof ApprovalGroup) ? $group->name : 'Group';

        return [
            'message' => trans('messages.success.delete', ['target' => $groupName], App::getLocale()),
        ];
    }
}
