<?php

class Session {
	protected static $flashMessage;

	public static function setFlash(string $message){
		self::$flashMessage = $message;
	}

	public static function hasFlash(): bool {
		return !is_null(self::$flashMessage);
	}

	public static function flash(){
		echo self::$flashMessage;
		self::$flashMessage = null;
	}

	public static function set(){

	}

	public static function get(){

	}

	public static function destroy(){

	}
}

