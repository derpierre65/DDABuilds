<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read int $is_hero
 * @property-read int $is_disabled
 */
class Hero extends Model
{
	protected $guarded = [];

	public $timestamps = false;

	public function towers() : HasMany
	{
		return $this->hasMany(Tower::class);
	}
}