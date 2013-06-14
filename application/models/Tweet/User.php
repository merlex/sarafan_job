<?php
class Application_Model_Tweet_User extends Op_Model{
	/**
	 * Загрузить по твиту:
	 * @param $tweet
	 * @return Application_Model_Tweet_User
	 */
	static function loadByTweet($tweet){		
		$ret=self::loadByTweetLazy($tweet);
		if (!$ret->getID()){
			$ret->save();
		}
		return $ret;
	}
	/**
	 * Ленивая загрузка пользователя (без создания)
	 * @param $tweet
	 * @return Application_Model_Tweet_User 
	 */
	static function loadByTweetLazy($tweet){
		if ($ret=self::findByTweet($tweet)){
			return $ret;
		}
		$ret= new Application_Model_Tweet_User();		
		$ret->name=$tweet->from_user;
		$ret->ts=time();								
		return $ret;
	}
	/**
	 * Поиск по твиту:
	 * @param $tweet
	 * @return Application_Model_Tweet_User
	 */	
	static function findByTweet($tweet){
		$mapper= new Application_Model_Tweet_UserMapper();
		if ($ret=$mapper->loadByTwitterUser($tweet->from_user)){		
			return $ret;
		}
		else {
			return null;
		}
	}
}
?>