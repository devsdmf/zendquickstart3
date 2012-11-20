<?php

/* Loading scripts and files */
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Controller/Action/Helper/FlashMessenger.php';

/**
 * The Abstract Class to use in controllers of default module for set all common params and configurations of controllers
 * 
 * @category App
 * @package App/Modules
 * @subpackage Default
 * @author Lucas Mendes de Freitas (devsdmf)
 * @since 2012
 * @license http://www.devsdmf.net/license/general.txt
 * @copyright devSDMF Software Development (c)
 *
 */

abstract class App_Modules_Default_Abstract extends Zend_Controller_Action
{
	/**
	 * The instance of FlashMessenger Action Helper 
	 * 
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	protected $_flashMessenger = null;
	/**
	 * The abstractInit is a method to set common params in view 
	 */
	function abstractInit()
	{
		/* Setting base url in view */
		$this->view->baseUrl = $this->_request->getBaseUrl();
		
		/* Setting the name of current module in view */
		$this->view->currentModule = $this->_request->getModuleName();
		
		/* Setting the name of current controller in view */
		$this->view->currentController = $this->_request->getControllerName();
		
		/* Setting the name of current action in view */
		$this->view->currentAction = $this->_request->getActionName();
		
		/* Setting the base url of assets in view */
		$this->view->assetUrl = $this->_request->getBaseUrl() . '/assets/';
	}
	/**
	 * The flashMessengerInit is a method to initialize the FlashMessenger Action Helper and getting stored messages in this
	 */
	function flashMessengerInit()
	{
		/* Getting instance of FlashMessenger Action Helper */
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		/* Getting stored messages on FlashMessenger and setting in view */
		$this->view->flashMessengerAlerts = $this->_flashMessenger->getMessages();
	}
	/**
	 * This is a method to verify if request method is post 
	 * 
	 * @return boolean
	 */
	function isPost()
	{
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post")
			return true;
		else
			return false;
	}
}