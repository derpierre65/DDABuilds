<?php

namespace App\Http\Controllers;

use App\Events\BuildViewEvent;
use App\Http\Requests\BuildRequest;
use App\Http\Resources\BuildResource;
use App\Models\Build;
use App\Models\Build\BuildWave;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BuildController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(Build::class);
	}

	public function index(Request $request)
	{
		$query = Build::query()->with(['map', 'gameMode', 'difficulty', 'likeValue']);
		$query->sort($request->query('sort-field'), $request->query('sort-order'));

		if ( $user = auth()->user() ) {
			if ( $request->query->getBoolean('mine') ) {
				$query->where('user_id', $user->getKey());
			}
			elseif ( $request->query->getBoolean('watch') ) {
				$query->whereHas('watchStatus');
			}
			elseif ( $request->query->getBoolean('liked') ) {
				$query->whereHas('likeValue');
			}
		}

		$searchParameters = $request->query->all();
		$searchParameters = [
			'title' => $searchParameters['title'] ?? null,
			'author' => $searchParameters['author'] ?? null,
			'map' => $searchParameters['map'] ?? null,
			'difficulty' => $searchParameters['difficulty'] ?? null,
			'gameMode' => $searchParameters['game-mode'] ?? null,
			'is_hardcore' => $searchParameters['is-hardcore'] ?? null,
			'is_rifted' => $searchParameters['is-rifted'] ?? null,
			'is_afk_able' => $searchParameters['is-afk-able'] ?? null,
		];

		$where = [
			'is_deleted' => false,
		];

		if ( $searchParameters['title'] ) {
			$where[] = ['title', 'like', '%'.$searchParameters['title'].'%'];
		}
		if ( $searchParameters['author'] ) {
			$where[] = ['author', 'like', '%'.$searchParameters['author'].'%'];
		}

		foreach ( ['map', 'gameMode', 'difficulty'] as $relation ) {
			if ( $value = $searchParameters[$relation] ) {
				$query->whereHas($relation, fn(Builder $query) => $query->whereIn('name', explode(',', $value)));
			}
		}

		// boolean columns
		foreach ( ['is_hardcore', 'is_afk_able', 'is_rifted'] as $field ) {
			if ( isset($searchParameters[$field]) && ($searchParameters[$field] === 'true' || (int) $searchParameters[$field]) ) {
				$where[$field] = true;
			}
		}

		$query
			->where($where)
			->where(function ($query) use ($user) {
				$query->where([
					'build_status' => Build::STATUS_PUBLIC,
				]);

				if ( $user ) {
					$query->orWhere([
						'user_id' => $user->getKey(),
					]);
				}
			});

		return BuildResource::collection($query->paginate());
	}

	public function show(Request $request, Build $build)
	{
		BuildViewEvent::dispatch($build, $request->session());

		$build->loadMissing([
			'map:id,name',
			'difficulty:id,name',
			'gameMode:id,name',
			'waves',
			'waves.towers',
			'heroStats',
			'likeValue',
			'watchStatus',
		]);

		return new BuildResource($build);
	}

	public function store(BuildRequest $request)
	{
		$data = $request->validated();

		/** @var Build $build */
		$build = Build::query()->create(array_merge([
			'user_id' => auth()->id(),
		], $data));

		if ( !empty($data['hero_stats']) ) {
			$build->syncStats($data['hero_stats']);
		}

		$waves = $waveTowers = [];
		foreach ( $data['towers'] as $tower ) {
			$waveTowers[$tower['wave_id']] = $waveTowers[$tower['wave_id']] ?? [];
			$waveTowers[$tower['wave_id']][] = $tower;
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
			if ( !isset($waves[$tower['wave_id']]) ) {
				continue;
			}

			$waves[$tower['wave_id']]->towers()->attach($tower['id'], Arr::except($tower, ['id', 'wave_id']));
		}

		Log::debug('test', [
			$build->generateThumbnail(),
		]);

		return response()->json($build);
	}

	public function update(BuildRequest $request, Build $build)
	{
		$data = $request->validated();

		$build->waves->each(function(BuildWave $wave) {
			$wave->towers()->sync([]);
		});

		if ( !empty($data['hero_stats']) ) {
			$build->syncStats($data['hero_stats']);
		}

		$waves = $waveTowers = [];
		foreach ( $data['towers'] as $tower ) {
			$waveTowers[$tower['wave_id']][] = $tower;
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

		foreach ( array_slice($waves, 0, $existsCount, true) as $index => $name ) {
			/** @var BuildWave $wave */
			$wave = $build->waves->get($index);
			$wave->update(['name' => $name]);
			$waves[$index] = $wave;
		}

		foreach ( $data['towers'] as $key => $tower ) {
			if ( !isset($waves[$tower['wave_id']]) ) {
				continue;
			}

			$waves[$tower['wave_id']]->towers()->attach($tower['id'], Arr::except($tower, ['id', 'wave_id']));
		}

		$build->update($data);
		$build->generateThumbnail();

		return response()->noContent();
	}

	public function destroy(Request $request, Build $build)
	{
		if ( $build->update(['is_deleted' => 1]) ) {
			return response()->noContent();
		}

		throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function watch(Build $build)
	{
		$this->authorize('watch', $build);

		if ( $build->watchStatus()->first() ) {
			$build->watchStatus()->detach(auth()->user()->getKey());
			$watchStatus = 0;
		}
		else {
			$build->watchStatus()->attach(auth()->user()->getKey());
			$watchStatus = 1;
		}

		return response()->json([
			'watch_status' => $watchStatus,
		]);
	}
}