<?php

/**
 * A function to easy variable debug using the var_dump() and exit() to break the script execution
 * 
 * @param multitype $var
 */
function vd( $var )
{
	var_dump($var);
	exit(0);
}

/* Initializing constants */
define("ROOT_PATH", dirname(__FILE__) . '/../');
define("APPLICATION_PATH", dirname(__FILE__) . '/');
define("CONFIG_DIR", APPLICATION_PATH . 'configuration/');
define("MODULES_DIR", APPLICATION_PATH . 'modules/');
define("LIB_DIR", APPLICATION_PATH . 'lib/');
define("PLUGIN_DIR", APPLICATION_PATH . 'plugins/');
define("PS", PATH_SEPARATOR);
define("DS", DIRECTORY_SEPARATOR);
define("APPLICATION_CONFIG_FILE", CONFIG_DIR . 'application.ini');
define("PUBLIC_PATH", ROOT_PATH . 'public_html/');

/* Mounting include path string with modules dir */
$res = opendir(MODULES_DIR);
$modulesIncPath = null;
while($dir = readdir($res))
{
	if($dir != '.' && $dir != '..')
		$modulesIncPath .= MODULES_DIR . $dir . '/' . PS . MODULES_DIR . $dir . '/models/' . PS;
}

/* Setting include paths */
$PATHS = '.' . PS . 
		 ROOT_PATH . PS . 
		 APPLICATION_PATH . PS .
		 MODULES_DIR . PS . 
		 $modulesIncPath	.
		 LIB_DIR . PS . 
		 PLUGIN_DIR . PS .
		 get_include_path();
set_include_path($PATHS);

/* Loading librarys */
require_once 'Initializer.php';
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/AutoLoader.php';
require_once 'Zend/Registry.php';

/* Setting params and configs on autoloader */
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

/* Setup the controller front */
$frontController = Zend_Controller_Front::getInstance();
$frontController->registerPlugin( new Initializer(APPLICATION_CONFIG_FILE) );

/* Trying to dispatch the front controller, in case of failure show Exception message */
try
{
	$frontController->dispatch();
} catch(Exception $e)
{
	$contentType = "text/html";
	header("Content-type: $contentType; charset=iso-8859-1");
	echo "an unexpected error occurred";
	echo "<h2>Unexpected Exception: " . $e->getMessage() . "</h2><br/><pre>";
	echo $e->getTraceAsString();
}