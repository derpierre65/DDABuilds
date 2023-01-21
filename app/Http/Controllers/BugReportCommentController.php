<?php

namespace App\Http\Controllers;

use App\Http\Resources\BugReportCommentResource;
use App\Models\BugReport;
use App\Models\BugReportComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BugReportCommentController extends Controller
{
	public function __construct()
	{
		$this->middleware('can:view,bug_report');
	}

	public function index(BugReport $bugReport)
	{
		return BugReportCommentResource::collection(
			$bugReport->comments()->orderBy('time', 'DESC')->with('user')->paginate()
		);
	}

	public function store(Request $request, BugReport $bugReport)
	{
		if ( $bugReport->status === BugReport::STATUS_CLOSED ) {
			throw new AccessDeniedHttpException('Bug report closed.');
		}

		$values = $request->validate([
			'description' => 'required|string',
		]);

		/** @var BugReportComment $latestBugReportComment */
		$latestBugReportComment = BugReportComment::select(['time'])->where([['steamID', auth()->id()], [DB::raw('UNIX_TIMESTAMP() - time'), '<', BugReportComment::WAIT_TIME]])->first();
		if ( $latestBugReportComment ) {
			return response()->json([
				'needWait' => BugReportComment::WAIT_TIME - (time() - $latestBugReportComment->time),
			]);
		}

		/** @var BugReportComment $bugReportComment */
		$bugReportComment = $bugReport->comments()->create(array_merge($values, [
			'time' => time(),
			'steamID' => auth()->id(),
		]));
		$bugReportComment->load('user');

		return [
			'needWait' => BugReportComment::WAIT_TIME,
			'comment' => new BugReportCommentResource($bugReportComment),
		];
	}
}
