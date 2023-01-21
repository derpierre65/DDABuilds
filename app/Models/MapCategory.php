<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 */
class MapCategory extends Model
{
	protected $guarded = [];

	public $timestamps = false;

	public function maps() : HasMany
	{
		return $this->hasMany(Map::class);
	}
}