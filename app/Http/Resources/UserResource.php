<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read User $resource
 */
class UserResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'name' => $this->resource->name,
			'avatar_hash' => $this->resource->avatar_hash,
			'unread_notifications' => $this->resource->unreadNotifications()->count(),
			'is_maintainer' => $this->resource->getIsMaintainerAttribute(),
		];
	}
}