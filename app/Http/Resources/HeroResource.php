<?php

namespace App\Http\Resources;

use App\Models\Hero;

/**
 * @property-read Hero $resource
 */
class HeroResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'name' => $this->resource->name,
			'is_hero' => $this->resource->is_hero,
			'towers' => TowerResource::collection($this->whenLoaded('towers')),
		];
	}
}