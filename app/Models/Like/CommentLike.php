<?php

namespace App\Models\Like;

use App\Models\Build\BuildComment;

/**
 * @method BuildComment getModel()
 */
class CommentLike extends AbstractLike
{
	protected static bool $enabledDislikes = true;

	protected static string $baseClass = BuildComment::class;

	public function getNotificationData() : array
	{
		return [
			'build' => $this->getModel()->build->getNotificationData(),
		];
	}
}