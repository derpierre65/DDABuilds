<?php

namespace App\Console\Commands;

use App\Models\Build;
use Illuminate\Console\Command;

class BuildGenerateThumbnail extends Command
{
	protected $signature = 'build:generate-thumbnail {id}';

	protected $description = 'Generate a new thumbnail for given build id';

	public function handle() : int
	{
		/** @var Build $build */
		$build = Build::query()->findOrFail($this->argument('id'));
		$build->generateThumbnail();

		return self::SUCCESS;
	}
}
