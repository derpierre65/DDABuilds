<?php

namespace App\Http\Resources;

use App\Models\MapCategory;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read MapCategory $resource
 */
class MapCategoryResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'name' => $this->resource->name,
			'maps' => MapResource::collection($this->whenLoaded('maps')),
		];
	}
}