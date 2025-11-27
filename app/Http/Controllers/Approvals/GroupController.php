<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
	/**
	 * Group Index
	 *
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$group = ApprovalGroup::when($request->input('search'), function ($query) use ($request) {
			$query->where('name', 'like', '%' . $request->input('search') . '%');
		})->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

		if ($request->input('type') === 'collection') {
			return $group->get();
		}

		return $group->paginate($request->input('per_page', 10));
	}

	/**
	 * Group Store
	 *
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
		]);

		$group = new ApprovalGroup;
		$group->name = $request->input('name');
		$group->save();

		return [
			'message' => trans('messages.success.store', ['target' => $group->name], App::getLocale()),
		];
	}

	/**
	 * ApprovalGroup Show
	 *
	 * Show the specified resource.
	 */
	public function show(Request $request)
	{
		return ApprovalGroup::with([
			'contributors',
			'contributors.user',
		])->findOrFail($request->route('id'));
	}

	/**
	 * ApprovalGroup Update
	 *
	 * Update the specified resource in storage.
	 */
	public function update(Request $request)
	{
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
		]);

		$group = ApprovalGroup::findOrFail($request->route('id'));
		$group->name = $request->input('name');
		$group->save();

		return [
			'message' => trans('messages.success.update', ['target' => $group->name], App::getLocale()),
		];
	}

	/**
	 * ApprovalGroup Delete
	 *
	 * Remove the specified resource from storage.
	 */
	public function delete(Request $request)
	{
		$group = ApprovalGroup::findOrFail($request->route('id'));
		if ($group->contributors()->exists()) {
			throw ValidationException::withMessages([
				'message' => trans('messages.fail.delete.cost', ['attribute' => $group->name, 'target' => 'Contributor'], App::getLocale()),
			]);
		}
		$group->delete();

		return [
			'message' => trans('messages.success.delete', ['target' => $group->name], App::getLocale()),
		];
	}
}
