<?php

namespace App\Http\Resources;

use App\Models\BugReportComment;

/**
 * @property-read BugReportComment $resource
 */
class BugReportCommentResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->getKey(),
			'user_id' => $this->resource->user_id,
			'user_name' => $this->whenLoaded('user', fn() => $this->resource->user->name),
			'description' => $this->resource->description,
			'created_at' => $this->resource->created_at,
		];
	}
}