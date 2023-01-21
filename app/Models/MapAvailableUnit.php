<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $mapID
 * @property-read int $difficultyID
 * @property-read int $units
 */
class MapAvailableUnit extends Model {
	protected $table = 'map_available_unit';

	public $incrementing = false;

	public $timestamps = false;
}