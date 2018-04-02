<?php

/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 03/08/2017
 * Time: 4:02 PM
 */
class PDODatabase {
	public function __construct(string $dbName) {
		$dbName = strtolower($dbName);
		return new PDO($dbName.":host=localhost;dbname:database", Config::get($dbName.'.user'), Config::get($dbName.'.pass'));
	}
}