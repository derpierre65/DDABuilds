<?php

namespace page;

use data\build\Build;
use system\Core;
use system\exception\IllegalLinkException;
use system\exception\NamedUserException;

class BuildPage extends BuildAddPage {
	/** @inheritDoc */
	public $loginRequired = false;

	/** @inheritDoc */
	public $templateName = 'buildAdd';

	/** @var string view mode (edit/view) */
	public $action = 'view';

	/** @inheritDoc */
	public function readParameters() {
		AbstractPage::readParameters();

		if ( !isset($_REQUEST['id']) ) {
			throw new IllegalLinkException();
		}

		$this->build = new Build($_REQUEST['id']);
		if ( !$this->build->getObjectID() ) {
			throw new IllegalLinkException();
		}

		if ( $this->build->fk_buildstatus === Build::STATUS_PRIVATE && !$this->build->isCreator() ) {
			throw new NamedUserException('Sorry, this build is private');
		}
		elseif ( $this->build->deleted ) {
			throw new NamedUserException('The Build you are trying to access got deleted. If the deletion was made by mistake please contact the site administrator.');
		}

		$this->map = $this->build->getMap();
		if ( $this->build->isCreator() && !isset($_REQUEST['view']) ) {
			$this->action = 'edit';
		}

		if ( $this->action === 'view' && !$this->build->isCreator() ) {
			$this->build->update([
				'views' => $this->build->views + 1,
			]);
		}

		$this->pageTitle = $this->build->name;
	}

	/** @inheritDoc */
	public function readData() {
		parent::readData();

		$this->buildName = $this->build->name;
		$this->author = $this->build->author;
		$this->expPerRun = $this->build->expPerRun;
		$this->timePerRun = $this->build->timePerRun;
		$this->description = $this->build->description;
	}

	/** @inheritDoc */
	public function assignVariables() {
		parent::assignVariables();

		Core::getTPL()->assign([
			'action' => $this->action,
		]);
	}
}