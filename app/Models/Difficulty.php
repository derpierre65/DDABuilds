<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int    $id
 * @property-read string $name
 */
class Difficulty extends Model {
	use HasFactory;

	/** @inheritdoc */
	protected $table = 'difficulty';

	/** @inheritdoc */
	protected $primaryKey = 'id';

	/** @inheritdoc */
	public $timestamps = false;

	/** @inheritdoc */
	protected $fillable = [
		'name',
	];
}
