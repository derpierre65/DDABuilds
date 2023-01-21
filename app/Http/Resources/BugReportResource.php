<?php

namespace App\Http\Resources;

use App\Models\BugReport;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin BugReport
 */
class BugReportResource extends JsonResource {
	public function toArray($request) {
		return [
			'ID' => $this->reportID,
			'title' => $this->title,
			'description' => $this->description,
			'time' => $this->time,
			'steamID' => $this->steamID,
			'steamName' => $this->relationLoaded('user') ? $this->user->name : new MissingValue(),
			'status' => $this->status,
		];
	}
}
