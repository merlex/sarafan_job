<?php
/**
 * Facebook пост:
 * @author 1
 *
 */
class Application_Model_Post extends Op_Model {
	
	protected $_ad=null;
	/**
	 * Получить рекламу:
	 * @return Application_Model_Ad
	 */
	function loadAd(){			
		$mapper= new Application_Model_AdMapper();
		return $mapper->find($this->ad_id);		
	}	
	/**
	 * Получить пользователя:
	 * @return Application_Model_User
	 */
	function loadUser(){
		$mapper= new Application_Model_Facebook_UserMapper();
		return $mapper->find($this->user_id);
	}
	
	function loadVisitors($limit=100){
		$mapper= new Application_Model_Post_VisitMapper();			
		return $mapper->getVisitors($this);
	}
	/**
	 * Получить пост пользователя для объявления
	 * @return Application_Model_Post
	 */
	static function loadUserAd($user, $ad){
		$mapper= new Application_Model_PostMapper();		
		return $mapper->loadUserAd($user, $ad);
	}
	/**
	 * Добавить запись:
	 * @param Application_Model_Ad $ad
	 * @param Application_Model_Facebook_User $user
	 * @param string $url
	 * @param string $customText
	 * @return Application_Model_Post
	 */
	static function create($ad, $user, $url){
		//1. Создать:		
		$ret= new Application_Model_Post();
		$ret->setValue('user_id',$user->getID());
		$ret->setValue('ad_id',$ad->getID());
		$ret->setValue('ts',time());
		$ret->save();
		//2. Запостить:
		//Формируем URL
		$url=str_replace('{id}',$ret->getID(),$url);
		//Получаем коннект пользователя:
		$connect= $user->getConnect();
		$post=$ad->announce;
		//Дописываем сообщение:
		
		if ($user->message){
			$post.=PHP_EOL.$user->message;
		}
		if ($user->foreign_id){
			$fb_post=$connect->post($ad->title, $post, $url);
		}	
		else {
			//debug:
			$fb_post->id=1;
		}	
		if ($fb_post->id){
			//posted:
			$ret->setValue('foreign_id',$fb_post->id);		
			$ret->save();
			//инкрементируем кол-во постов:
			$ad->setValue('posts',(int)$ad->getValue('posts')+1);
			
			$ad->save();
			//записваем время след. поста:
			if ($user->post_period){
				$user->setValue('post_ts',time()+$user->post_period);
				$user->save();	
			}
			return $ret;
		}
		else {
			//error:					
			$ret->getMapper()->delete($ret);
			return null;	
		}
	}
	/**
	 * URL поста:
	 * @return string
	 */
	function getURL(){
		list($id, $story_fbid)=explode('_',$this->foreign_id);
		return "http://www.facebook.com/permalink.php?story_fbid={$story_fbid}&id={$id}";
	}
	/**
	 * Количество визитов:
	 * @return int
	 */	
	function loadVisitsCount(){		
		$mapper= new Application_Model_Post_VisitMapper();
		return $mapper->getVisitsCount($this);
	}
	/**
	 * Количество визитов:
	 * @return int
	 */	
	function loadUniqueCount(){		
		$mapper= new Application_Model_Post_VisitMapper();
		return $mapper->getUniqueCount($this);
	}
	/**
	 * 
	 * @return unknown_type
	 */	
	function getStat(){
		
	}
}
?>