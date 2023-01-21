<?php

namespace Database\Factories;

use App\Models\Build;
use App\Models\Difficulty;
use App\Models\GameMode;
use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildFactory extends Factory
{
	protected $model = Build::class;

	public function definition() : array
	{
		return [
			'author' => substr($this->faker->name(), 0, 20),
			'title' => $this->faker->words($this->faker->numberBetween(1, 3), true),
			'description' => $this->faker->sentences($this->faker->numberBetween(10, 100), true),
			'user_id' => $this->faker->steamID,
			'map_id' => Map::query()->inRandomOrder()->first()->getKey(),
			'difficulty_id' => Difficulty::query()->inRandomOrder()->first()->getKey(),
			'game_mode_id' => GameMode::query()->inRandomOrder()->first()->getKey(),
			'build_status' => $this->faker->numberBetween(1, 3),
			'is_hardcore' => $this->faker->boolean(),
			'is_afk_able' => $this->faker->boolean(),
			'is_rifted' => $this->faker->boolean(),
			'views' => $this->faker->numberBetween(500, 500000),
		];
	}
}