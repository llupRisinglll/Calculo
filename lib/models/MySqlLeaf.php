<?php

/**
 * Written by: Luis Edward M. Miranda
 * Written for: MongoDB v3.4.6
 */
class MySqlLeaf {
	private static $dbCon = NULL;

	public static function getCon(){
		if (self::$dbCon === NULL){

			try {
				$db = mysqli_connect(
					MySQL_host,
					MySQL_user,
					MySQL_pass,
					MySQL_dbn
				);
			} catch(Exception $e) {
				echo $e->getMessage();
				$db = NULL;
			}

			self::$dbCon = $db;
		}

		return self::$dbCon;
	}

}