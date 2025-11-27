<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalDictionary;
use App\Models\Approval\ApprovalFlowComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class FlowComponentController extends Controller
{
    /**
     * Flow Component Index
     *
     * Display a listing of the resource.
     *
     * @return ApprovalFlowComponent[]|Collection<int, ApprovalFlowComponent>|LengthAwarePaginator<int, ApprovalFlowComponent>
     */
    public function index(Request $request)
    {
        $flowComponent = ApprovalFlowComponent::where('approval_flow_id', $request->route('flow_id'))->when($request->input('search'), function ($query) use ($request) {
            return $query->where('key', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $flowComponent->get();
        }

        return $flowComponent->paginate($request->input('per_page', 10));
    }

    /**
     * Flow Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array<string, string>
     */
    public function store(Request $request)
    {
        $request->validate([
            'approval_dictionary_id' => ['required', 'exists:approval_dictionaries,id'],
        ]);

        $flowComponent = new ApprovalFlowComponent;
        $flowComponent->approval_flow_id = $request->route('flow_id');
        $flowComponent->approval_dictionary_id = $request->input('approval_dictionary_id');
        $dictionary = ApprovalDictionary::findOrFail($request->input('approval_dictionary_id'));
        if ($dictionary instanceof ApprovalDictionary) {
            $flowComponent->key = $dictionary->key;
        }
        $flowComponent->save();

        return [
            'message' => trans('messages.success.store', ['target' => $flowComponent->key], App::getLocale()),
        ];
    }

    /**
     * Flow Component Show
     *
     * Show the specified resource.
     *
     * @return ApprovalFlowComponent|Collection<int, ApprovalFlowComponent>
     */
    public function show(Request $request)
    {
        return ApprovalFlowComponent::with([
            'flow',
            'dictionary',
        ])->findOrFail($request->route('id'));
    }

    /**
     * Flow Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'approval_dictionary_id' => ['required', 'exists:approval_dictionaries,id'],
        ]);

        $flowComponent = ApprovalFlowComponent::findOrFail($request->route('id'));
        if ($flowComponent instanceof ApprovalFlowComponent) {
            $flowComponent->approval_flow_id = $request->route('flow_id');
            $flowComponent->approval_dictionary_id = $request->input('approval_dictionary_id');
            $dictionary = ApprovalDictionary::findOrFail($request->input('approval_dictionary_id'));
            if ($dictionary instanceof ApprovalDictionary) {
                $flowComponent->key = $dictionary->key;
            }
            $flowComponent->save();
        }

        $flowComponentKey = ($flowComponent instanceof ApprovalFlowComponent) ? $flowComponent->key : 'Component';

        return [
            'message' => trans('messages.success.update', ['target' => $flowComponentKey], App::getLocale()),
        ];
    }

    /**
     * Flow Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $flowComponent = ApprovalFlowComponent::findOrFail($request->route('id'));
        if ($flowComponent instanceof ApprovalFlowComponent) {
            $flowComponent->delete();
        }

        $flowComponentKey = ($flowComponent instanceof ApprovalFlowComponent) ? $flowComponent->key : 'Component';

        return [
            'message' => trans('messages.success.delete', ['target' => $flowComponentKey], App::getLocale()),
        ];
    }
}
