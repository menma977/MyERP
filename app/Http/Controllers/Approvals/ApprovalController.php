<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalResource;
use App\Models\Approval\Approval;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class ApprovalController extends Controller
{
    /**
     * Approval Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, Approval>|LengthAwarePaginator<int, Approval>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $approvals = Approval::with([
            'flow',
            'flow.components',
            'components',
        ])->when($request->input('search'), function ($build) use ($request) {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalResource::collection($approvals->get());
        }

        if ($request->input('type') === 'count') {
            return $approvals->count();
        }

        return ApprovalResource::collection($approvals->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Approval Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, approval: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'flow_id' => ['required', 'exists:approval_flows,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:0,1'],
        ]);

        $approval = new Approval;
        $approval->approval_flow_id = $request->input('flow_id');
        $approval->name = $request->input('name');
        $approval->type = $request->input('type');
        $approval->save();

        return [
            'message' => trans('messages.success.store', ['target' => $approval->name], App::getLocale()),
            'approval' => $approval->toResource(),
        ];
    }

    /**
     * Approval Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return Approval::with([
            'flow',
            'flow.components',
            'components',
        ])->withUsers()->findOrFail(FakeIdTranslationService::model(new Approval)->key($request->route('id'))->translateUlid())->toResource();
    }

    /**
     * Approval Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, approval: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'flow_id' => ['required', 'exists:approval_flows,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:0,1'],
        ]);

        $approval = Approval::findOrFail(FakeIdTranslationService::model(new Approval)->key($request->route('id'))->translateUlid());
        $approval->approval_flow_id = $request->input('flow_id');
        $approval->name = $request->input('name');
        $approval->type = $request->input('type');
        $approval->save();

        return [
            'message' => trans('messages.success.update', ['target' => $approval->name], App::getLocale()),
            'approval' => $approval->toResource(),
        ];
    }

    /**
     * Approval Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, approval: JsonResource}
     */
    public function delete(Request $request): array
    {
        $approval = Approval::findOrFail(FakeIdTranslationService::model(new Approval)->key($request->route('id'))->translateUlid());
        if ($approval->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $approval->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }
        $approval->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => $approval->name], App::getLocale()),
            'approval' => $approval->toResource(),
        ];
    }

    /**
     * Approval Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, approval: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var Approval $approval */
        $approval = Approval::onlyTrashed()->findOrFail(FakeIdTranslationService::model(new Approval)->key($request->route('id'))->translateUlid());
        $approval->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => $approval->name], App::getLocale()),
            'approval' => $approval->toResource(),
        ];
    }

    /**
     * Approval Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, approval: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var Approval $approval */
        $approval = Approval::onlyTrashed()->findOrFail(FakeIdTranslationService::model(new Approval)->key($request->route('id'))->translateUlid());
        $approval->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => $approval->name], App::getLocale()),
            'approval' => $approval->toResource(),
        ];
    }
}
