<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalComponent;
use App\Services\FakeIdTranslationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class ApprovalComponentController extends Controller
{
    /**
     * Approval Component Index
     *
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<int, ApprovalComponent>|Collection<int, ApprovalComponent>
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $approvalComponent = ApprovalComponent::with([
            'approval',
            'contributors.approvable',
        ])->where(
            'approval_id',
            FakeIdTranslationService::model(new Approval)->key($request->route('approval_id'))->translateUlid()
        )->when($request->input('search'), function ($build) use ($request) {
            return $build->where('name', 'like', '%'.$request->input('search').'%');
        })->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

        if ($request->input('type') === 'collection') {
            return $approvalComponent->get();
        }

        return $approvalComponent->withUsers()->paginate($request->input('per_page', 10), $request->input('columns', '*'));
    }

    /**
     * Approval Component Store
     *
     * Store a newly created resource in storage.
     *
     * @return array<string, mixed>
     */
    public function store(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:0,1'],
            'color' => ['required', 'string', 'max:255'],
        ]);

        $approvalId = FakeIdTranslationService::model(new Approval)->key($request->route('approval_id'))->translateUlid();
        $approvalComponent = new ApprovalComponent;
        $approvalComponent->approval_id = $approvalId;
        $approvalComponent->name = $request->input('name');
        $approvalComponent->type = $request->input('type');
        $approvalComponent->color = $request->input('color');
        $approvalComponent->step = 0;
        $approvalComponent->can_edit = true;
        $approvalComponent->can_drag = true;
        $approvalComponent->can_delete = true;
        $approvalComponent->save();

        $this->synchronizeSteps($approvalId);

        return [
            'message' => trans('messages.success.store', ['target' => $approvalComponent->name], App::getLocale()),
        ];
    }

    /**
     * Approval Component Show
     *
     * Show the specified resource.
     *
     * @return ApprovalComponent|Collection<int, ApprovalComponent>
     */
    public function show(Request $request): ApprovalComponent|Collection
    {
        return ApprovalComponent::with([
            'approval',
            'contributors.approvable',
        ])->withUsers()->findOrFail(FakeIdTranslationService::model(new ApprovalComponent)->key($request->route('id'))->translateUlid());
    }

    /**
     * Approval Component Update
     *
     * Update the specified resource in storage.
     *
     * @return array<string, mixed>
     */
    public function update(Request $request): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'integer', 'in:0,1'],
            'color' => ['required', 'string', 'max:255'],
            'step' => ['required', 'integer', 'min:0'],
        ]);

        $approvalComponent = ApprovalComponent::findOrFail(FakeIdTranslationService::model(new ApprovalComponent)->key($request->route('id'))->translateUlid());
        $approvalComponent->approval_id = FakeIdTranslationService::model(new Approval)->key($request->route('approval_id'))->translateUlid();
        $approvalComponent->name = $request->input('name');
        $approvalComponent->type = $request->input('type');
        $approvalComponent->color = $request->input('color');
        $approvalComponent->step = (int) $request->input('step');
        $approvalComponent->save();

        $this->synchronizeSteps(FakeIdTranslationService::model(new Approval)->key($request->route('approval_id'))->translateUlid());

        return [
            'message' => trans('messages.success.update', ['target' => $approvalComponent->name], App::getLocale()),
        ];
    }

    /**
     * Approval Component Delete
     *
     * Remove the specified resource from storage.
     *
     * @return array<string, mixed>
     */
    public function delete(Request $request): array
    {
        $approvalComponent = ApprovalComponent::findOrFail(FakeIdTranslationService::model(new ApprovalComponent)->key($request->route('id'))->translateUlid());
        if ($approvalComponent->contributors()->exists()) {
            throw ValidationException::withMessages([
                'message' => trans('messages.fail.delete.cost', ['attribute' => $approvalComponent->name, 'target' => 'Contributor'], App::getLocale()),
            ]);
        }
        $approvalId = $approvalComponent->approval_id;
        $componentName = $approvalComponent->name;
        $approvalComponent->delete();

        $this->synchronizeSteps($approvalId);

        return [
            'message' => trans('messages.success.delete', ['target' => $componentName], App::getLocale()),
        ];
    }

    /**
     * Approval Component Restore
     *
     * Restore the specified resource from storage.
     *
     * @return array<string, mixed>
     */
    public function restore(Request $request): array
    {
        /** @var ApprovalComponent $approvalComponent */
        $approvalComponent = ApprovalComponent::onlyTrashed()->findOrFail(FakeIdTranslationService::model(new ApprovalComponent)->key($request->route('id'))->translateUlid());
        $approvalComponent->restore();

        $this->synchronizeSteps($approvalComponent->approval_id);

        return [
            'message' => trans('messages.success.restore', ['target' => $approvalComponent->name], App::getLocale()),
        ];
    }

    /**
     * Approval Component Destroy
     *
     * Permanently remove the specified resource from storage.
     *
     * @return array<string, mixed>
     */
    public function destroy(Request $request): array
    {
        /** @var ApprovalComponent $approvalComponent */
        $approvalComponent = ApprovalComponent::onlyTrashed()->findOrFail(FakeIdTranslationService::model(new ApprovalComponent)->key($request->route('id'))->translateUlid());
        $approvalId = $approvalComponent->approval_id;
        $approvalComponent->forceDelete();

        $this->synchronizeSteps($approvalId);

        return [
            'message' => trans('messages.success.destroy', ['target' => $approvalComponent->name], App::getLocale()),
        ];
    }

    /**
     * Synchronize steps for approval components
     */
    private function synchronizeSteps(int $approvalId): void
    {
        $components = ApprovalComponent::where('approval_id', $approvalId)->orderBy('step')->get();
        $step = 0;
        foreach ($components as $component) {
            $component->step = $step;
            $component->save();

            $step++;
        }
    }
}
