<?php

namespace App\Http\Controllers;

use App\Http\Resources\DifficultyResource;
use App\Http\Resources\HeroResource;
use App\Http\Resources\MapCategoryResource;
use App\Http\Resources\MapResource;
use App\Models\Difficulty;
use App\Models\GameMode;
use App\Models\Hero;
use App\Models\Map;
use App\Models\MapCategory;

class MapController extends Controller
{
	public function index()
	{
		return MapCategoryResource::collection(MapCategory::query()->with('maps')->get());
	}

	public function editor(Map $map)
	{
		$map->loadMissing('difficultyUnits');

		return [
			'map' => new MapResource($map),
			'heroes' => HeroResource::collection(Hero::query()->with('towers')->get()),
			'difficulties' => DifficultyResource::collection(Difficulty::all()),
			'gameModes' => GameMode::all(),
		];
	}
}