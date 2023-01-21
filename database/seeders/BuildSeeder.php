<?php

namespace Database\Seeders;

use App\Models\Build;
use App\Models\Build\BuildWave;
use App\Models\Hero;
use App\Models\Tower;
use Faker\Generator;
use Illuminate\Database\Seeder;

class BuildSeeder extends Seeder {
	public function run(Generator $faker) {
		$heros = Hero::query()->where('is_hero', 1)->get();

		// generate builds
		$builds = Build::factory()->times(21)->create();

		// generate stuff for builds
		$builds->each(function (Build $build) use ($faker, $heros) {
			for ( $i = 0, $max = $faker->numberBetween(1, 2);$i < $max;$i++ ) {
				// generate a random wave
				/** @var BuildWave $buildWave */
				$buildWave = BuildWave::query()->create([
					'build_id' => $build->id,
					'name' => 'wave '.$faker->word.($i + 1),
				]);

				// generate random towers for this wave
				for ( $j = 0, $maxTowers = $faker->numberBetween(4, 15);$j < $maxTowers;$j++ ) {
					/** @var Tower $tower */
					$tower = Tower::query()->where('unit_cost', '>', 0)->inRandomOrder()->first();

					$buildWave->towers()->attach($tower->getKey(), [
						'rotation' => $tower->is_rotatable ? $faker->numberBetween(0, 359) : 0,
						'size' => $tower->is_resizable ? $faker->numberBetween($tower->unit_cost, $tower->max_unit_cost) : 0,
						'x' => $faker->numberBetween(0, 1100),
						'y' => $faker->numberBetween(0, 800),
					]);
				}
			}

			foreach ( $heros->random(3) as $hero ) {
				$build->heroStats()->create([
					'hero_id' => $hero->getKey(),
					'hp' => $faker->numberBetween(0, 10000),
					'damage' => $faker->numberBetween(0, 10000),
					'range' => $faker->numberBetween(0, 10000),
					'rate' => $faker->numberBetween(0, 10000),
				]);
			}

			for ( $i = 0, $count = $faker->numberBetween(0, 25);$i < $count;$i++ ) {
				$build->commentList()->create([
					'user_id' => $faker->steamID,
					'description' => substr($faker->sentences($faker->numberBetween(10, 100), true), 0, 1000),
				]);
			}

			$build->generateThumbnail();
		});
	}
}
