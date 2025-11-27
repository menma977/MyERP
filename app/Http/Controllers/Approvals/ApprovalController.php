<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\Approval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
     * @return Approval[]|Collection<int, Approval>|LengthAwarePaginator<int, Approval>
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
     *
     * @return array<string, string>
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
    /**
     * @return Approval|Collection<int, Approval>
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
    /**
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'flow_id' => ['required', 'exists:approval_flows,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:0,1'],
        ]);

        $approval = Approval::findOrFail($request->route('id'));
        if ($approval instanceof Approval) {
            $approval->approval_flow_id = $request->input('flow_id');
            $approval->name = $request->input('name');
            $approval->type = $request->input('type');
            $approval->save();
        }

        $approvalName = ($approval instanceof Approval) ? $approval->name : 'Approval';

        return [
            'message' => trans('messages.success.update', ['target' => $approvalName], App::getLocale()),
        ];
    }

    /**
     * Approval Delete
     *
     * Remove the specified resource from storage.
     */
    /**
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $approval = Approval::findOrFail($request->route('id'));
        if ($approval instanceof Approval && $approval->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $approval->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }
        if ($approval instanceof Approval) {
            $approval->delete();
        }

        $approvalName = ($approval instanceof Approval) ? $approval->name : 'Approval';

        return [
            'message' => trans('messages.success.delete', ['target' => $approvalName], App::getLocale()),
        ];
    }
}
