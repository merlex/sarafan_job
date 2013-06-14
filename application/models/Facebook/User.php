<?php
include_once 'Op/Model.php';
/**
 * Facebook пользователь:
 * @author atukmanov
 *
 */
class Application_Model_Facebook_User extends Op_Model{
	/**
	 * Инициировать пользователя из подключения:
	 * @param Facebook_Connect $connect
	 * @return Application_Model_Facebook_User
	 */
	static function fromConnect($connect){
		
		if (!$connect->user){
			//Нет подключения:
			return null;
		}
		//Проверяем есть ли уже пользователь:
		$mapper= new Application_Model_Facebook_UserMapper();		
		if ($user=$mapper->loadUserByForeignID($connect->user)){			
			if ($connect->getAccessToken()){
				$user->setValue('access_token',$connect->getAccessToken());
				$user->setValue('is_post',$connect->isPost());				
				$mapper->save($user);
			}
			return $user;
		}
		
		if (!$connect->getAccessToken()) return;
		//Создаем нового пользователя:
		$user= new Application_Model_Facebook_User();
		$user->setValue('foreign_id',$connect->user);
		if ($connect->getAccessToken()){			
			//Выставляем данные доступа и профиля:
			$user->setValue('access_token',$connect->getAccessToken());						
			$user->setValue('name',$connect->userInfo->name);									
		}
		$user->setValue('ts',time());			
		$user->setID($mapper->save($user));		
		return $user;
		
	}
	/**
	 * URL:
	 * @return string
	 */
	function getURL(){
		return "http://www.facebook.com/profile.php?id={$this->foreign_id}";
	}
	/**
	 * Данные о канале для формы
	 * @return array
	 */
	function getChannelData(){
		$ret=array();
		$ret['message']=$this->message;
		$ret['cat']=$this->getCategoriesID();
		return $ret;
	}
	/**
	 * Сохранить данные из формы:
	 * @param $data
	 * @return void
	 */
	function setChannelData($data){
		
		if ($this->setCategories($data['cat'])){
			//Время автопубликации 7 дней:			
			$this->setValue('post_period',60*60*24*7);
		}
		else {
			//Выключаем автопубликацию:
			$this->setValue('post_period',0);
		}
		$this->setValue('message',$data['message']);
		$this->getMapper()->save($this);
	}
	/**
	 * Получить каналы
	 * @return array
	 */
	function getChannels(){
		$channelMapper= new Application_Model_Facebook_ChannelMapper();
		return $channelMapper->getUserChannels($this->getID());		
	}
	/**
	 * Список id транслируемых категорий:
	 * @return boolean
	 */
	function getCategoriesID(){
		$ret=array();
		if ($channels=$this->getChannels()){
			foreach ($channels as $channel){
				$ret[]=$channel->category_id;
			}
		}
		return $ret;
	}
	/**
	 * Получить категории:
	 * @param $catID
	 * @return boolean
	 */
	function setCategories($catIDs){
		$oldChannels=Op_Util::ArrayReindex($this->getChannels(),'category_id','id');
		$channelMapper= new Application_Model_Facebook_ChannelMapper();
		//Добавляем новые:
		foreach ($catIDs as $id){
			if (!isset($oldChannels[$id])){
				//Добавляем:
				$channel= new Application_Model_Facebook_Channel();
				$channel->setInfo(array(
					'facebook_user_id'=>$this->getID(),
					'category_id'=>$id,
				));
				$channelMapper->save($channel);
			}
		}
		//Удаляем старые:
		foreach ($oldChannels as $catID=>$channelID){
			if (!in_array($catID, $catIDs)){
				$channelMapper->delete($channelID);
			}
		}
		return count($catID);
	}
	/**
	 * Получить агента записи
	 * @return Facebook_Connect
	 */
	function getConnect(){
		$cfg=Zend_Registry::get('config');		
		$facebook_connect= new Facebook_Connect($cfg->production->facebook->appID, $cfg->production->facebook->appSecret);
		$facebook_connect->setAccessToken($this->access_token);
		return $facebook_connect;
	}
	/*********************** POSTING ***********************
	 * 
	 */
	protected $_channelCategories=array();
	/**
	 * Добавить канал:
	 * @param $channel
	 * @return void
	 */
	function addChannel($channel){
		$this->_channelCategories[]=$channel->category_id;		
	}
	
	protected $_ad=array();
	/**
	 * Добавить объявление:
	 * @param $ad
	 * @return boolean
	 */
	function addAd($ad){
		if (in_array($ad->category_id, $this->_channelCategories)){
			//Поддерживается категория
			if (!Application_Model_Post::loadUserAd($this, $ad)){
				//Не было добавлено:
				$this->_ad[]=$ad->id;
				return 1;
			}
		}
		return 0;
	}
}
?>