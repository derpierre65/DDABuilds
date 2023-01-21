<?php

namespace Database\Factories;

use App\Models\BugReportComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class BugReportCommentFactory extends Factory
{
	protected $model = BugReportComment::class;

	public function definition() : array
	{
		return [
			'user_id' => $this->faker->steamID,
			'time' => $this->faker->unixTime(),
			'description' => $this->faker->sentences($this->faker->numberBetween(10, 100), true),
		];
	}
}
