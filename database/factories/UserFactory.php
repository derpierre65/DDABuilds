<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
	public function definition() : array
	{
		return [
			'id' => $this->faker->steamID,
			'name' => $this->faker->userName,
			'avatar_hash' => '2b056d9ce9fa8d9a838d5535067de49cddea8076',
		];
	}
}
