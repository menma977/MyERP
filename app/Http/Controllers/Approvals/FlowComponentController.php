<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Resources\Approval\ApprovalFlowComponentResource;
use App\Models\Approval\ApprovalDictionary;
use App\Models\Approval\ApprovalFlowComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class FlowComponentController extends Controller
{
    /**
     * Flow Component Index
     *
     * Display a listing of the resource.
     *
     * @return Collection<int, ApprovalFlowComponent>|LengthAwarePaginator<int, ApprovalFlowComponent>|JsonResource|int
     */
    public function index(Request $request): Collection|LengthAwarePaginator|JsonResource|int
    {
        $flowComponent = ApprovalFlowComponent::where('approval_flow_id', $request->route('flow_id'))->when($request->input('search'), function ($build) use ($request) {
            return $build->where('key', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return ApprovalFlowComponentResource::collection($flowComponent->get());
        }

        if ($request->input('type') === 'count') {
            return $flowComponent->count();
        }

        return ApprovalFlowComponentResource::collection($flowComponent->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Flow Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, flow_component: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'approval_dictionary_id' => ['required', 'exists:approval_dictionaries,id'],
        ]);

        $flowComponent = new ApprovalFlowComponent;
        $flowComponent->approval_flow_id = $request->route('flow_id');
        $flowComponent->approval_dictionary_id = $request->input('approval_dictionary_id');
        $dictionary = ApprovalDictionary::where('id', $request->input('approval_dictionary_id'))->first();
        if ($dictionary instanceof ApprovalDictionary) {
            $flowComponent->key = $dictionary->key;
        }
        $flowComponent->save();

        return [
            'message' => trans('messages.success.store', ['target' => $flowComponent->key], App::getLocale()),
            'flow_component' => $flowComponent->toResource(),
        ];
    }

    /**
     * Flow Component Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return ApprovalFlowComponent::with([
            'flow',
            'dictionary',
        ])->withUsers()->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Flow Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, flow_component: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'approval_dictionary_id' => ['required', 'exists:approval_dictionaries,id'],
        ]);

        $flowComponent = ApprovalFlowComponent::where('id', $request->route('id'))->firstOrFail();
        $flowComponent->approval_flow_id = $request->route('flow_id');
        $flowComponent->approval_dictionary_id = $request->input('approval_dictionary_id');
        $dictionary = ApprovalDictionary::where('id', $request->input('approval_dictionary_id'))->first();
        if ($dictionary instanceof ApprovalDictionary) {
            $flowComponent->key = $dictionary->key;
        }
        $flowComponent->save();

        return [
            'message' => trans('messages.success.update', ['target' => $flowComponent->key], App::getLocale()),
            'flow_component' => $flowComponent->toResource(),
        ];
    }

    /**
     * Flow Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, flow_component: JsonResource}
     */
    public function delete(Request $request): array
    {
        $flowComponent = ApprovalFlowComponent::where('id', $request->route('id'))->firstOrFail();
        $flowComponent->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => $flowComponent->key], App::getLocale()),
            'flow_component' => $flowComponent->toResource(),
        ];
    }

    /**
     * Flow Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, flow_component: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var ApprovalFlowComponent $flowComponent */
        $flowComponent = ApprovalFlowComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $flowComponent->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => $flowComponent->key], App::getLocale()),
            'flow_component' => $flowComponent->toResource(),
        ];
    }

    /**
     * Flow Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, flow_component: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var ApprovalFlowComponent $flowComponent */
        $flowComponent = ApprovalFlowComponent::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $flowComponent->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => $flowComponent->key], App::getLocale()),
            'flow_component' => $flowComponent->toResource(),
        ];
    }
}
