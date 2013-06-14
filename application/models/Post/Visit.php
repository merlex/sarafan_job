<?php
/**
 * Построить пост:
 * @author 1
 *
 */
class Application_Model_Post_Visit extends Op_Model {
	/**
	 * Фабрика:
	 * @param $ad
	 * @param $user
	 * @return Application_Model_Ad_Visit
	 */
	static function factory(Application_Model_Post $post, Application_Model_Facebook_User $user){
		$ret= new Application_Model_Post_Visit();
		$ret->setInfo(array(
			'user_id'=>$user->getID(),
			'post_id'=>$post->getID(),
			'ts'=>time()
		));
		return $ret;
	}	
}
?>