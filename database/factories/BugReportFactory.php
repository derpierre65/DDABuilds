<?php

namespace Database\Factories;

use App\Models\BugReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class BugReportFactory extends Factory {
	protected $model = BugReport::class;

	public function definition() : array
	{
		return [
			'steamID'     => $this->faker->steamID,
			'time'        => $this->faker->unixTime(),
			'title'       => $this->faker->words($this->faker->numberBetween(3, 7), true),
			'description' => $this->faker->sentences($this->faker->numberBetween(10, 100), true),
		];
	}
}