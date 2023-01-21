<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
	public function run()
	{
		$steamUsers = [
			[
				'id' => 76561198054589426,
				'name' => 'derpierre65',
				'avatar_hash' => 'ab788fdd0d6636f946729c3fa1456ec2858db472',
			],
			[
				'id' => 76561198080938830,
				'name' => 'dragongun100',
				'avatar_hash' => 'ab788fdd0d6636f946729c3fa1456ec2858db472',
			],
			[
				'id' => 76561198011599149,
				'name' => 'kazeshoni',
				'avatar_hash' => 'ab788fdd0d6636f946729c3fa1456ec2858db472',
			],
		];

		foreach ( $steamUsers as $steamUser ) {
			if ( User::query()->find($steamUser['id']) === null ) {
				User::query()->create($steamUser);
			}
		}
	}
}
