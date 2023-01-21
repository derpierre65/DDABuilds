<?php

namespace App\Http\Resources;

use App\Models\BugReport;

/**
 * @property-read BugReport $resource
 */
class BugReportResource extends JsonResource
{
	public function toArray($request) : array
	{
		return [
			'id' => $this->resource->id,
			'user_id' => $this->resource->user_id,
			'user_name' => $this->whenLoaded('user', fn() => $this->resource->user->name),
			'title' => $this->resource->title,
			'description' => $this->resource->description,
			'status' => $this->resource->status,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
		];
	}
}