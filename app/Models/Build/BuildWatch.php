<?php

namespace App\Models\Build;

use Illuminate\Database\Eloquent\Model;

class BuildWatch extends Model {
	protected $table = 'build_watch';

	public $incrementing = false;

	protected $primaryKey = null;
}