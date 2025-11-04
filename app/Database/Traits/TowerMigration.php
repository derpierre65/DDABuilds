<?php

namespace App\Database\Traits;

use App\Models\Hero;
use App\Models\Tower;

trait TowerMigration
{
	public function addTower(string $heroName, string $towerName, int $unitCost, int $maxUnitCost, int $manaCost, bool $isResizable, bool $isRotatable, int $unitType = 0): void
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
			'hero_id' => $hero->getKey(),
			'is_resizable' => $isResizable,
			'is_Rotatable' => $isRotatable,
			'mana' => $manaCost,
			'max_unit_cost' => $maxUnitCost,
			'unit_cost' => $unitCost,
			'unit_type' => $unitType,
		]);
	}

    public function updateTower(string $name, array $update): void
    {
        Tower::query()->where(['name' => $name])->update($update);
    }

	public function deleteTower(string $towerName) : void
	{
		Tower::query()->where(['name' => $towerName])->delete();
	}
}