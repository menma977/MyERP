<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalContributor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class ApprovalComponentContributorController extends Controller
{
    /**
     * Approval Component Contributor Index
     *
     * Display a listing of the resource.
     *
     * @return ApprovalContributor[]|Collection<int, ApprovalContributor>|LengthAwarePaginator<int, ApprovalContributor>
     */
    public function index(Request $request)
    {
        $approvalComponentContributor = ApprovalContributor::with([
            'component',
            'approvable',
        ])
            ->where('approval_component_id', $request->route('approval_component_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $approvalComponentContributor->get();
        }

        return $approvalComponentContributor->paginate($request->input('per_page', 10));
    }

    /**
     * Approval Component Contributor Store
     *
     * Store a newly created resource in storage.
     *
     * @noinspection DuplicatedCode
     *
     * @return array<string, string>
     */
    public function store(Request $request)
    {
        $request->validate([
            'approvable_id' => ['required'],
            'key' => ['required', Rule::in(array_keys(config('approval.group')))],
        ]);

        $approvalComponentContributor = new ApprovalContributor;
        $approvalComponentContributor->approval_component_id = (int) $request->route('approval_component_id');
        $approvalComponentContributor->approvable_id = $request->input('approvable_id');
        $approvalComponentContributor->approvable_type = config('approval.group')[$request->input('key')];
        $approvalComponentContributor->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'contributor'], App::getLocale()),
        ];
    }

    /**
     * Approval Component Contributor Show
     *
     * Show the specified resource.
     *
     * @return ApprovalContributor|Collection<int, ApprovalContributor>
     */
    public function show(Request $request)
    {
        return ApprovalContributor::with([
            'component',
            'approvable',
        ])->findOrFail($request->route('id'));
    }

    /**
     * Approval Component Contributor Update
     *
     * Update the specified resource in storage.
     *
     * @noinspection DuplicatedCode
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'approvable_id' => ['required'],
            'key' => ['required', Rule::in(array_keys(config('approval.group')))],
        ]);

        $approvalComponentContributor = ApprovalContributor::findOrFail($request->route('id'));
        if ($approvalComponentContributor instanceof ApprovalContributor) {
            $approvalComponentContributor->approval_component_id = (int) $request->route('approval_component_id');
            $approvalComponentContributor->approvable_id = $request->input('approvable_id');
            $approvalComponentContributor->approvable_type = config('approval.group')[$request->input('key')];
            $approvalComponentContributor->save();
        }

        return [
            'message' => trans('messages.success.update', ['target' => 'contributor'], App::getLocale()),
        ];
    }

    /**
     * Approval Component Contributor Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $approvalComponentContributor = ApprovalContributor::findOrFail($request->route('id'));
        if ($approvalComponentContributor instanceof ApprovalContributor) {
            $approvalComponentContributor->delete();
        }

        return [
            'message' => trans('messages.success.delete', ['target' => 'contributor'], App::getLocale()),
        ];
    }
}
