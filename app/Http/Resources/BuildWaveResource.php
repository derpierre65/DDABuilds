<?php

namespace App\Http\Resources;

use App\Models\Build\BuildWave;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read BuildWave $resource
 */
class BuildWaveResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'name' => $this->resource->name,
			'towers' => BuildTowerResource::collection($this->whenLoaded('towers')),
		];
	}
}