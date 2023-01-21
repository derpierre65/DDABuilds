<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $name
 */
class Difficulty extends Model
{
	public $timestamps = false;

	protected $guarded = [];
}