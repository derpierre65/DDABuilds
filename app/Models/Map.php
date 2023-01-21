<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read int $id
 * @property string $name
 * @property int $units
 * @property int $map_category_id
 *
 * @property-read string $public_path
 * @property-read Collection<MapAvailableUnit> $difficultyUnits
 */
class Map extends Model
{
	public $timestamps = false;

	protected $guarded = [];

	public function difficultyUnits(): HasMany
	{
		return $this->hasMany(MapAvailableUnit::class);
	}

	public function getPublicPathAttribute(): string
	{
		return public_path('assets/images/map/' . $this->name . '.png');
	}
}