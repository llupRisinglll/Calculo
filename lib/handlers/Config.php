<?php

class Config {
	protected static $settings = array();

	// Return the value on setting array when it is true, else return null
	public static function get($key){
		return isset(self::$settings[$key]) ? self::$settings[$key] : null;
	}

	// Create a dictionary in the setting array
	public static function set($key, $value){
		self::$settings[$key] = $value;
	}

	// Export the Array
	public static function array(){
		return self::$settings;
	}
}

