<?php
include_once 'Facebook/Sig.php';
include_once 'Facebook/Connect.php';
#include_once 'Op/Controller.php';
#include_once 'Op/Form.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	
	/**
	 * 
	 * @return unknown_type
	 */
	public function run(){
		//read config and put it into registry:		
		date_default_timezone_set('Europe/Moscow');
		$cfg= new Zend_Config_Ini(dirname(__FILE__).'/configs/application.ini');
		Zend_Registry::set('config',$cfg);
		
		$loader=Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Op_');
		$loaderResource= new Zend_Loader_Autoloader_Resource(array(
		    'namespace'=>'Op_',
			'basePath'=>'/var/www/cloud/data/zend/job/library/'
			));
			
		parent::run();	
	}
	
	protected function _initDoctype(){
		
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
	} 

}