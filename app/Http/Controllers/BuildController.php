<?php

namespace App\Http\Controllers;

use App\Events\BuildViewEvent;
use App\Http\Requests\BuildRequest;
use App\Http\Resources\BuildResource;
use App\Models\Build;
use App\Models\Build\BuildWave;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BuildController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(Build::class);
	}

	public function index(Request $request)
	{
		$builds = Build::query()
			->with(['map', 'gameMode', 'difficulty', 'likeValue']);

		$builds->sort($request->query('sortField'), $request->query('sortOrder'));

		if ( auth()->id() ) {
			if ( $request->query->getBoolean('mine') ) {
				$builds->where('steamID', auth()->id());
			}
			elseif ( $request->query->getBoolean('watch') ) {
				$builds->whereHas('watchStatus');
			}
			elseif ( $request->query->getBoolean('liked') ) {
				$builds->whereHas('likeValue');
			}
		}

		return BuildResource::collection($builds->search([
			'isDeleted' => 0,
			'title' => $request->query('title'),
			'author' => $request->query('author'),
			'map' => $request->query('map'),
			'difficulty' => $request->query('difficulty'),
			'gameMode' => $request->query('gameMode'),
			'hardcore' => $request->query('hardcore'),
			'rifted' => $request->query('rifted'),
			'afkAble' => $request->query('afkAble'),
		])->paginate());
	}

	public function show(Request $request, Build $build)
	{
		BuildViewEvent::dispatch($build, $request->session());

		$build->loadMissing(['map:ID,name', 'difficulty:ID,name', 'gameMode:ID,name', 'waves.towers', 'heroStats', 'likeValue', 'watchStatus']);

		return new BuildResource($build);
	}

	public function store(BuildRequest $request)
	{
		$data = $request->validated();
		/** @var Build $build */
		$build = Build::query()->create(array_merge([
			'date' => time(),
			'steamID' => auth()->id(),
		], $data));

		foreach ( $data['heroStats'] as $key => $heroStats ) {
			$heroStats['heroID'] = $key;
			$build->addStats($heroStats);
		}

		$waves = $waveTowers = [];
		foreach ( $data['towers'] as $tower ) {
			$waveTowers[$tower['waveID']] = $waveTowers[$tower['waveID']] ?? [];
			$waveTowers[$tower['waveID']][] = $tower;
		}

		foreach ( $data['waves'] as $key => $name ) {
			if ( empty($waveTowers[$key]) ) {
				continue;
			}

			/** @var BuildWave $buildWave */
			$buildWave = $build->waves()->create([
				'name' => $name,
			]);
			$waves[$key] = $buildWave;
		}

		foreach ( $data['towers'] as $key => $tower ) {
			if ( !isset($waves[$tower['waveID']]) ) {
				continue;
			}

			$waves[$tower['waveID']]->towers()->create(array_merge($tower, [
				'towerID' => $tower['ID'],
				'overrideUnits' => $tower['size'],
			]));
		}

		$build->generateThumbnail();

		return response()->json($build);
	}

	public function update(BuildRequest $request, Build $build)
	{
		$data = $request->all();

		$build->heroStats()->delete();

		$build->waves()->each(function (BuildWave $wave) {
			$wave->towers()->delete();
		});

		foreach ( $data['heroStats'] as $key => $heroStats ) {
			$heroStats['heroID'] = $key;
			$build->addStats($heroStats);
		}

		$waves = $waveTowers = [];
		foreach ( $data['towers'] as $tower ) {
			$waveTowers[$tower['waveID']][] = $tower;
		}

		foreach ( $data['waves'] as $key => $name ) {
			if ( empty($waveTowers[$key]) ) {
				continue;
			}

			$waves[$key] = $name;
		}

		$existsCount = $build->waves()->count();
		$create = count($waves) - $existsCount;
		if ( $create > 0 ) {
			foreach ( array_slice($waves, $create * -1, null, true) as $key => $name ) {
				$waves[$key] = $build->waves()->create([
					'name' => $name,
				]);
			}
		}
		elseif ( $create < 0 ) {
			$build->waves()->get()->slice($create)->each(function (BuildWave $wave) {
				$wave->delete();
			});
		}

		$i = 0;
		foreach ( array_slice($waves, 0, $existsCount, true) as $key => $name ) {
			/** @var BuildWave $wave */
			$wave = $build->waves()->get()->get($i);
			$wave->update(['name' => $name,]);

			$waves[$key] = $wave;
			$i++;
		}

		foreach ( $data['towers'] as $key => $tower ) {
			if ( !isset($waves[$tower['waveID']]) ) {
				continue;
			}

			$waves[$tower['waveID']]->towers()->create(array_merge($tower, [
				'towerID' => $tower['ID'],
				'overrideUnits' => $tower['size'],
			]));
		}

		$build->update($data);
		$build->generateThumbnail();

		return response()->noContent();
	}

	public function destroy(Request $request, Build $build)
	{
		if ( $build->update(['isDeleted' => 1]) ) {
			return response()->noContent();
		}

		throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function watch(Build $build)
	{
		$this->authorize('watch', $build);

		if ( $build->watchStatus ) {
			Build\BuildWatch::query()->where([
				'buildID' => $build->getKey(),
				'steamID' => auth()->id(),
			])->delete();
			$watchStatus = 0;
		}
		else {
			$build->watchStatus()->create([
				'buildID' => $build->getKey(),
				'steamID' => auth()->id(),
			]);
			$watchStatus = 1;
		}

		return [
			'watchStatus' => $watchStatus,
		];
	}
}