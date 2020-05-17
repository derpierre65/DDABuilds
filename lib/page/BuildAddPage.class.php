<?php

namespace page;

use data\build\status\BuildStatusList;
use data\difficulty\DifficultyList;
use data\heroClass\HeroClassList;
use data\map\Map;
use data\tower\Tower;
use data\tower\TowerList;
use system\Core;
use system\exception\IllegalLinkException;
use system\util\StringUtil;

class BuildAddPage extends AbstractPage {
	public $loginRequired = true;

	/** @var Map */
	public $map;

	/** @var TowerList */
	public $towers;

	public $buildName = '';

	public $author = '';

	/** @var HeroClassList */
	public $heroClasses;

	/** @var DifficultyList */
	public $difficulties;

	/** @var BuildStatusList */
	public $buildStatuses;

	/**
	 * @throws IllegalLinkException
	 */
	public function readParameters() {
		parent::readParameters();

		if ( !isset($_REQUEST['id']) ) {
			throw new IllegalLinkException();
		}

		$this->map = new Map($_REQUEST['id']);
		if ( !$this->map->getObjectID() ) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		if ( empty($_POST) ) {
			$this->buildName = '';
			$this->author = StringUtil::encodeHTML(Core::getUser()->displayName);
		}

		// get hero classes
		$this->heroClasses = new HeroClassList();
		$this->heroClasses->getConditionBuilder()->add('isDisabled = 0');
		$this->heroClasses->readObjects();

		// get build statuses (private, unlisted)
		$this->buildStatuses = new BuildStatusList();
		$this->buildStatuses->readObjects();

		// get difficulties
		$this->difficulties = new DifficultyList();
		$this->difficulties->readObjects();

		// get towers
		$towers = new TowerList();
		$towers->readObjects();
		$this->towers = [];
		/** @var Tower $tower */
		foreach ( $towers as $tower ) {
			$this->towers[$tower->fk_class][] = $tower;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		Core::getTPL()->assign([
			'action'        => 'add',
			'map'           => $this->map,
			'towers'        => $this->towers,
			'buildName'     => $this->buildName,
			'author'        => $this->author,
			'difficulties'  => $this->difficulties,
			'buildStatuses' => $this->buildStatuses,
			'heroClasses'   => $this->heroClasses->getObjects(),
		]);
	}
}