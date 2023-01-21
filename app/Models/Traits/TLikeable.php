<?php

namespace App\Models\Traits;

use App\Models\Like;

/**
 * @property-read Like $likeValue
 */
trait TLikeable
{
	public function likeValue()
	{
		return $this
			->hasOne(Like::class, 'object_id', $this->getKeyName())
			->where([
				'user_id' => auth()->user()?->getKey() ?? '0',
				'object_type' => $this->getLikeObjectType(),
			]);
	}

	public function getLikeObjectType() : string
	{
		return lcfirst(array_slice(explode('\\', static::class), -1)[0]);
	}
}