<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalContributorResource;
use App\Models\Approval\ApprovalContributor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
     * @return Collection<int, ApprovalContributor>|LengthAwarePaginator<int, ApprovalContributor>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $approvalComponentContributor = ApprovalContributor::with([
            'component',
            'approvable',
        ])
            ->where('approval_component_id', $request->route('approval_component_id'))
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalContributorResource::collection($approvalComponentContributor->get());
        }

        if ($request->input('type') === 'count') {
            return $approvalComponentContributor->count();
        }

        return ApprovalContributorResource::collection($approvalComponentContributor->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Approval Component Contributor Store
     *
     * Store a newly created resource in storage.
     *
     * @noinspection DuplicatedCode
     *
     * @return array{message: string, approval_contributor: JsonResource}
     */
    public function store(Request $request): array
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
            'approval_contributor' => $approvalComponentContributor->toResource(),
        ];
    }

    /**
     * Approval Component Contributor Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ApprovalContributor::with([
            'component',
            'approvable',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Approval Component Contributor Update
     *
     * Update the specified resource in storage.
     *
     * @noinspection DuplicatedCode
     *
     * @return array{message: string, approval_contributor: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'approvable_id' => ['required'],
            'key' => ['required', Rule::in(array_keys(config('approval.group')))],
        ]);

        $approvalComponentContributor = ApprovalContributor::where('id', $request->route('id'))->firstOrFail();
        $approvalComponentContributor->approval_component_id = (int) $request->route('approval_component_id');
        $approvalComponentContributor->approvable_id = $request->input('approvable_id');
        $approvalComponentContributor->approvable_type = config('approval.group')[$request->input('key')];
        $approvalComponentContributor->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'contributor'], App::getLocale()),
            'approval_contributor' => $approvalComponentContributor->toResource(),
        ];
    }

    /**
     * Approval Component Contributor Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, approval_contributor: JsonResource}
     */
    public function delete(Request $request): array
    {
        $approvalComponentContributor = ApprovalContributor::where('id', $request->route('id'))->firstOrFail();
        $approvalComponentContributor->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'contributor'], App::getLocale()),
            'approval_contributor' => $approvalComponentContributor->toResource(),
        ];
    }

    /**
     * Approval Component Contributor Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, approval_contributor: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var ApprovalContributor $approvalComponentContributor */
        $approvalComponentContributor = ApprovalContributor::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $approvalComponentContributor->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'contributor'], App::getLocale()),
            'approval_contributor' => $approvalComponentContributor->toResource(),
        ];
    }

    /**
     * Approval Component Contributor Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, approval_contributor: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var ApprovalContributor $approvalComponentContributor */
        $approvalComponentContributor = ApprovalContributor::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $approvalComponentContributor->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'contributor'], App::getLocale()),
            'approval_contributor' => $approvalComponentContributor->toResource(),
        ];
    }
}
