<?php

use App\Database\Traits\HeroMigration;
use App\Database\Traits\MapMigration;
use App\Database\Traits\TowerMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNewMapsAndHeroes extends Migration
{
	use MapMigration;
	use HeroMigration;
	use TowerMigration;

	public function up()
	{
		$this->addMap('theBazaar', 100, 'campaign');
		$this->addMap('theLostMetropolis', 130, 'encore');
		$this->addMap('yuletideVillage', 120, 'bonus');
		$this->addHero('summoner');

		$this->addTower('summoner', 'archerMinion', 2, 2, 60, false, false);
		$this->addTower('summoner', 'spiderMinion', 3, 3, 70, false, false);
		$this->addTower('summoner', 'sirenMinion', 4, 4, 120, false, false);
		$this->addTower('summoner', 'mageMinion', 4, 4, 100, false, false);
		$this->addTower('summoner', 'ogreMinion', 5, 5, 150, false, false);
	}

	public function down()
	{
		$this->deleteMap('theBazaar');
		$this->deleteMap('theLostMetropolis');
		$this->deleteMap('yuletideVillage');
		$this->deleteMapCategory('bonus');

		$this->deleteTower('archerMinion');
		$this->deleteTower('spiderMinion');
		$this->deleteTower('sirenMinion');
		$this->deleteTower('mageMinion');
		$this->deleteTower('ogreMinion');

		$this->deleteHero('summoner');
	}
}
