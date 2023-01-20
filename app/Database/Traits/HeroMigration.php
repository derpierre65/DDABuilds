<?php

namespace App\Database\Traits;

use App\Models\Hero;

trait HeroMigration
{
	public function addHero(string $name, bool $isHero = true, bool $isDisabled = false) : void
	{
		Hero::query()->updateOrCreate(['name' => $name], [
			'name' => $name,
			'isHero' => $isHero,
			'isDisabled' => $isDisabled,
		]);
	}

	public function deleteHero(string $name) : void
	{
		Hero::query()->where(['name' => $name])->delete();
	}
}