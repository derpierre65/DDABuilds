<?php

namespace App\Http\Resources;

use App\Models\DatabaseNotification;

/**
 * @property-read DatabaseNotification $resource
 */
class NotificationResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->id,
			'type' => $this->getType(),
			'created' => $this->resource->created_at,
			'read' => !!$this->resource->read_at,
			'data' => $this->resource->data,
		];
	}

	public function getType() : string
	{
		$split = explode('\\', $this->resource->type);

		return lcfirst(substr(array_pop($split), 0, -12)); // -12 = Notification
	}
}