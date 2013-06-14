<?php
/**
 *	Работа с facebook
 * 
 * @author atukmanov
 *
 */
class Facebook_Connect extends Op_Model {

	protected $appID=null;
	protected $appSecret=null;
	
	const writeScope='offline_access,publish_stream';
	
	function __construct($appID, $appSecret){
		$this->appID=$appID;
		$this->appSecret=$appSecret;
	}
	/**
	 * URL для аутентификации:
	 * @param $callbackURL
	 * @param $scope
	 * @return string
	 */
	function buildAuthURL($callbackURL, $scope){
		$ret='https://www.facebook.com/dialog/oauth?client_id='.$this->appID.'&redirect_uri='.urldecode($callbackURL);
		//Дополнительные права:
		if ($scope) $ret.='&scope='.$scope;
		return $ret;
	}
	/**
	 * Подписанный запрос:
	 * @var Facebook_Sig
	 */
	var $sig;
	/**
	 * 
	 * @param Zend_Request_Http $request
	 * @return boolean
	 */
	function parseAuthRequest($request, $callbackURL){
		//Проверяем подписанный запрос, передаваемый в iframe:
	//Если передан код oAuth получаем токен:
		if ($code=$request->getParam('code')){
			if ($this->fetchAccessToken($code, $callbackURL)){
				$this->userInfo = $this->load('me/');
				$this->user = $this->userInfo->id;
				return true;
			}
			else
			{
				//Ошибка токена:
				return false;
			}
		}
		$sig= new Facebook_Sig($this->appID, $this->appSecret);
		if (!$sig->readRequest($request)){
			//Запрос не передан:

			return false;
		}
		
		//Копируем данные:
		$this->_info=$sig->getInfo();
		return true;
	}
	
	protected $accessTokenURL='https://graph.facebook.com/oauth/access_token';
	
	protected $accessToken;
	
	function getAccessToken(){
		return $this->accessToken;
	}
	
	function setAccessToken($token){
		$this->accessToken=$token;
	}
	/**
	 * Получить access token
	 * @param $accessToken
	 * @return string
	 */
	function fetchAccessToken($code, $callbackURL){
		$args=array(
			'client_id'=>$this->appID,
			'client_secret'=>$this->appSecret,
			'code'=>$code,
			'redirect_uri'=>$callbackURL
		);
		$url=$this->accessTokenURL.'?'.http_build_query($args);
		
		$client= new Zend_Http_Client($this->accessTokenURL);
		$client->setParameterPost($args);
		$client->setMethod(Zend_Http_Client::POST);
		$resp=$client->request();
		parse_str($resp->getBody(), $info);
		if (!isset($info['access_token'])){						
			return null;				
		}
		$this->accessToken=$info['access_token'];		
		return true;
	}
	/**
	 * Наличие доступа на запись:
	 * @return boolean
	 */
	function isPost(){
		$p = $this->load('me/permissions');
		if (isset($p->data[0]->status_update))
		{
			return true;
		}
		else {
			return false;
		}
		return (false!==strpos($this->ext_perms,'status_update'))?true:false;
	}
	
	protected $graphURL='https://graph.facebook.com/';
	/**
	 * Загрузить:
	 * @param $what
	 * @param $post
	 * @param $id
	 * @return object
	 */
	function load($url, $post=null){
		$client= new Zend_Http_Client($this->graphURL.$url);
		if ($post){			
			$client->setParameterPost('access_token',$this->accessToken);
			foreach ($post as $k=>$v){
				$client->setParameterPost($k,$v);
			}						
			$client->setMethod(Zend_Http_Client::POST);
		}
		else {
			
			$client->setParameterGet('access_token',$this->accessToken);
			$client->setMethod(Zend_Http_Client::GET);
		}
		
		$resp=$client->request();		
		return json_decode($resp->getBody());
	}
	/**
	 * Добавить пост:
	 * @param $title
	 * @param $post
	 * @param $link
	 * @return object
	 */
	function post($title, $post, $link){
		return $this->load('feed',array(
			'app_id'=>$this->appID,
			'message'=>$post,
			'link'=>$link,
			'caption'=>$title,
		));
	}
	
	
	
	function loadUserInfo(){
		return $this->load('me');
	}
} 
?>