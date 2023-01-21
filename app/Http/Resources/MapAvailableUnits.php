<?php

namespace App\Http\Resources;

use App\Models\MapAvailableUnit;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read MapAvailableUnit $resource
 */
class MapAvailableUnits extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'difficulty_id' => $this->resource->difficulty_id,
			'units' => $this->resource->units,
		];
	}
}