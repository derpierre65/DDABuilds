<?php

namespace Tests\Browser\Tests;

use App\Models\BugReport;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Navigation;
use Tests\Browser\Components\UserNavigation;
use Tests\Browser\Pages\BugReportPage;
use Tests\DuskTestCase;

class BugReportTest extends DuskTestCase {
	protected static $title = '';

	public function testCreate() {
		BugReport::query()->where('steamID', 1337)->delete();

		$this->browse(function (Browser $I) {
			$I->loginAsTester();
			$I->visit('/');

			$I->within(new Navigation(), function($I) {
				$I->navigateTo('Report Bug');
			});

			$I->waitForText('Issues reported here are community reviewed and the reviewers are not related to Chromatic Games or Dungeon Defenders: Awakened employees in anyway.');

			$title = implode(' ', $this->faker->words);
			$description = $this->faker->text;

			// fill input fields
			$I->type($this->getVueSelector('input', 'title'), $title);
			$I->typeCkeditor($this->getVueSelector('input', 'description'), $description);
			$I->click($this->getVueSelector('input', 'checkbox'));
			$I->click($this->getVueSelector('input', 'save'));

			// check text on view
			$I->on(new BugReportPage())->checkValues($title, $description);

			// check if action menu is not visible for normal user
			$I->assertMissing($this->getVueSelector('action-menu'));

			self::$title = $title;
		});
	}

	/**
	 * @depends testCreate
	 */
	public function testList() {
		$this->browse(function (Browser $I) {
			$I->loginAsTester();
			$I->visit('/');

			$I->within(new UserNavigation(), function(Browser $I) {
				$I->navigateTo('My Bug Reports');
			});

			$I->waitForText(self::$title);
			$I->clickLink(self::$title);

			$I->on(new BugReportPage())->checkValues(self::$title);
		});
	}
}