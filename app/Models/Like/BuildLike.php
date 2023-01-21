<?php

namespace App\Models\Like;

use App\Models\Build;

/**
 * @method Build getModel()
 */
class BuildLike extends AbstractLike {
	protected static string $baseClass = Build::class;

	public function getNotificationData() : array {
		return [
			'build' => $this->getModel()->getNotificationData(),
		];
	}
}