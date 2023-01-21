<?php

namespace App\Models\Build;

use App\Models\Tower;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property-read int $build_id
 *
 * @property-read Collection<Tower> $towers
 */
class BuildWave extends Model
{
	public $timestamps = false;

	protected $fillable = ['name'];

	public function towers() : BelongsToMany
	{
		return $this->belongsToMany(Tower::class)
			->withPivot([
				'x',
				'y',
				'rotation',
				'size',
			]);
	}
}