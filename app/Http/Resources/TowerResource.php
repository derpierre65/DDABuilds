<?php

namespace App\Http\Resources;

use App\Models\Tower;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Tower $resource
 */
class TowerResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'hero_id' => $this->resource->hero_id,
			'name' => $this->resource->name,
			'unit_cost' => $this->resource->unit_cost,
			'max_unit_cost' => $this->resource->max_unit_cost,
			'image_size' => $this->resource->image_size,
			'mana' => $this->resource->mana,
			'is_resizable' => $this->resource->is_resizable,
			'is_rotatable' => $this->resource->is_rotatable,
		];
	}
}