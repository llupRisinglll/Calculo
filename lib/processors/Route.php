<?php

class Route {
	protected static $path;
	protected static $uri;
	protected static $pathParts;
	public static $pathExist = false;
	public static $requestMethod = "GET";
	public static $domainName = "";

	/**
	 * @return string
	 */
	public static function getDomainName(): string {
		return self::$domainName;
	}

	/**
	 * @return string
	 */
	public static function getRequestMethod(): string {
		return self::$requestMethod;
	}

	/**
	 * @param string $requestMethod
	 */
	public static function setRequestMethod(string $requestMethod) {
		self::$requestMethod = $requestMethod;
	}

	/**
	 * @param bool $pathExist
	 */
	public static function setPathExist(bool $pathExist) {
		self::$pathExist = $pathExist;
	}

	/**
	 * @return mixed
	 */
	public static function getPathExist(): bool {
		return self::$pathExist;
	}


	public function __construct($requestDetails) {
		// Get the HTTP REQUEST METHOD
		self::$requestMethod = $requestDetails["REQUEST_METHOD"];

		// domain name of the server.
		self::$domainName = $requestDetails["SERVER_NAME"];

		// /path1/path2/path3
		self::$uri = $requestDetails["REQUEST_URI"];

		// path1/path2/pathN
		$uriTrimmed = urldecode(trim($requestDetails["REQUEST_URI"], '/'));

		/**
		 * Convert a string into array
		 * Array (
		 *      [0] => Param1
		 *      [1] => Param2
		 * )
		 */
		self::$pathParts = explode('/', $uriTrimmed);

	}

	public static function method(string $method, $callback) {
		// e.g. POST, GET, PUT, DELETE
		if (self::$requestMethod == strtoupper($method)) {
			$callback();
		}
	}


	public static function action($url, $callback){
		if (self::$uri == $url) {
			$callback($_POST);
			self::setPathExist(true);
		}
	}

	public static function path($url, $addParams, $callback){
		if (!$addParams){
			// Check if it is super matched :))
			if (self::$uri == $url){
				$callback();
				self::setPathExist(true);
			}
		}else{
			// path1/path2/pathN
			$urlTrimmed = urldecode(trim($url, '/'));
			$urlParts = explode('/', $urlTrimmed);

			// Check if the parts is possible match or has an additional parameter
			if (count(self::$pathParts) > count($urlParts)){
				/**
				 * Get the array of the possible matched array
				 * Output should be...
				 * array (
				 *    0 => "path1",
				 *    1 => "path2"
				 * )
				 */
				$slicedURI = array_slice(self::$pathParts, 0, count($urlParts));

				// Check if the two arrays matched
				if ($slicedURI === $urlParts){
					/**
					 * Create a new array that doesn't include the matched value
					 * Output should be...
					 * array (
					 *    0 => "par1",
					 *    1 => "par2"
					 * )
					 */
					$usableParams = array_slice(self::$pathParts, count($urlParts));
					$callback($usableParams);
					self::setPathExist(true);
				}
			}
		}
	}

	public static function view($file, array $arrVariables = array()){
		// Load the Vendor Files Automatically when called
		require_once VENDORS.DS.'autoload.php';

		$loader = new Twig_Loader_Filesystem(VIEW_PATH);

		$twig = new Twig_Environment($loader, array(
			// "cache" => CACHE_PATH
		));

		// Put the Config Variables in the Twig Template
		$twig->addGlobal('config', Config::array());

		echo $twig->render($file, $arrVariables);
	}

	public static function redirect($uri){
		header("location: ". $uri);
		exit;
	}
}