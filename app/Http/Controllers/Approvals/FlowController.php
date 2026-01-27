<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalFlowResource;
use App\Models\Approval\ApprovalFlow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class FlowController extends Controller
{
    /**
     * Flow Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, ApprovalFlow>|LengthAwarePaginator<int, ApprovalFlow>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $flow = ApprovalFlow::when($request->input('search'), function ($build) use ($request) {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalFlowResource::collection($flow->get());
        }

        if ($request->input('type') === 'count') {
            return $flow->count();
        }

        return ApprovalFlowResource::collection($flow->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Flow Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, flow: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $flow = new ApprovalFlow;
        $flow->name = $request->input('name');
        $flow->save();

        return [
            'message' => trans('messages.success.store', ['target' => $flow->name], App::getLocale()),
            'flow' => $flow->toResource(),
        ];
    }

    /**
     * ApprovalFlow Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ApprovalFlow::with([
            'components',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * ApprovalFlow Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, flow: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $flow = ApprovalFlow::where('id', $request->route('id'))->firstOrFail();
        $flow->name = $request->input('name');
        $flow->save();

        return [
            'message' => trans('messages.success.update', ['target' => $flow->name], App::getLocale()),
            'flow' => $flow->toResource(),
        ];
    }

    /**
     * ApprovalFlow Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, flow: JsonResource}
     */
    public function delete(Request $request): array
    {
        $flow = ApprovalFlow::where('id', $request->route('id'))->firstOrFail();

        if ($flow->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $flow->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }
        $flow->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => $flow->name], App::getLocale()),
            'flow' => $flow->toResource(),
        ];
    }

    /**
     * Flow Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, flow: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var ApprovalFlow $flow */
        $flow = ApprovalFlow::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $flow->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => $flow->name], App::getLocale()),
            'flow' => $flow->toResource(),
        ];
    }

    /**
     * Flow Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, flow: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var ApprovalFlow $flow */
        $flow = ApprovalFlow::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $flow->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => $flow->name], App::getLocale()),
            'flow' => $flow->toResource(),
        ];
    }
}
