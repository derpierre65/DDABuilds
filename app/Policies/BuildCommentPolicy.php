<?php

namespace App\Policies;

use App\Models\Build\BuildComment;
use App\Models\User;

class BuildCommentPolicy
{
	public function viewAny(?User $user) : bool
	{
		return true;
	}

	public function create(User $user) : bool
	{
		return true;
	}

	public function like(User $user, BuildComment $comment) : bool
	{
		return $comment->user_id !== $user->getKey();
	}
}