<?php

namespace App\Http\Resources;

use App\Models\Map;

/**
 * @property-read Map $resource
 */
class MapResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'name' => $this->resource->name,
			'units' => $this->resource->units,
			'difficulty_units' => MapAvailableUnits::collection($this->whenLoaded('difficultyUnits')),
		];
	}
}