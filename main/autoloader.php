<?php
// an AutoLoad Function
function __autoload($className){

	// Initiate all of possible paths in a variable
	$handlersPath = ROOT.DS.'lib/handlers'.DS.ucfirst($className).'.php';
	$processorsPath = ROOT.DS.'lib/processors'.DS.ucfirst($className).'.php';
	$modelsPath = ROOT.DS.'lib/models'.DS.ucfirst($className).'.php';

	/**
	 * Dynamically Load a File
	 * Check which type of class it is.
	 */

	if (file_exists($handlersPath)){
		// Use the Handler Class
		require_once $handlersPath;
	} elseif (file_exists($processorsPath)){
		// Use the Processors Class
		require_once $processorsPath;
	} elseif (file_exists($modelsPath)){
		// Use the Models Class
		require_once $modelsPath;
	} else{

		// TODO: Show a 500 ERROR not found instead...
		throw new Exception('Failed to include ClassName:'. $className);
	}
}