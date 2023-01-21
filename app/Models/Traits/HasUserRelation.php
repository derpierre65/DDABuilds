<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 *
 * @property-read User $user
 */
trait HasUserRelation
{
	public function user() : BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}