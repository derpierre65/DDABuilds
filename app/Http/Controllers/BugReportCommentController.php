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
			$bugReport->comments()->latest()->with('user')->paginate()
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

		/** @var BugReportComment $comment */
		$comment = BugReportComment::query()
			->select(['created_at'])
			->where(['user_id' => auth()->user()->getKey()])
			->latest()
			->first();
		if ( $comment && ($diff = $comment->created_at->diffInSeconds(now())) < BugReportComment::WAIT_TIME) {
			return response()->json([
				'need_wait' => BugReportComment::WAIT_TIME - $diff,
			]);
		}

		/** @var BugReportComment $bugReportComment */
		$bugReportComment = $bugReport->comments()->create(array_merge($values, [
			'user_id' => auth()->user()->getKey(),
		]));
		$bugReportComment->loadMissing('user');

		return [
			'need_wait' => BugReportComment::WAIT_TIME,
			'comment' => new BugReportCommentResource($bugReportComment),
		];
	}
}
