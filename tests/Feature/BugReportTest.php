<?php

namespace Tests\Feature;

use App\Models\BugReport;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BugReportTest extends TestCase
{
	public function createBugReport(&$title = null, &$description = null) : TestResponse
	{
		$title = $title ?? implode(' ', $this->faker->words);
		$description = $description ?? $this->faker->text;

		return $this->postJson('/api/bug-reports/', [
			'title' => $title,
			'description' => $description,
		]);
	}

	public function testUnauthenticated()
	{
		$response = $this->createBugReport();
		$response->assertForbidden();
	}

	public function testInvalidInput()
	{
		$this->loginAsTester();

		$title = '   x';
		$response = $this->createBugReport($title);
		$response->assertStatus(422);
	}

	public function testBugReportCreate() : BugReport
	{
		$this->loginAsTester();

		// delete old issues to prevent the spam protection
		BugReport::query()->where('user_id', $this->getTestUser()->id)->delete();

		// create issue
		$response = $this->createBugReport($title, $description);
		$response->assertCreated();

		// fetch the issue from response
		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('id', $jsonResponse);
		/** @var BugReport $report */
		$report = BugReport::query()->find($jsonResponse['id']);
		$this->assertNotNull($report);

		// check issue values
		$this->assertEquals($this->getTestUser()->id, $report->user_id);
		$this->assertEquals($title, $report->title);
		$this->assertEquals($description, $report->description);
		$this->assertEquals(BugReport::STATUS_OPEN, $report->status);

		return $report;
	}

	/**
	 * @depends testBugReportCreate
	 */
	public function testBugReportCreateSpamProtection(BugReport $report) : BugReport
	{
		$this->loginAsTester();

		$response = $this->post('/api/bug-reports/', [
			'title' => 'title',
			'description' => 'description',
		]);
		$response->assertOk();

		$jsonResponse = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('need_wait', $jsonResponse);

		return $report;
	}

	/**
	 * @depends testBugReportCreateSpamProtection
	 */
	public function testBugReportViewForbidden(BugReport $report) : BugReport
	{
		$response = $this->get('/api/bug-reports/'.$report->getKey());
		$response->assertForbidden();

		return $report;
	}

	/**
	 * @depends testBugReportViewForbidden
	 */
	public function testBugReportViewForbiddenOthers(BugReport $report) : BugReport
	{
		$report->update([
			'user_id' => $this->getSubTestUser()->getKey(),
		]);

		$this->loginAsTester();
		$response = $this->get('/api/bug-reports/'.$report->getKey());
		$response->assertForbidden();

		return $report;
	}

	/**
	 * @depends testBugReportViewForbiddenOthers
	 */
	public function testBugReportView(BugReport $report)
	{
		$report->update([
			'user_id' => $this->getTestUser()->getKey(),
		]);

		$this->loginAsTester();
		$response = $this->get('/api/bug-reports/'.$report->getKey());
		$response->assertOk();
	}

	public function testBugReportList()
	{
		// guests should not have permission
		$response = $this->get('/api/bug-reports/');
		$response->assertForbidden();

		// guests should not have permission on mine list
		$response = $this->get('/api/bug-reports/?mine=1');
		$response->assertForbidden();

		// non maintainer should not have permission
		$this->loginAsTester();
		$response = $this->get('/api/bug-reports/');
		$response->assertForbidden();

		// non maintainer should have permission to mine issue list
		$response = $this->get('/api/bug-reports/?mine=1');
		$response->assertOk();
	}
}
