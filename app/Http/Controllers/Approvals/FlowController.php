<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalFlow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class FlowController extends Controller
{
    /**
     * Flow Index
     *
     * Display a listing of the resource.
     */
    /**
     * @return ApprovalFlow[]|Collection<int, ApprovalFlow>|LengthAwarePaginator<int, ApprovalFlow>
     */
    public function index(Request $request)
    {
        $flow = ApprovalFlow::when($request->input('search'), function ($query) use ($request) {
            $query->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $flow->get();
        }

        return $flow->paginate($request->input('per_page', 10));
    }

    /**
     * Flow Store
     *
     * Store a newly created resource in storage.
     */
    /**
     * @return array<string, string>
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $flow = new ApprovalFlow;
        $flow->name = $request->input('name');
        $flow->save();

        return [
            'message' => trans('messages.success.store', ['target' => $flow->name], App::getLocale()),
        ];
    }

    /**
     * ApprovalFlow Show
     *
     * Show the specified resource.
     */
    /**
     * @return ApprovalFlow|Collection<int, ApprovalFlow>
     */
    public function show(Request $request)
    {
        return ApprovalFlow::with([
            'components',
        ])->findOrFail($request->route('id'));
    }

    /**
     * ApprovalFlow Update
     *
     * Update the specified resource in storage.
     */
    /**
     * @return array<string, string>
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $flow = ApprovalFlow::findOrFail($request->route('id'));
        if ($flow instanceof ApprovalFlow) {
            $flow->name = $request->input('name');
            $flow->save();
        }

        $flowName = ($flow instanceof ApprovalFlow) ? $flow->name : 'Flow';

        return [
            'message' => trans('messages.success.update', ['target' => $flowName], App::getLocale()),
        ];
    }

    /**
     * ApprovalFlow Delete
     *
     * Remove the specified resource from storage.
     */
    /**
     * @return array<string, string>
     */
    public function delete(Request $request)
    {
        $flow = ApprovalFlow::findOrFail($request->route('id'));

        if ($flow instanceof ApprovalFlow && $flow->components()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $flow->name, 'target' => 'Component'], App::getLocale()),
            ]);
        }

        if ($flow instanceof ApprovalFlow) {
            $flow->delete();
        }

        $flowName = ($flow instanceof ApprovalFlow) ? $flow->name : 'Flow';

        return [
            'message' => trans('messages.success.delete', ['target' => $flowName], App::getLocale()),
        ];
    }
}
