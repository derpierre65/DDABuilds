<?php

namespace data;

use system\Core;
use system\database\util\PreparedStatementConditionBuilder;

class DatabaseObjectList implements \Countable, \SeekableIterator {
	/**
	 * class name
	 * @var    string
	 */
	public $className = '';

	/**
	 * result objects
	 * @var    DatabaseObject[]
	 */
	public $objects = [];

	/**
	 * ids of result objects
	 * @var    integer[]
	 */
	public $objectIDs;

	/**
	 * list of index to object relation
	 * @var    integer[]
	 */
	protected $indexToObject;

	/**
	 * sql conditions
	 * @var    PreparedStatementConditionBuilder
	 */
	protected $conditionBuilder;

	/**
	 * enables the automatic usage of the qualified shorthand
	 * @var    boolean
	 */
	public $useQualifiedShorthand = true;

	/**
	 * sql offset
	 * @var    integer
	 */
	public $sqlOffset = 0;

	/**
	 * sql limit
	 * @var    integer
	 */
	public $sqlLimit = 0;

	/**
	 * sql order by statement
	 * @var    string
	 */
	public $sqlOrderBy = '';

	/**
	 * sql select parameters
	 * @var    string
	 */
	public $sqlSelects = '';

	/**
	 * sql select joins
	 * @var    string
	 */
	public $sqlJoins = '';

	/**
	 * sql select joins which are necessary for where statements
	 * @var    string
	 */
	public $sqlConditionJoins = '';

	/**
	 * current iterator index
	 * @var    integer
	 */
	protected $index = 0;

	public function __construct() {
		// set class name
		if ( empty($this->className) ) {
			$className = get_called_class();

			if ( mb_substr($className, -4) == 'List' ) {
				$this->className = mb_substr($className, 0, -4);
			}
		}

		$this->conditionBuilder = new PreparedStatementConditionBuilder();
	}

	/**
	 * Counts the number of objects.
	 *
	 * @return    integer
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*)
			FROM	".$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias()."
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder();
		$statement = Core::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());

		return $statement->fetchSingleColumn();
	}

	/**
	 * Reads the object ids from database.
	 */
	public function readObjectIDs() {
		$this->objectIDs = [];
		$sql = "SELECT	".$this->getDatabaseTableAlias().".".$this->getDatabaseTableIndexName()." AS objectID
			FROM	".$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias()."
				".$this->sqlConditionJoins."
				".$this->getConditionBuilder()."
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = Core::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$this->objectIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
	}

	/**
	 * Reads the objects from database.
	 */
	public function readObjects() {
		if ( $this->objectIDs !== null ) {
			if ( empty($this->objectIDs) ) {
				return;
			}

			/** @noinspection DuplicatedCode */
			$sql = "SELECT	".(!empty($this->sqlSelects) ? $this->sqlSelects.($this->useQualifiedShorthand ? ',' : '') : '')."
					".($this->useQualifiedShorthand ? $this->getDatabaseTableAlias().'.*' : '')."
				FROM	".$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias()."
					".$this->sqlJoins."
				WHERE	".$this->getDatabaseTableAlias().".".$this->getDatabaseTableIndexName()." IN (?".str_repeat(',?', count($this->objectIDs) - 1).")
					".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$statement = Core::getDB()->prepareStatement($sql);
			$statement->execute($this->objectIDs);
			$this->objects = $statement->fetchObjects($this->className);
		}
		else {
			/** @noinspection DuplicatedCode */
			$sql = "SELECT	".(!empty($this->sqlSelects) ? $this->sqlSelects.($this->useQualifiedShorthand ? ',' : '') : '')."
					".($this->useQualifiedShorthand ? $this->getDatabaseTableAlias().'.*' : '')."
				FROM	".$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias()."
					".$this->sqlJoins."
					".$this->getConditionBuilder()."
					".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
			$statement = Core::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
			$statement->execute($this->getConditionBuilder()->getParameters());
			$this->objects = $statement->fetchObjects($this->className);
		}

		// decorate objects
		if ( !empty($this->decoratorClassName) ) {
			foreach ( $this->objects as &$object ) {
				$object = new $this->decoratorClassName($object);
			}
			unset($object);
		}

		// use table index as array index
		$objects = $this->indexToObject = [];
		foreach ( $this->objects as $object ) {
			$objectID = $object->getObjectID();
			$objects[$objectID] = $object;
			$this->indexToObject[] = $objectID;
		}
		$this->objectIDs = $this->indexToObject;
		$this->objects = $objects;
	}

	/**
	 * Returns the object ids of the list.
	 *
	 * @return integer[]
	 */
	public function getObjectIDs() {
		return $this->objectIDs;
	}

	/**
	 * Sets the object ids.
	 *
	 * @param integer[] $objectIDs
	 */
	public function setObjectIDs(array $objectIDs) {
		$this->objectIDs = array_merge($objectIDs);
	}

	/**
	 * Returns the objects of the list.
	 *
	 * @return DatabaseObject[]
	 */
	public function getObjects() {
		return $this->objects;
	}

	/**
	 * Returns the name of the database table.
	 *
	 * @return    string
	 */
	public function getDatabaseTableName() {
		return call_user_func([$this->className, 'getDatabaseTableName']);
	}

	/**
	 * Returns the name of the database table.
	 *
	 * @return    string
	 */
	public function getDatabaseTableIndexName() {
		return call_user_func([$this->className, 'getDatabaseTableIndexName']);
	}

	/**
	 * Returns the name of the database table alias.
	 *
	 * @return    string
	 */
	public function getDatabaseTableAlias() {
		return call_user_func([$this->className, 'getDatabaseTableAlias']);
	}

	/**
	 * Returns the condition builder object.
	 *
	 * @return    PreparedStatementConditionBuilder
	 */
	public function getConditionBuilder() {
		return $this->conditionBuilder;
	}

	/** @inheritDoc */
	public function count() {
		return count($this->objects);
	}

	/** @inheritDoc */
	public function current() {
		$objectID = $this->indexToObject[$this->index];

		return $this->objects[$objectID];
	}

	/** @inheritDoc */
	public function next() {
		++$this->index;
	}

	/** @inheritDoc */
	public function key() {
		return $this->indexToObject[$this->index];
	}

	/** @inheritDoc */
	public function valid() {
		return isset($this->indexToObject[$this->index]);
	}

	/** @inheritDoc */
	public function rewind() {
		$this->index = 0;
	}

	/** @inheritDoc */
	public function seek($index) {
		$this->index = $index;

		if ( !$this->valid() ) {
			throw new \OutOfBoundsException();
		}
	}
}