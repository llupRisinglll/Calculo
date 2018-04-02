<?php

/**
 * Written by: Luis Edward M. Miranda
 * Written for: MongoDB v3.4.6
 */
class MongoLeaf {
	private static $dbCon = NULL;

	/**
	 * Initialize and Get the Database Connection Manager
	 * @param bool $client
	 * @return \MongoDB\Driver\Manager|null
	 */
	private static function getCon(bool $client = false){
		$dbName = mongoDBName;
		$host = mongoHost;
		$port = mongoPort;

		if ($client){
			$user = mongoUserClient;
			$pass = mongoPassClient;
		}else{
			$user = mongoUser;
			$pass = mongoPass;
		}

		if (self::$dbCon === null) {
			try {
				$conString = "mongodb://$user:$pass@$host:$port/$dbName";
				$con = new MongoDB\Driver\Manager($conString);

			} catch (MongoDB\Driver\Exception\Exception $e) {
				$filename = basename(__FILE__);

				echo "The $filename script has experienced an error.\n";
				echo "It failed with the following exception:\n";

				echo "Exception:", $e->getMessage(), "\n";
				echo "In file:", $e->getFile(), "\n";
				echo "On line:", $e->getLine(), "\n";

				$con = NULL;
			}

			self::$dbCon = $con;
		}

		return self::$dbCon;
	}

	/**
	 * Query in a Database Collection
	 * @param string $collection
	 * @param array $filter
	 * @param array $option
	 * @param \MongoDB\Driver\ReadPreference|null $readPref
	 * @return array
	 */
	public static function query(string $collection, array $filter, array $option = array(), MongoDB\Driver\ReadPreference $readPref = null){
		# Create an empty array
		$arr = array();

		$manager = self::getCon();
		$query = new MongoDB\Driver\Query($filter, $option);
		$cursor = $manager->executeQuery(mongoDBName. "." .$collection, $query, $readPref);

		// This is to convert the result into a PHP array.
		foreach ($cursor as $item){
			if (is_object($item))
				$item = get_object_vars($item);

			# Add to the List of Results
			array_push($arr, $item);
		}

		return $arr;
	}

	/**
	 * Check whether there is an error while writing a document
	 * @param \MongoDB\Driver\WriteResult $cursor
	 * @return bool
	 */
	private static function getWriteError(MongoDB\Driver\WriteResult $cursor){
		// If a write could not happen at all
		return count($cursor->getWriteErrors()) > 0;
	}

	/**
	 * Check whether there is an error while fulfilling a write concern
	 * @param \MongoDB\Driver\WriteResult $cursor
	 * @return bool
	 */
	private static function getWriteConcernError(MongoDB\Driver\WriteResult $cursor){
		// If the Write Concern could not be fulfilled
		return $cursor->getWriteConcernError() != null;
	}

	/**
	 * Create a Bulk Action Container
	 * @return \MongoDB\Driver\BulkWrite
	 */
	public static function generateBulk(){
		return new MongoDB\Driver\BulkWrite();
	}

	public static function generateWriteConcern($writeOperation, $writeTimeout, $journal){

		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
	}

	/**
	 * Bulk Insert, Update, Delete Documents in a Database Collection
	 * @param string $collection
	 * @param \MongoDB\Driver\BulkWrite $bulk
	 * @param \MongoDB\Driver\WriteConcern|null $writeConcern
	 * @return \MongoDB\Driver\WriteResult|null
	 */
	public static function write(string $collection, MongoDB\Driver\BulkWrite $bulk, MongoDB\Driver\WriteConcern $writeConcern = null){
		$manager = self::getCon();
		$cursor = $manager->executeBulkWrite(mongoDBName. "." .$collection, $bulk, $writeConcern);

		return (self::getWriteConcernError($cursor) || self::getWriteError($cursor))? null : $cursor;
	}

	/**
	 * Delete One Document in a Database Collection
	 * @param string $collection
	 * @param array $filter
	 * @param array $option
	 * @param \MongoDB\Driver\WriteConcern|null $writeConcern
	 * @return \MongoDB\Driver\WriteResult|null
	 */
	public static function deleteOne(string $collection, array $filter, array $option = array(), MongoDB\Driver\WriteConcern $writeConcern = null){
		$manager = self::getCon();

		$bulk = new MongoDB\Driver\BulkWrite();
		$bulk->delete($filter, $option);

		$cursor = $manager->executeBulkWrite(mongoDBName. "." .$collection, $bulk, $writeConcern);

		return (self::getWriteConcernError($cursor) || self::getWriteError($cursor))? null : $cursor;
	}

	/**
	 * Insert One Document in a Database Collection
	 * @param string $collection
	 * @param array $document
	 * @param \MongoDB\Driver\WriteConcern|null $writeConcern
	 * @return \MongoDB\Driver\WriteResult|null
	 */
	public static function insertOne(string $collection, array $document, MongoDB\Driver\WriteConcern $writeConcern = null){
		$manager = self::getCon();

		$bulk = new MongoDB\Driver\BulkWrite();
		$bulk->insert($document);

		$cursor = $manager->executeBulkWrite(mongoDBName. "." .$collection, $bulk, $writeConcern);

		return (self::getWriteConcernError($cursor) || self::getWriteError($cursor))? null : $cursor;
	}

	/**
	 * Update One Document in a Database Collection
	 * @param string $collection
	 * @param array $filter
	 * @param array $newObj
	 * @param array $option
	 * @param \MongoDB\Driver\WriteConcern|null $writeConcern
	 * @return \MongoDB\Driver\WriteResult|null
	 */
	public static function updateOne(string $collection, array $filter, array $newObj, array $option = array(), MongoDB\Driver\WriteConcern $writeConcern = null){
		$manager = self::getCon();

		$bulk = new MongoDB\Driver\BulkWrite();
		$bulk->update($filter, $newObj, $option);

		$cursor = $manager->executeBulkWrite(mongoDBName. "." .$collection, $bulk, $writeConcern);

		return (self::getWriteConcernError($cursor) || self::getWriteError($cursor))? null : $cursor;
	}

}