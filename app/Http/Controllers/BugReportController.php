<?php

namespace App\Http\Controllers;

use App\Http\Resources\BugReportResource;
use App\Models\BugReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BugReportController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(BugReport::class);
	}

	public function index(Request $request)
	{
		if ( !($user = auth()->user()) ) {
			throw new AccessDeniedHttpException();
		}

		$reports = BugReport::query();
		if ( $request->query->getBoolean('mine') ) {
			$reports->where([
				'user_id' => $user->getKey(),
			]);
		}

		return BugReportResource::collection($reports->paginate());
	}

	public function store(Request $request)
	{
		$values = $request->validate([
			'title' => 'required|min:3|max:128',
			'description' => 'nullable|string',
		]);

		/** @var BugReport $report */
		$report = BugReport::query()
			->select(['created_at'])
			->where(['user_id' => auth()->user()->getKey()])
			->latest()
			->first();
		if ( $report && ($diff = $report->created_at->diffInSeconds(now())) < BugReport::WAIT_TIME ) {
			return response()->json([
				'need_wait' => BugReport::WAIT_TIME - $diff,
			]);
		}

		$values['description'] ??= '';

		return new BugReportResource(BugReport::query()->create(array_merge($values, [
			'user_id' => auth()->id(),
			'status' => BugReport::STATUS_OPEN,
		])));
	}

	public function show(BugReport $bugReport)
	{
		$bugReport->loadMissing('user');

		return new BugReportResource($bugReport);
	}

	public function update(Request $request, BugReport $bugReport)
	{
		$success = false;
		if ( $bugReport->status === BugReport::STATUS_OPEN && $request->request->getInt('status', -1) === BugReport::STATUS_CLOSED ) {
			$success = $bugReport->update([
				'status' => BugReport::STATUS_CLOSED,
			]);
		}

		if ( !$success ) {
			throw new BadRequestHttpException();
		}

		return response()->noContent();
	}
}