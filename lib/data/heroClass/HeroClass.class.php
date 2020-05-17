<?php

namespace data\heroClass;

use data\DatabaseObject;

/**
 * Class HeroClass
 * @package data\heroClass
 *
 * @property-read int    $id
 * @property-read string $name
 * @property-read int    $isDisabled
 * @property-read int    $isHero
 */
class HeroClass extends DatabaseObject {
	protected static $databaseTableName = 'classes';

	protected static $databaseTableIndexName = 'id';
}