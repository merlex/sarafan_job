<?php
/**
 * Активность пользователя в опросе:
 *
 */
class Application_Model_Tweet_Poll_User extends Op_Model {
	/**
	 * 
	 * @param Application_Model_Tweet_Poll_User $vote
	 * @return Application_Model_Tweet_Poll_User
	 */
	static function log($vote){
		$mapper= new Application_Model_Tweet_Poll_UserMapper();
		if ($ret=$mapper->loadPollUser($vote->poll_id, $vote->user_id)){
			//Инкрементируем кол-во:
			$ret[$vote->type]+=1;
		}
		else {
			//Новый:
			$ret= new Application_Model_Tweet_Poll(array(
				'user_id'=>$vote->user_id,
				'poll_id'=>$vote->poll_id,
				$vote->type=>1,
			));
		}
		//Сохраняем:
		$ret->save();
		return $ret;
	}
}
?>