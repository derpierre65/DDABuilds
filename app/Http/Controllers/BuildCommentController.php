<?php

namespace App\Http\Controllers;

use App\Http\Resources\BuildCommentResource;
use App\Models\Build;
use App\Models\Build\BuildComment;
use Illuminate\Http\Request;

class BuildCommentController extends Controller
{
	public function __construct()
	{
		$this->middleware('can:view,build');
		$this->authorizeResource(BuildComment::class);
	}

	public function index(Build $build)
	{
		return BuildCommentResource::collection(
			$build
				->commentList()
				->latest()
				->with(['user', 'likeValue'])
				->paginate()
		);
	}

	public function store(Request $request, Build $build)
	{
		$values = $request->validate([
			'description' => 'required|string',
		]);

		$comment = $build->commentList()->create(array_merge($values, [
			'user_id' => auth()->user()->getKey(),
		]));
		$comment->loadMissing(['user']);

		return new BuildCommentResource($comment);
	}
}