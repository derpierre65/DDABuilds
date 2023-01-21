<?php

namespace App\Http\Resources;

use App\Models\Build;
use Illuminate\Http\Resources\MissingValue;

/**
 * @property-read Build $resource
 */
class BuildResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->id,
			'author' => $this->resource->author,
			'title' => $this->resource->title,
			'description' => $this->resource->description,
			'user_id' => $this->resource->user_id,
			'game_mode_id' => $this->resource->game_mode_id,
			'game_mode_name' => $this->whenLoaded('gameMode', fn() => $this->resource->gameMode->name),
			'difficulty_id' => $this->resource->difficulty_id,
			'difficulty_name' => $this->whenLoaded('difficulty', fn() => $this->resource->difficulty->name),
			'map_id' => $this->resource->map_id,
			'map_name' => $this->whenLoaded('map', fn() => $this->resource->map->name),
			'build_status' => $this->resource->build_status,
			'views' => $this->resource->views,
			'likes' => $this->resource->likes,
			'comments' => $this->resource->comments,
			'is_hardcore' => $this->resource->is_hardcore,
			'is_afk_able' => $this->resource->is_afk_able,
			'is_rifted' => $this->resource->is_rifted,
			'time_per_run' => $this->resource->time_per_run,
			'exp_per_run' => $this->resource->exp_per_run,
			'waves' => BuildWaveResource::collection($this->whenLoaded('waves')),
			'hero_stats' => BuildHeroResource::collection($this->whenLoaded('heroStats')),
			'like_value' => $this->whenLoaded('likeValue', fn() => $this->resource->likeValue->like_value) ?? new MissingValue(),
			'watch_status' => $this->whenLoaded('watchStatus', 1) ?? new MissingValue(),
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
		];
	}
}