<?php

namespace App\Observers;

use App\Models\Build;

class BuildObserver
{
	public function deleting(Build $build)
	{
		if ( file_exists($build->getPublicThumbnailPathAttribute()) ) {
			unlink($build->getPublicThumbnailPathAttribute());
		}
	}
}