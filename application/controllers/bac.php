<?php
include_once 'Op/Util.php';
include_once 'Facebook/Sig.php';
include_once 'Facebook/Connect.php';

class FacebookController extends Zend_Controller_Action{
		
	protected $appID=null;
	protected $apiKey=null;
	protected $appSecret=null;
	protected $canvasURL=null;
	/**
	 * Применить:
	 * @var Facebook_Sign
	 */
	protected $fbSignedRequest=null;
	
	protected function initAppConfig(){
		$cfg=Zend_Registry::get('config');
	
		$this->appID=$cfg->production->facebook->appID;
		$this->apiKey=$cfg->production->facebook->apiKey;
		$this->appSecret=$cfg->production->facebook->appSecret;
		$this->canvasURL=$cfg->production->facebook->canvasURL;
	}
	/**
	 * 
	 * @return unknown_type
	 */
	protected function initLayout(){
		$this->_helper->layout->setLayout('facebook');
	}
	
	protected $fbSignedMethod=null;
	/**
	 * Инициация пользователя:
	 * @return boolean
	 */
	protected function initFacebookUser(){
		$session =new Zend_Session_Namespace('facebook');
		
		$this->fbSignedRequest=new Facebook_Sig($this->appID, $this->appSecret);
		if ($this->fbSignedRequest->readRequest($this->getRequest())){			
			$this->fbSignedMethod='facebook';
			$session->user= seri
			return true;			
		}
		elseif ($session->user){					
			$this->fbSignedRequest= unserialize($session->signed);
			$this->fbSignedMethod='session';
			return true;
		}
		else {
			return false;
		}
	}
	/**
	 * 
	 * @return unknown_type
	 */
	protected function setUser(){
		
	}
	
	function init(){
		$this->initAppConfig();
		$this->initLayout();
		if (!$this->initFacebookUser()){
			return $this->render('noFacebook');
		}					
	}
	/**
	 * 
	 * @return 
	 */
	function indexAction(){
		$adMapper= new Application_Model_AdMapper();		
		$this->view->ad=$adMapper->selectPastActive(3);
		$this->view->categories=$adMapper->selectCategories();
		$this->view->authURL=$this->getAuthURL();
	}

	function buildFaceboolURL($action, $params){
		$ret=$this->canvasURL.$action;
		$q=array();
		foreach ($params as $k=>$v){
			if (preg_match('@^[a-z]+$@',$k) && preg_match('@^[a-z0-9]+$@',$v)){
				$ret.='/'.$k.'/'.$v;
			}
			else {
				$q[$k]=$v;
			}		
		}
		if (count($q)) $ret.='?'.http_build_query($q);
		return $ret;
	}
	
	function getAuthURL(){
		return 'https://www.facebook.com/dialog/oauth?client_id='.$this->appID.'&redirect_uri='.urldecode($this->buildFaceboolURL('manage')).'&scope=offline_access,publish_stream'; 
	}
	/**
	 * Управление трансляциями:
	 * @return bool
	 */
	function manageAction(){
		//Авторизация:
		$this->view->authURL=$this->getAuthURL();
		//Проверяем код:
		if (!$code=$this->getRequest()->getParam('code')){			
			return $this->render('oauth_fault');
		}
		//Проверяем коннект:
		$connect= new Facebook_Connect($this->appID, $this->appSecret);
		if (!$connect->fetchAccessToken($code, 'http://apps.facebook.com/sarafan_job/callback/')){
			return $this->render('oauth_fault');
		}
		//Получаем пользователя:
		$fbUser=Application_Model_Facebook_User::fromConnect($connect);
		//Сохраняем в сессии:		
	}
	/**
	 * 
	 * @param $connect
	 * @return unknown_type
	 */
	function channelForm($connect){
		$this->render('channelForm');
	}
	/**
	 * Обработка iframe:
	 * @return boolean
	 */
	function iframeAction(){
		
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function adAction(){
		Op_Util::p($this->fbSignedMethod);
		$mapper= new Application_Model_AdMapper();
		if (!$obj=$mapper->find($this->getRequest()->getParam('id'))){
			//404:
			$this->prepareSeeAlso(0);
			return $this->render('ad404');
		}
		elseif (!$obj->is_active){
			$this->prepareSeeAlso($obj->category_id);
			return $this->render('finished');
		}
		$this->view->obj=$obj;
	}
	/**
	 * Получить см. также
	 * @param int $categoryID id категории
	 * @param int $count кол-во
	 * @return unknown_type
	 */
	protected function prepareSeeAlso($categoryID, $count=5){
		$mapper=new Application_Model_AdMapper();
		$sel=$mapper->getActiveSelect();
		if ($categoryID) $sel->where('category_id=?',$categoryID);
		$sel->limit($count);
		$this->view->seeAlso=$mapper->fetchList($sel->query());
			
	}

}
?>