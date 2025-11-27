<?php

namespace App\Services\Method;

use App\Enums\ApprovalStatusEnum;
use App\Enums\ApprovalTypeEnum;
use App\Models\Approval\Approval;
use App\Models\Approval\ApprovalComponent;
use App\Models\Approval\ApprovalContributor;
use App\Models\Approval\ApprovalEvent;
use App\Models\Approval\ApprovalEventComponent;
use App\Models\Approval\ApprovalEventContributor;
use App\Models\Approval\ApprovalFlowComponent;
use App\Models\Approval\ApprovalGroup;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class OutsiderService extends BinaryService
{
	protected ?string $requestableType = null;

	protected int|string|null $requestableId = null;

	protected ?int $binary = null;

	protected ?string $status = null;

	protected User $user;

	/**
	 * Creates a new instance of the service for a non-Eloquent entity.
	 * This static factory method initializes the service with a type and ID,
	 * allowing for fluent method chaining in the approval workflow for entities
	 * that don't have corresponding Eloquent models.
	 *
	 * @param string $type The type identifier for the entity requiring approval
	 * @param int|string $id The unique identifier of the entity
	 * @return OutsiderService A new instance of the approval service configured for the entity
	 */
	public static function model(string $type, int|string $id): self
	{
		$instance = new self;
		$instance->requestableType = $type;
		$instance->requestableId = $id;

		return $instance;
	}

	/**
	 * Retrieves the approval event for the current non-Eloquent entity with its relationships.
	 * This method fetches the approval event and its associated components and contributors.
	 * It can be filtered by status and binary step if they have been set on the service instance.
	 *
	 * Unlike the BinaryService, this method works with entities that don't have Eloquent models,
	 * using the requestableType and requestableId properties to identify the entity.
	 *
	 * @return ApprovalEvent|null The approval event with loaded relationships
	 */
	public function get(): ?ApprovalEvent
	{
		return ApprovalEvent::with([
			'components.contributors.user',
		])
			->withSum('components', 'step')
			->where('requestable_type', $this->requestableType)
			->where('requestable_id', $this->requestableId)
			->when($this->status, function ($query) {
				return $query->where('status', $this->status);
			})->when($this->binary, function ($query) {
				return $query->where('approvable_id', $this->binary);
			})->first();
	}

	/**
	 * Creates or retrieves an approval event for the current non-Eloquent entity.
	 * This method handles the creation of approval events, components, and contributors
	 * based on the flow component configuration.
	 * If an approval event already exists
	 * for the entity, it returns the existing one.
	 * Otherwise, it creates a new one with
	 * all necessary parts and contributors based on the approval flow configuration.
	 *
	 * Unlike BinaryService, this method uses the requestableType property to find the
	 * flow component rather than a model's morph class.
	 *
	 * @return ApprovalEvent The created or existing approval event
	 *
	 * @throws Throwable When a flow component is not found for the current entity
	 *
	 * @noinspection DuplicatedCode
	 */
	public function store(): ApprovalEvent
	{
		return DB::transaction(function () {
			$flowComponent = ApprovalFlowComponent::where('key', $this->requestableType)->first();
			if (!$flowComponent) {
				throw ValidationException::withMessages([
					'message' => "Flow component with $this->requestableType not found. Please register it first.",
				]);
			}

			$approvalEvent = ApprovalEvent::where('requestable_type', $this->requestableType)->where('requestable_id', $this->requestableId)->first();
			if (!$approvalEvent) {
				$approval = Approval::where('approval_flow_id', $flowComponent->approval_flow_id)->first();
				if (!$approval) {
					throw ValidationException::withMessages([
						'message' => "Approval flow with ID $flowComponent->approval_flow_id not found. Please check the flow component configuration.",
					]);
				}

				$approvalEvent = new ApprovalEvent;
				$approvalEvent->requestable_type = $this->requestableType ?? '';
				$approvalEvent->requestable_id = (string)($this->requestableId ?? '');
				$approvalEvent->approval_id = $approval->id;
				$approvalEvent->type = $approval->type ?? ApprovalTypeEnum::PARALLEL;
				$approvalEvent->status = ApprovalStatusEnum::DRAFT;
				$approvalEvent->step = 0;
				$approvalEvent->target = 0;
				$approvalEvent->save();

				$approvalComponent = ApprovalComponent::where('approval_id', $approval->id)->get();
				$binary = 0;
				$hasAnyContributor = false;
				$componentsWithContributors = [];
				$componentsWithoutContributors = [];

				foreach ($approvalComponent as $component) {
					$binary |= 1 << $component->step;

					$approvalEventComponent = new ApprovalEventComponent;
					$approvalEventComponent->approval_event_id = $approvalEvent->id;
					$approvalEventComponent->name = $component->name;
					$approvalEventComponent->step = 0 | 1 << $component->step;
					$approvalEventComponent->color = $component->color;
					$approvalEventComponent->type = $component->type;
					$approvalEventComponent->save();

					$approvalContributor = ApprovalContributor::where('approval_component_id', $component->id)->get();
					$componentHasContributor = false;

					foreach ($approvalContributor as $contributor) {
						if ($contributor->approvable_type === Role::class) {
							$role = Role::where('id', $contributor->approvable_id)->first();
							if ($role) {
								foreach ($role->users as $user) {
									$approvalContributor = new ApprovalEventContributor;
									$approvalContributor->approval_event_component_id = $approvalEventComponent->id;
									$approvalContributor->user_id = $user->id;
									$approvalContributor->save();
									$componentHasContributor = true;
								}
							}
						} elseif ($contributor->approvable_type === ApprovalGroup::class) {
							$group = ApprovalGroup::where('id', $contributor->approvable_id)->first();
							if ($group) {
								foreach ($group->contributors as $user) {
									$approvalContributor = new ApprovalEventContributor;
									$approvalContributor->approval_event_component_id = $approvalEventComponent->id;
									$approvalContributor->user_id = $user->user_id;
									$approvalContributor->save();
									$componentHasContributor = true;
								}
							}
						} else {
							$approvalContributor = new ApprovalEventContributor;
							$approvalContributor->approval_event_component_id = $approvalEventComponent->id;
							$approvalContributor->user_id = (int)$contributor->approvable_id;
							$approvalContributor->save();
							$componentHasContributor = true;
						}
					}

					if ($componentHasContributor) {
						$hasAnyContributor = true;
						$componentsWithContributors[] = $component->step;
					} else {
						$approvalEventComponent->approved_at = now();
						$approvalEventComponent->save();
						$componentsWithoutContributors[] = $component->step;
					}
				}

				$approvalEvent->status = ApprovalStatusEnum::DRAFT;
				$approvalEvent->target = $binary;

				if (!$hasAnyContributor) {
					$approvalEvent->status = ApprovalStatusEnum::APPROVED;
					$approvalEvent->step = $binary;
					$approvalEvent->approved_at = now();
				} else {
					foreach ($componentsWithoutContributors as $step) {
						$approvalEvent->step |= 1 << $step;
					}

					if (!empty($componentsWithContributors)) {
						$firstComponentWithContributors = min($componentsWithContributors);
						$approvalEvent->step |= 1 << $firstComponentWithContributors;
					}

					if (($approvalEvent->step & $binary) === $binary) {
						$approvalEvent->status = ApprovalStatusEnum::APPROVED;
						$approvalEvent->approved_at = now();
					}
				}

				$approvalEvent->save();
			}

			return $approvalEvent;
		});
	}
}
