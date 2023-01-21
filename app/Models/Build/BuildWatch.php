<?php

namespace App\Models\Build;

use Illuminate\Database\Eloquent\Model;

class BuildWatch extends Model {
	public $timestamps = false;

	public $incrementing = false;

	protected $primaryKey = null;

	protected $guarded = [];
}