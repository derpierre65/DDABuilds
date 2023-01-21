<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapCategory extends Model {
	protected $table = 'map_category';

	protected $primaryKey = 'ID';

	protected $guarded = [];

	public $timestamps = false;

	public function maps() {
		return $this->hasMany(Map::class, 'mapCategoryID', 'ID');
	}
}