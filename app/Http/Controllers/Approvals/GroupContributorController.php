<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approval\ApprovalGroupContributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class GroupContributorController extends Controller
{
	/**
	 * Group Contributor Index
	 *
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$groupContributors = ApprovalGroupContributor::with([
			'group',
			'user.employee.employeePersonal',
		])->when($request->input('search'), function ($query) use ($request) {
			$query->whereHas('user', function ($query) use ($request) {
				$query->where('name', 'like', '%' . $request->input('search') . '%');
			});
		})->when($request->route('group_id'), function ($query) use ($request) {
			$query->where('approval_group_id', $request->route('group_id'));
		})->orderBy($request->input('sort_by', 'id'), $request->input('sort_order', 'desc'));

		if ($request->input('type') === 'collection') {
			return $groupContributors->get();
		}

		return $groupContributors->paginate($request->input('per_page', 10));
	}

	/**
	 * Group Contributor Store
	 *
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'user_id' => ['required', 'exists:users,id'],
		]);

		$group = ApprovalGroupContributor::where('approval_group_id', $request->route('group_id'))
			->where('user_id', $request->input('user_id'))
			->first();
		if ($group) {
			throw ValidationException::withMessages([
				'user_id' => ['The user has already been added to the group.'],
			]);
		}

		$groupContributors = new ApprovalGroupContributor;
		$groupContributors->approval_group_id = $request->route('group_id');
		$groupContributors->user_id = $request->input('user_id');
		$groupContributors->save();

		return [
			'message' => trans('messages.success.store', ['target' => $groupContributors->user->name], App::getLocale()),
		];
	}

	/**
	 * Group Contributor Show
	 *
	 * Show the specified resource.
	 */
	public function show(Request $request)
	{
		return ApprovalGroupContributor::with([
			'group',
			'user.employee.employeePersonal',
		])->findOrFail($request->route('id'));
	}

	/**
	 * Group Contributor Update
	 *
	 * Update the specified resource in storage.
	 */
	public function update(Request $request)
	{
		$request->validate([
			'user_id' => ['required', 'exists:users,id'],
		]);

		$groupContributors = ApprovalGroupContributor::findOrFail($request->route('id'));
		$groupContributors->approval_group_id = $request->route('group_id');
		$groupContributors->user_id = $request->input('user_id');
		$groupContributors->save();

		return [
			'message' => trans('messages.success.update', ['target' => $groupContributors->user->name], App::getLocale()),
		];
	}

	/**
	 * Group Contributor Delete
	 *
	 * Remove the specified resource from storage.
	 */
	public function delete(Request $request)
	{
		$groupContributors = ApprovalGroupContributor::findOrFail($request->route('id'));
		$groupContributors->delete();

		return [
			'message' => trans('messages.success.delete', ['target' => $groupContributors->user->name], App::getLocale()),
		];
	}
}
