<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $map_id
 * @property-read int $difficulty_id
 * @property int $units
 */
class MapAvailableUnit extends Model
{
	public $incrementing = false;

	public $timestamps = false;
}