<?php
/**
 * Построить пост:
 * @author 1
 *
 */
class Application_Model_Ad_Visit extends Op_Model {
	/**
	 * Фабрика:
	 * @param $ad
	 * @param $user
	 * @return Application_Model_Ad_Visit
	 */
	static function factory(Application_Model_Ad $ad, Application_Model_Facebook_User $user){
		$ret= new Application_Model_Ad_Visit();
		$ret->setInfo(array(
			'user_id'=>$user->getID(),
			'ad_id'=>$ad->getID(),
			'ts'=>time()
		));
		return $ret;
	}	
}
?>