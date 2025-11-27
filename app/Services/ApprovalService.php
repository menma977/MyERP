<?php

namespace App\Services;

use App\Interfaces\ApprovalServiceInterface;
use App\Services\Method\BinaryService;
use App\Services\Method\OutsiderService;
use Illuminate\Database\Eloquent\Model;

class ApprovalService
{
	/**
	 * Creates a binary approval service instance for the given Eloquent model.
	 * This method initializes a BinaryService that handles approval workflows
	 * for Eloquent models using binary flags to represent approval steps.
	 *
	 * @param Model $model The Eloquent model that requires approval processing
	 * @return ApprovalServiceInterface Returns a configured BinaryService instance for the model
	 *
	 * @example
	 * // Example usage with an Eloquent model
	 * $document = Document::find(1);
	 * $approvalService = app(ApprovalService::class)->forBinary($document);
	 */
	public function forBinary(Model $model): ApprovalServiceInterface
	{
		/** @var BinaryService $service */
		$service = app(BinaryService::class);

		return $service::model($model->getMorphClass(), $model->getKey());
	}

	/**
	 * Creates an outsider approval service instance for non-Eloquent entities.
	 * This method initializes an OutsiderService that handles approval workflows
	 * for entities that are not Eloquent models, using a type identifier and ID.
	 * This is useful for approving external resources or entities that don't
	 * have corresponding Eloquent models.
	 *
	 * @param string $requestableType The type identifier for the entity requiring approval
	 * @param int|string $requestableId The unique identifier of the entity
	 * @return ApprovalServiceInterface Returns a configured OutsiderService instance
	 *
	 * @example
	 *  OUTSIDER helper: no Eloquent model, type + id.
	 *    app(ApprovalService::class)
	 *        ->forOutsider('FINANCE', 45)
	 *        ->user($userId)
	 *        ->approve();
	 *
	 * @noinspection PhpUnused
	 */
	public function forOutsider(string $requestableType, int|string $requestableId): ApprovalServiceInterface
	{
		/** @var OutsiderService $service */
		$service = app(OutsiderService::class);

		return $service::model($requestableType, $requestableId);
	}
}
