<?php

namespace App\Database\Traits;

use App\Models\Hero;
use App\Models\Tower;

trait TowerMigration
{
	public function addTower(string $heroName, string $towerName, int $unitCost, int $maxUnitCost, int $manaCost, bool $isResizable, bool $isRotatable, int $unitType = 0)
	{
		$hero = Hero::query()->where(['name' => $heroName])->first();
		if ( !$hero ) {
			\Log::debug('Fail to create tower, hero does not exists.', [
				'tower' => $towerName,
				'hero' => $heroName,
			]);

			return;
		}

		Tower::query()->updateOrCreate([
			'name' => $towerName,
		], [
			'heroClassID' => $hero->getKey(),
			'isResizable' => $isResizable,
			'isRotatable' => $isRotatable,
			'manaCost' => $manaCost,
			'maxUnitCost' => $maxUnitCost,
			'unitCost' => $unitCost,
			'unitType' => $unitType,
		]);
	}

	public function deleteTower(string $towerName) : void
	{
		Tower::query()->where(['name' => $towerName])->delete();
	}
}