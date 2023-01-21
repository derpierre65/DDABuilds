<?php

namespace App\Models\Traits;

use App\Models\Build;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin Model
 *
 * @property int $build_id
 * @property-read Build $build
 */
trait HasBuildRelation
{
	public function build() : BelongsTo
	{
		return $this->belongsTo(Build::class);
	}
}