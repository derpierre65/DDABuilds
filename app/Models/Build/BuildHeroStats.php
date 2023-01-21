<?php

namespace App\Models\Build;

use Illuminate\Database\Eloquent\Model;

class BuildHeroStats extends Model {
	protected $primaryKey = null;

	public $timestamps = false;

	public $incrementing = false;

	protected $fillable = [
		'buildID',
		'heroID',
		'hp',
		'damage',
		'range',
		'rate',
	];
}