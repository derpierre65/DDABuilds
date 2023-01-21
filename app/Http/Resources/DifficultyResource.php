<?php

namespace App\Http\Resources;

use App\Models\Difficulty;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Difficulty $resource
 */
class DifficultyResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'name' => $this->resource->name,
		];
	}
}