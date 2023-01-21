<?php

namespace App\Http\Resources;

use App\Models\BugReportComment;
use Illuminate\Http\Resources\MissingValue;

/** @mixin BugReportComment */
class BugReportCommentResource extends JsonResource {
	public function toArray($request) : array
	{
		return [
			'ID' => $this->commentID,
			'steamID' => $this->steamID,
			'time' => $this->time,
			'description' => $this->description,
			'steamName' => $this->whenLoaded('user', fn() => $this->user->name),
		];
	}
}
