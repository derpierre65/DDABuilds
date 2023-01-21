<?php

namespace App\Providers;

use App\Faker\Provider\FakerProvider;
use App\Http\Resources\JsonResource;
use App\Models\Build;
use App\Observers\BuildCommentObserver;
use App\Observers\BuildObserver;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	public function register()
	{
		// register the custom faker provider class
		$this->app->singleton(Generator::class, function () {
			$faker = Factory::create();
			$faker->addProvider(new FakerProvider($faker));

			return $faker;
		});

		JsonResource::withoutWrapping();
	}

	public function boot()
	{
		Build::observe(BuildObserver::class);
		Build\BuildComment::observe(BuildCommentObserver::class);
	}
}