<?php

namespace App\Http\Resources;

use App\Models\Build\BuildComment;
use Illuminate\Http\Resources\MissingValue;

/**
 * @property-read BuildComment $resource
 */
class BuildCommentResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->id,
			'user_id' => $this->resource->user_id,
			'user_name' => $this->whenLoaded('user', fn() => $this->resource->user?->name),
			'user_avatar' => $this->whenLoaded('user', fn() => $this->resource->user?->avatar_hash),
			'description' => $this->resource->description,
			'likes' => $this->resource->likes,
			'dislikes' => $this->resource->dislikes,
			'like_value' => $this->whenLoaded('likeValue', fn() => $this->resource->likeValue?->like_value) ?? new MissingValue(),
			'created_at' => $this->resource->created_at,
		];
	}
}