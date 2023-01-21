<?php

namespace App\Providers;

use App\Models\Build;
use App\Models\Build\BuildComment;
use App\Models\BugReport;
use App\Policies\BuildCommentPolicy;
use App\Policies\BuildPolicy;
use App\Policies\BugReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	protected $policies = [
		Build::class => BuildPolicy::class,
		BugReport::class => BugReportPolicy::class,
		BuildComment::class => BuildCommentPolicy::class,
	];

	public function boot() {
		$this->registerPolicies();
	}
}
