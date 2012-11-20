<?php
/**
 * Loading required scripts and files 
 */
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Router/Route.php';
require_once 'Zend/Db.php';
require_once 'Zend/Db/Table.php';
require_once 'Zend/Registry.php';

/**
 * The Plugin for set configurations and params for the correctly initialization of application
 * 
 * @category Plugins
 * @package App
 * @subpackage Plugins
 * @author Lucas Mendes de Freitas (devsdmf)
 * @since 2012
 * @license http://www.devsdmf.net/license/general.txt
 * @copyright devSDMF Software Development (c)
 *
 */

class Initializer extends Zend_Controller_Plugin_Abstract
{
	/**
	 * The path to application config file
	 * 
	 * @var string
	 */
	protected $_configFile = null;
	/**
	 * The application configurations
	 * 
	 * @var stdClass
	 */
	protected $_configs = null;
	/**
	 * The instance of Zend_Controller_Front
	 * 
	 * @var Zend_Controller_Front
	 */
	protected $_front = null;
	/**
	 * Constructor
	 * 
	 * @param string $configFile
	 */
	function __construct( $configFile )
	{
		/* Setting config file on object */
		$this->_configFile = $configFile;
		
		/* Getting Controller Front */
		$this->_front = Zend_Controller_Front::getInstance();
		
		/* Loading configs */
		$this->loadConfigs();
		
		/* Setting system configs */
		$this->appendSystemConfigs();
		
		/* Setting enviroment */
		$this->appendEnviromentConfigs();
		
		/* Setting database configs */
		$this->appendDatabaseConfigs();
		
		/* Setting routes on Application Router */
		$this->setRoutes();
		
		/* Register configs */
		$this->registerConfigs();
	}
	/**
	 * Method to load all configs of config file
	 * 
	 * @return void
	 */
	function loadConfigs()
	{
		/* Initializing stdClass on configs var */
		$this->_configs = new stdClass();
		
		/* Getting configs */
		$configs = new Zend_Config_Ini($this->_configFile);
		
		/* Looping for set configs in object */
		foreach($configs as $config => $params)
		{
			$this->_configs->$config = $params->$config;
		}
	}
	/**
	 * Method to append system configuartions
	 * 
	 * @return void
	 */
	function appendSystemConfigs()
	{
		/* Verifying if configs was loaded */
		if($this->_configs == null)
		{
			/* Loading configs */
			$this->loadConfigs();
		}
	
		/* Getting enviroment */
		$configs = $this->_configs->system;
	
		/* Set timezone */
		date_default_timezone_set($configs->timezone);
		
		/* Verifying if allow custom helpers */
		if($configs->allowcustomhelpers)
		{
			/* Setting the default path to custom helpers */
			Zend_Controller_Action_HelperBroker::addPath(PLUGIN_DIR . 'Helpers/','Helper');
		}
	}
	/**
	 * Method to append enviroment configurations
	 * 
	 * @return void
	 */
	function appendEnviromentConfigs()
	{
		/* Verifying if configs was loaded */
		if($this->_configs == null)
		{
			/* Loading configs */
			$this->loadConfigs();
		}
		
		/* Getting enviroment */
		$env = $this->_configs->system->enviroment;
		
		/* Setting enviroment configs */
		$configs = $this->_configs->enviroment->$env;
		
		/* Setting params of enviroment */
		error_reporting($configs->errorreporting);
		ini_set('display_startup_errors', $configs->displaystartuperrors);
		ini_set('display_errors', $configs->displayerrors);
		$this->_front->throwExceptions($configs->throwexceptions);
	}
	/**
	 * Method to append and generate the adapter of Zend_Db_Table
	 * 
	 * @return void
	 */
	function appendDatabaseConfigs()
	{
		/* Verifying if configs was loaded */
		if($this->_configs == null)
		{
			/* Loading configs */
			$this->loadConfigs();
		}
		
		/* Getting configs */
		$configs = $this->_configs->database;
		
		/* Manufacturing the database adapter */
		$adapter = Zend_Db::factory($configs->adapter,$configs->config->toArray());
		
		/* Setting adapter on Zend_Db_Table */
		Zend_Db_Table::setDefaultAdapter($adapter);
	}
	/**
	 * Method to set routes of system
	 * 
	 * @return void
	 */
	function setRoutes()
	{
		/* Verifying if configs was loaded */
		if($this->_configs == null)
		{
			/* Loading configs */
			$this->loadConfigs();
		}
		
		/* Getting router */
		$router = $this->_front->getRouter();
		
		/* Getting configs */
		$routes = new Zend_Config_Ini(CONFIG_DIR . $this->_configs->system->routefile);
		
		/* Looping for init routes */
		foreach($routes as $route => $params)
		{
			$router->addRoute($route, 
					new Zend_Controller_Router_Route($params->route,array('module'=>$params->options->module,'controller'=>$params->options->controller,'action'=>$params->options->action)));
		}
	}
	/**
	 * Method to registry all configs in Zend_Registry
	 * 
	 * @return void
	 */
	function registerConfigs()
	{
		/* Verifying if configs was loaded */
		if($this->_configs == null)
		{
			/* Loading configs */
			$this->loadConfigs();
		}
		
		/* Registering application configurations */
		Zend_Registry::set('configuration', $this->_configs);
	}
	/**
	 * This is a method to initialize the routes of system on Front Controller
	 * @see Zend_Controller_Plugin_Abstract::routeStartup()
	 */
	function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		/* Initializing controllers */
		$this->initControllers();
	}
	/**
	 * Method to set the controllers and modules dir
	 */
	function initControllers()
	{
		/* Setting module directory */
		$this->_front->addModuleDirectory(MODULES_DIR);
	}
}