<?php

namespace App\Http\Resources;

use App\Models\Hero;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Hero $resource
 */
class BuildHeroResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'hp' => $this->resource->pivot->hp,
			'damage' => $this->resource->pivot->damage,
			'range' => $this->resource->pivot->range,
			'rate' => $this->resource->pivot->rate,
		];
	}
}