<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class ApprovalController extends Controller
{
    /**
     * Approval Index
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $approvals = Approval::with([
            'flow',
            'flow.components',
            'components',
        ])->when($request->input('search'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $approvals->get();
        }

        return $approvals->paginate($request->input('per_page', 10));
    }

    /**
     * Approval Store
     *
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
        ];
    }

    /**
     * Approval Show
     *
     * Show the specified resource.
     */
    public function show(Request $request)
    {
        return Approval::with([
            'flow',
            'flow.components',
            'components',
        ])->findOrFail($request->route('id'));
    }

    /**
     * Approval Update
     *
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'flow_id' => ['required', 'exists:approval_flows,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:0,1'],
        ]);

        $approval = Approval::findOrFail($request->route('id'));
        $approval->approval_flow_id = $request->input('flow_id');
        $approval->name = $request->input('name');
        $approval->type = $request->input('type');
        $approval->save();

        return [
            'message' => trans('messages.success.update', ['target' => $approval->name], App::getLocale()),
        ];
    }

    /**
     * Approval Delete
     *
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $approval = Approval::findOrFail($request->route('id'));
        if ($approval->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $approval->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }
        $approval->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => $approval->name], App::getLocale()),
        ];
    }
}
