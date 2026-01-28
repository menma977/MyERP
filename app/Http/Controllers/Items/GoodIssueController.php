<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Resources\Items\GoodIssueResource;
use App\Models\Items\GoodIssue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GoodIssueController extends Controller
{
    /**
     * Good Issue Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, GoodIssue>|Collection<int, GoodIssue>|JsonResource|int
     */
    public function index(Request $request): LengthAwarePaginator|Collection|JsonResource|int
    {
        $goodIssues = GoodIssue::with([
            'salesInvoice',
            'components',
        ])->when($request->input('search'), function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                return $query->where('code', 'like', '%'.$request->input('search').'%')->orWhereHas('salesInvoice', function (Builder $query) use ($request) {
                    $query->where('code', 'like', '%'.$request->input('search').'%');
                });
            });
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type', 'paginate') === 'collection') {
            return GoodIssueResource::collection($goodIssues->get());
        }

        if ($request->input('type', 'paginate') === 'count') {
            return $goodIssues->count();
        }

        return GoodIssueResource::collection($goodIssues->withContributors()->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*')));
    }

    /**
     * Good Issue Show
     *
     * Show the specified resource.
     */
    public function show(Request $request): JsonResource
    {
        return GoodIssue::with([
            'salesInvoice',
            'components',
            'event.components.contributors.user',
            'createdBy',
            'updatedBy',
            'deletedBy',
        ])->where('id', $request->route('id'))->firstOrFail()->toResource();
    }

    /**
     * Good Issue Store
     *
     * Store a newly created resource in storage.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function store(Request $request): array
    {
        $request->validate([
            'sales_invoice_id' => ['required', 'string', 'exists:sales_invoices,id'],
            'note' => ['nullable', 'string'],
        ]);

        $goodIssue = new GoodIssue;
        $goodIssue->sales_invoice_id = $request->input('sales_invoice_id');
        $goodIssue->note = $request->input('note');
        $goodIssue->save();

        return [
            'message' => trans('messages.success.store', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Update
     *
     * Update the specified resource in storage.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function update(Request $request): array
    {
        $request->validate([
            'sales_invoice_id' => ['required', 'string', 'exists:sales_invoices,id'],
            'note' => ['nullable', 'string'],
        ]);

        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();
        $goodIssue->sales_invoice_id = $request->input('sales_invoice_id');
        $goodIssue->note = $request->input('note');
        $goodIssue->save();

        return [
            'message' => trans('messages.success.update', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function delete(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();

        $goodIssue->delete();

        return [
            'message' => trans('messages.success.delete', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function restore(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $goodIssue->restore();

        return [
            'message' => trans('messages.success.restore', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function destroy(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::onlyTrashed()->where('id', $request->route('id'))->firstOrFail();
        $goodIssue->forceDelete();

        return [
            'message' => trans('messages.success.destroy', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Approves
     *
     * Approve the specified resource.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function approve(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'approve', 'attribute' => 'Good Issue', 'target' => 'Access'], App::getLocale()),
            ]);
        }
        $goodIssue->approve($user);

        return [
            'message' => trans('messages.success.approve', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Reject
     *
     * Reject the specified resource.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function reject(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'reject', 'attribute' => 'Good Issue', 'target' => 'Access'], App::getLocale()),
            ]);
        }
        $goodIssue->reject($user);

        return [
            'message' => trans('messages.success.reject', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Cancel
     *
     * Cancel the specified resource.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function cancel(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'cancel', 'attribute' => 'Good Issue', 'target' => 'Access'], App::getLocale()),
            ]);
        }
        $goodIssue->cancel($user);

        return [
            'message' => trans('messages.success.cancel', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Rollback
     *
     * Roll back the specified resource.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function rollback(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'rollback', 'attribute' => 'Good Issue', 'target' => 'Access'], App::getLocale()),
            ]);
        }
        $goodIssue->rollback($user);

        return [
            'message' => trans('messages.success.rollback', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }

    /**
     * Good Issue Force
     *
     * Force execute action on the specified resource.
     *
     * @return array{message: string, good_issue: JsonResource}
     */
    public function force(Request $request): array
    {
        /** @var GoodIssue $goodIssue */
        $goodIssue = GoodIssue::where('id', $request->route('id'))->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => trans('messages.fail.action.cost', ['action' => 'force', 'attribute' => 'Good Issue', 'target' => 'Access'], App::getLocale()),
            ]);
        }
        $goodIssue->force($user, $request->input('step'));

        return [
            'message' => trans('messages.success.force', ['target' => 'Good Issue'], App::getLocale()),
            'good_issue' => $goodIssue->toResource(),
        ];
    }
}
