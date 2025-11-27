<?php

namespace App\Abstracts;

use App\Interfaces\ApprovalServiceInterface;
use App\Models\Approval\ApprovalEvent;
use App\Models\User;
use App\Observers\CreatedByObserver;
use App\Observers\DeletedByObserver;
use App\Observers\UpdatedByObserver;
use App\Services\ApprovalService;
use App\Traits\CreatedByTrait;
use App\Traits\DeletedByTrait;
use App\Traits\UpdatedByTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Throwable;

#[ObservedBy([CreatedByObserver::class, UpdatedByObserver::class, DeletedByObserver::class])]
abstract class ApprovalAbstract extends Model
{
	use CreatedByTrait, DeletedByTrait, UpdatedByTrait;

	/**
	 * @return MorphOne<ApprovalEvent, $this>
	 */
	public function event(): MorphOne
	{
		return $this->morphOne(ApprovalEvent::class, 'requestable');
	}

	/**
	 * @throws Throwable
	 */
	public function initEvent(User $user): void
	{
		$this->approvalService()->model($this::class, (int)$this->getKey())->user($user->id)->store();
	}

	/**
	 * @throws Throwable
	 *
	 * @noinspection DuplicatedCode
	 */
	public function approve(User $user): void
	{
		$approvalEvent = $this->approvalService()->user($user->id)->approve();
		$this->onApprove($approvalEvent);
	}

	/**
	 * @throws Throwable
	 *
	 * @noinspection DuplicatedCode
	 */
	public function reject(User $user): void
	{
		$approvalEvent = $this->approvalService()->user($user->id)->reject();
		$this->onReject($approvalEvent);
	}

	/**
	 * @throws Throwable
	 *
	 * @noinspection DuplicatedCode
	 */
	public function cancel(User $user): void
	{
		$approvalEvent = $this->approvalService()->user($user->id)->cancel();
		$this->onCancel($approvalEvent);
	}

	/**
	 * @throws Throwable
	 */
	public function rollback(User $user): void
	{
		$approvalEvent = $this->approvalService()->user($user->id)->rollback();
		$this->onRollback($approvalEvent);
	}

	/**
	 * @throws Throwable
	 */
	public function force(User $user, ?int $binary = null, ?string $status = null): void
	{
		$approvalEvent = $this->approvalService()->user($user->id)->binary($binary ?? 0)->status($status ?? '')->force();
		$this->onForce($approvalEvent);
	}

	protected function approvalService(): ApprovalServiceInterface
	{
		/** @var ApprovalService $factory */
		$factory = app(ApprovalService::class);

		return $factory->forBinary($this);
	}

	protected function onApprove(ApprovalEvent $approvalEvent): void
	{
	}

	protected function onReject(ApprovalEvent $approvalEvent): void
	{
	}

	protected function onCancel(ApprovalEvent $approvalEvent): void
	{
	}

	protected function onRollback(ApprovalEvent $approvalEvent): void
	{
	}

	protected function onForce(ApprovalEvent $approvalEvent): void
	{
	}
}
