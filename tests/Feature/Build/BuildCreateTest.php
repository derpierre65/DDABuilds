<?php

namespace Tests\Feature\Build;

use App\Models\Build;
use App\Models\Hero;
use App\Models\Tower;
use Illuminate\Support\Arr;
use Tests\TestCase;

class BuildCreateTest extends TestCase
{
	public function testCreateAsGuest()
	{
		$response = $this->post('/api/builds/');
		$response->assertForbidden();
	}

	public function testValidator()
	{
		$this->loginAsTester();

		$response = $this->postJson('/api/builds/', []);
		$response->assertStatus(422);

		$data = json_decode($response->getContent(), true);
		$this->assertNotEmpty($data['message']);
		$this->assertNotEmpty($data['errors']);
	}

	public function buildCreate(array $overrideData = []) : Build
	{
		/** @var Tower $tower */
		$tower = Tower::query()->where(['is_resizable' => true])->inRandomOrder()->first();
		$heroes = Hero::query()->where(['is_hero' => true, 'is_disabled' => false])->inRandomOrder()->limit(3)->get();

		$body = array_merge([
			'title' => $this->faker->sentence(),
			'description' => '',
			'author' => $this->getTestUser()->name,
			'time_per_run' => '123',
			'exp_per_run' => '456',
			'is_afk_able' => $this->faker->boolean,
			'is_hardcore' => $this->faker->boolean,
			'is_rifted' => $this->faker->boolean,
			'game_mode_id' => 2,
			'difficulty_id' => 2,
			'build_status' => Build::STATUS_PUBLIC,
			'map_id' => 1,
			'hero_stats' => [],
			'waves' => [
				'first_wave',
				'second_wave',
			],
			'towers' => [
				[
					'id' => $tower->getKey(),
					'wave_id' => 0,
					'x' => 500,
					'y' => 500,
					'rotation' => 90,
					'size' => 0,
				],
				[
					'id' => $tower->getKey(),
					'wave_id' => 1,
					'x' => 400,
					'y' => 400,
					'rotation' => 90,
					'size' => $tower->max_unit_cost - 1,
				],
			],
		], $overrideData);

		/** @var Hero $hero */
		foreach ( $heroes as $hero ) {
			$body['hero_stats'][] = [
				'id' => $hero->getKey(),
				'hp' => 1,
				'rate' => 2,
				'damage' => 3,
				'range' => 4,
			];
		}

		$response = $this->post('/api/builds/', $body);
		$response->assertOk();

		$responseData = json_decode($response->getContent(), true);
		$id = $responseData['id'];

		/** @var Build $build */
		$build = Build::query()->with(['gameMode', 'difficulty', 'map', 'waves', 'heroStats', 'likeValue', 'watchStatus'])->find($id);

		// check build data
		$this->assertSame($this->getTestUser()->getKey(), $build->user_id);

		foreach ( $body as $key => $value ) {
			if ( !is_array($value) ) {
				$this->assertSame($build->{$key}, $value);
			}
		}

		// check the waves
		$waves = $build->waves()->with('towers')->get();
		foreach ( $body['waves'] as $waveIndex => $waveName ) {
			/** @var Build\BuildWave $buildWave */
			$buildWave = $waves[$waveIndex];
			$this->assertSame($buildWave->name, $waveName);
			$tower = $waves[$waveIndex]->towers->first();
			foreach ( Arr::except($body['towers'][$waveIndex], ['id', 'wave_id']) as $towerIndex => $bodyTower ) {
				$this->assertSame($tower->pivot->{$towerIndex}, $bodyTower);
			}
		}

		$this->assertSame($build->heroStats()->count(), count($body['hero_stats']), 'more hero stats available than given');

		/** @var Hero $hero */
		$heroStatsById = array_column($body['hero_stats'], null, 'id');
		foreach ( $build->heroStats()->get() as $key => $hero ) {
			$this->assertSame(Arr::except($heroStatsById[$hero->getKey()], ['id']), [
				'hp' => $hero->pivot->hp,
				'rate' => $hero->pivot->rate,
				'damage' => $hero->pivot->damage,
				'range' => $hero->pivot->range,
			]);
		}

		// check if thumbnail was generated
		// TODO fix github pipeline, imagescale fails
		$this->assertFileExists($build->getPublicThumbnailPathAttribute(), 'build thumbnail was not generated');

		return $build;
	}

	public function testCreate() : Build
	{
		$this->loginAsTester();
		$build = $this->buildCreate();
		$build->delete();

		// create a private build
		return $this->buildCreate([
			'is_afk_able' => false,
			'is_hardcore' => false,
			'is_rifted' => false,
			'build_status' => Build::STATUS_PRIVATE,
		]);
	}

	/** @depends testCreate */
	public function testPermissions(Build $build) : Build
	{
		$response = $this->getJson('/api/builds/'.$build->id);
		$response->assertForbidden();

		// login as sub tester, another users should not have access on private builds
		$this->loginAsSubTester();
		$response = $this->getJson('/api/builds/'.$build->id);
		$response->assertForbidden();

		// login as creator, creator should have access
		$this->loginAsTester();
		$response = $this->getJson('/api/builds/'.$build->id);
		$response->assertOk();

		return $build;
	}

	/** @depends testPermissions */
	public function testDelete(Build $build)
	{
		// test permission
		$response = $this->deleteJson('/api/builds/'.$build->id);
		$response->assertForbidden();

		// test as non creator
		$this->loginAsSubTester();
		$response = $this->deleteJson('/api/builds/'.$build->id);
		$response->assertForbidden();

		$this->loginAsTester();
		$response = $this->deleteJson('/api/builds/'.$build->id);
		$response->assertNoContent();

		// check if it deleted
		$this->assertSame(1, Build::query()->find($build->id)->is_deleted);
	}
}