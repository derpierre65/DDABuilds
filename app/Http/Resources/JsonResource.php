<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

class JsonResource extends BaseJsonResource
{
	public static function collection($resource)
	{
		return tap(new CustomResourceCollection($resource, static::class), function ($collection) {
			if ( property_exists(static::class, 'preserveKeys') ) {
				$collection->preserveKeys = (new static([]))->preserveKeys === true;
			}
		});
	}
}