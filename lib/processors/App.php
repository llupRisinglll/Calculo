<?php

class App {
	private static $router;

	public static function run($requestDetails){
		self::$router = new Route($requestDetails);
	}
}