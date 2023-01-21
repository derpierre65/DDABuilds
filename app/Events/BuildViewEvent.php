<?php

namespace App\Events;

use App\Models\Build;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Session\Store;

class BuildViewEvent
{
	use Dispatchable;

	public function __construct(Build $build, private readonly Store $session)
	{
		if ( !$this->isViewed($build) ) {
			$build->increment('views');
			$this->store($build);
		}
	}

	private function store(Build $build) : void
	{
		$this->session->push('builds_viewed', $build->id);
	}

	private function isViewed(Build $build) : bool
	{
		return in_array($build->getKey(), $this->session->get('builds_viewed', []));
	}
}