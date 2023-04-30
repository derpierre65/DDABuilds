<?php

use App\Database\Traits\HeroMigration;
use App\Database\Traits\MapMigration;
use App\Database\Traits\TowerMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	use HeroMigration;
	use TowerMigration;
	use MapMigration;

    public function up()
    {
        // mana 50, 3
        // mana 80, 4
        // mana 70, 4
	    $this->addMap('theForsakenTemple', 130, 'campaign');
	    $this->addMap('castleArmory', 90, 'encore');
		$this->addHero('guardian');
		$this->addTower('guardian', 'guardian_1', 2, 2, 20, false, true);
		$this->addTower('guardian', 'guardian_2', 3, 3, 20, false, false);
		$this->addTower('guardian', 'guardian_3', 3, 3, 50, false, false);
		$this->addTower('guardian', 'guardian_4', 4, 4, 80, false, true);
		$this->addTower('guardian', 'guardian_5', 4, 4, 70, false, true);
    }

    public function down()
    {
    }
};
