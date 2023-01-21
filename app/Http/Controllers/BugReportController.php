<?php

namespace App\Http\Controllers;

use App\Http\Resources\BugReportResource;
use App\Models\BugReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BugReportController extends Controller {
	public function __construct() {
		$this->authorizeResource(BugReport::class);
	}

	public function index(Request $request) {
		if ( !auth()->id() ) {
			throw new AccessDeniedHttpException();
		}

		$reports = BugReport::query()->orderBy('time', 'DESC');
		if ( $request->query->getBoolean('mine') ) {
			$reports->where(['steamID' => auth()->id()]);
		}

		return BugReportResource::collection($reports->paginate());
	}

	public function store(Request $request) {
		$values = $request->validate([
			'title' => 'required|min:3|max:128',
			'description' => 'nullable|string',
		]);

		/** @var BugReport $report */
		$report = BugReport::query()
			->select(['time'])
			->where([
				'steamID' => auth()->id(),
				[DB::raw('UNIX_TIMESTAMP() - time'), '<', BugReport::WAIT_TIME],
			])
			->first();
		if ( $report ) {
			return response()->json([
				'needWait' => BugReport::WAIT_TIME - (time() - $report->time),
			]);
		}

		$values['description'] = $values['description'] ?? '';

		return new BugReportResource(BugReport::create(array_merge($values, [
			'time' => time(),
			'steamID' => auth()->id(),
			'status' => BugReport::STATUS_OPEN,
		])));
	}

	public function show(BugReport $bugReport) {
		$bugReport->loadMissing('user');

		return new BugReportResource($bugReport);
	}

	public function update(Request $request, BugReport $bugReport) {
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