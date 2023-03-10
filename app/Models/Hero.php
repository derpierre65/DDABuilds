<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $ID
 * @property-read string $name
 * @property-read int $isHero
 * @property-read int $isDisabled
 */
class Hero extends Model
{
	protected $primaryKey = 'ID';

	protected $guarded = [];

	public $timestamps = false;

	public function towers(): HasMany
	{
		return $this->hasMany(Tower::class, 'heroClassID', 'ID');
	}
}