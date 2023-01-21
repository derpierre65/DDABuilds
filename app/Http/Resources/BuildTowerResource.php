<?php

namespace App\Http\Resources;

use App\Models\Tower;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Tower $resource
 */
class BuildTowerResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'x' => $this->resource->pivot->x,
			'y' => $this->resource->pivot->y,
			'rotation' => $this->resource->pivot->rotation,
			'size' => $this->resource->pivot->size,
		];
	}
}