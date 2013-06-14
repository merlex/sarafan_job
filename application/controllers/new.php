<?php
include_once 'Op/Util.php';
include_once 'Facebook/Sig.php';
include_once 'Facebook/Connect.php';
/**
 * Класс для работы с facebook:
 * @author atukmanov
 *
 */
class FacebookController extends Zend_Controller_Action{
	
	function indexAction(){
		
	}
	/***************************************************************
	 * Аутентификация:
	 */
		
	function initAuth(){
		$session= new Zend_Session_Namespace('facebook');
		$request= $this->getRequest();
		if ($request->getParam('code')){
			
		}
	}
	/**
	 * 
	 * @var Facebook_Auth_User
	 */
	protected $user=null;
	/***************************************************************
	 * Конфигурационные параметры:
	 * 	 
	 */
	protected $appID=null;
	protected $apiKey=null;
	protected $appSecret=null;
	protected $canvasURL=null;
	/**
	 * Применить:
	 * @var Facebook_Sign
	 */
	protected $fbSignedRequest=null;
	/**
	 * Инициация конфиг
	 * @return unknown_type
	 */
	protected function initAppConfig(){
		$cfg=Zend_Registry::get('config');
	
		$this->appID=$cfg->production->facebook->appID;
		$this->apiKey=$cfg->production->facebook->apiKey;
		$this->appSecret=$cfg->production->facebook->appSecret;
		$this->canvasURL=$cfg->production->facebook->canvasURL;
	}
}
?>