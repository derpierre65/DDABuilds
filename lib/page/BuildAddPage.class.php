<?php

namespace page;

use data\build\Build;
use data\build\status\BuildStatusList;
use data\difficulty\DifficultyList;
use data\gamemode\GamemodeList;
use data\heroClass\HeroClassList;
use data\map\Map;
use data\tower\Tower;
use data\tower\TowerList;
use system\Core;
use system\exception\IllegalLinkException;
use system\util\StringUtil;

class BuildAddPage extends AbstractPage {
	/** @inheritDoc */
	public $loginRequired = true;

	/** @inheritDoc */
	public $pageTitle = 'Create Build';

	/** @var Build */
	public $build;

	/** @var Map */
	public $map;

	/** @var TowerList */
	public $towers;

	/** @var HeroClassList */
	public $heroClasses;

	/** @var DifficultyList */
	public $difficulties;

	/** @var BuildStatusList */
	public $buildStatuses;

	/** @var GamemodeList */
	public $gamemodes;

	/** @var TowerList */
	public $towerList;

	public $buildName = '';

	public $author = '';

	public $timePerRun = '';

	public $expPerRun = '';

	public $description = '';

	/** @var string view mode (edit/view) */
	public $action = 'edit';

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

		$this->build = new Build(null);
	}

	/** @inheritDoc */
	public function readData() {
		parent::readData();

		if ( empty($_POST) ) {
			$this->buildName = '';
			$this->author = StringUtil::encodeHTML(Core::getUser()->name);
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

		// get game modes
		if ( $this->action === 'edit' ) {
			$this->gamemodes = new GamemodeList();
			$this->gamemodes->readObjects();
		}

		// get towers
		$this->towerList = new TowerList();
		$this->towerList->readObjects();
		$this->towers = [];

		$useMU = false;

		/** @var Tower $tower */
		foreach ( $this->towerList as $tower ) {
			if ( $this->heroClasses->search($tower->fk_class) ) {
				$this->towers[$tower->fk_class][] = $tower;

				if ( $tower->mu ) {
					$useMU = true;
				}
			}
		}

		Core::getTPL()->assign('showMU', $useMU);
	}

	/** @inheritDoc */
	public function assignVariables() {
		parent::assignVariables();

		Core::getTPL()->assign([
			'action'          => 'add',
			'build'           => $this->build,
			'map'             => $this->map,
			'towers'          => $this->towers,
			'buildName'       => $this->buildName,
			'author'          => $this->author,
			'difficulties'    => $this->difficulties,
			'gamemodes'       => $this->gamemodes,
			'buildStatuses'   => $this->buildStatuses,
			'expPerRun'       => $this->expPerRun,
			'timePerRun'      => $this->timePerRun,
			'description'     => $this->description,
			'availableTowers' => $this->towerList->getObjects(),
			'heroClasses'     => $this->heroClasses,
		]);
	}
}