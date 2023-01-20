<?php

namespace App\Database\Traits;

use App\Models\Map;
use App\Models\MapCategory;

trait MapMigration
{
	public function addMap(string $name, int $units, string $categoryName) : void
	{
		Map::query()->updateOrCreate(['name' => $name], [
			'units' => $units,
			'mapCategoryID' => $this->addMapCategory($categoryName)->getKey(),
		]);
	}

	public function addMapCategory(string $categoryName) : MapCategory
	{
		/** @var MapCategory $mapCategory */
		$mapCategory = MapCategory::query()->firstOrCreate(['name' => $categoryName]);

		return $mapCategory;
	}

	public function deleteMapCategory(string $categoryName) : bool
	{
		return MapCategory::query()->where(['name' => $categoryName])->delete();
	}

	public function deleteMap(string $name) : void
	{
		Map::query()->where(['name' => $name])->delete();
	}
}