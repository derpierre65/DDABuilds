<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

class CustomResourceCollection extends AnonymousResourceCollection
{
	public function paginationInformation($request, $paginated, $default) : array
	{
		return [
			'pagination' => Arr::only($paginated, [
				'current_page',
				'last_page',
				'per_page',
				'from',
				'to',
				'total',
			]),
		];
	}
}