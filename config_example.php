<?php
CONST LIVE = false;
CONST DBSTRINGLIVE = 'mysql:host=localhost;dbname=DBNAME';
CONST DBUSERLIVE = '';
CONST DBPASSWORDLIVE = '';
CONST DBSTRINGLOCAL = 'mysql:host=localhost;dbname=DBNAME';
CONST DBUSERLOCAL = '';
CONST DBPASSWORDLOCAL = '';
CONST STEAMAPIKEY = '';

CONST HIDETHIS = true;


define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);

spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.php';
});