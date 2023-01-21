<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int    $ID
 * @property-read string $name
 */
class GameMode extends Model {
	/** @inheritdoc */
	protected $primaryKey = 'id';

	/** @inheritdoc */
	public $timestamps = false;

	/** @inheritdoc */
	protected $fillable = [
		'name',
	];
}
