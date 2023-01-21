<?php

namespace Database\Seeders;

use App\Models\BugReport;
use App\Models\BugReportComment;
use Illuminate\Database\Seeder;

class BugReportSeeder extends Seeder {
	public function run() {
		BugReport::factory()->times(50)->create()->each(function (BugReport $report) {
			$report->comments()->saveMany(BugReportComment::factory()->times(rand(0, 30))->make());
		});
	}
}