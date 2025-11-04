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
        //region squire
        // Spiked Blockade
        $this->updateTower('squire_1', [
            'unit_cost' => 2,
            'mana' => 30,
        ]);
        // Harpoon Tower
        $this->updateTower('squire_2', [
            'unit_cost' => 5,
            'mana' => 80,
        ]);
        // Sniper Cannon
        $this->updateTower('squire_3', [
            'unit_cost' => 4,
            'mana' => 80,
        ]);
        // Bowling Ball Tower
        $this->updateTower('squire_4', [
            'unit_cost' => 4,
            'mana' => 70,
        ]);
        // Slice N' Dice Blockade
        $this->updateTower('squire_5', [
            'unit_cost' => 4,
            'mana' => 100,
        ]);
        //endregion

        //region warden
        // Roots of Purity
        $this->updateTower('warden_1', [
            'unit_cost' => 1,
            'mana' => 20,
        ]);
        // Wisp Den
        $this->updateTower('warden_2', [
            'unit_cost' => 4,
            'mana' => 50,
        ]);
        // Beaming Blossom
        $this->updateTower('warden_3', [
            'unit_cost' => 4,
            'mana' => 70,
        ]);
        // Shroomy Pit
        $this->updateTower('warden_4', [
            'unit_cost' => 1,
            'mana' => 60,
        ]);
        // Sludge Launcher
        $this->updateTower('warden_5', [
            'unit_cost' => 5,
            'mana' => 100,
        ]);
        //endregion

        $this->updateTower('huntress_4', [
            'unit_cost' => 3,
            'mana' => 60,
        ]);
        $this->updateTower('huntress_4', [
            'unit_cost' => 3,
            'mana' => 60,
        ]);

        $this->updateTower('guardian_1', [
            'unit_cost' => 3,
        ]);
    }

    public function down()
    {
        //
    }
};
