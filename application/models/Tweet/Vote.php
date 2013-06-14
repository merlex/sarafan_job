<?php
/**
 * Голос:
 * @author 1
 *
 */
class Application_Model_Tweet_Vote extends Op_Model {	
	/**
	 * Залогировать голос:
	 * 
	 * @param Application_Model_Tweet_Vote_Type $type
	 * @param Application_Model_Tweet_Poll $poll
	 * @param Application_Model_Tweet_User $user
	 * @param tweet $tweet
	 * @param boolean $accepted
	 * @return Application_Model_Tweet_Vote
	 */
	static function log($type, $poll, $user, $tweet, $accepted){
		
		$ret=new Application_Model_Tweet_Vote(array(
			'type'=>$type->type,
			'poll_id'=>$poll->forceID(),
			'user_id'=>$user->forceID(),
			
			'accepted'=>$accepted,		
			'ts'=>time(),
		));
		$ret->save();
		//Записываем в данные пользователя:
		Application_Model_Tweet_Poll_User::log($ret);
		return $ret;
	}
	/**
	 * Выставить опрос:
	 * @param Op_Model $poll
	 * @return 
	 */
	function setPoll($poll){
		$this->info['poll_id']=$poll->getID();
	}
	/**
	 * Получить тип комманды:
	 * @return Application_Model_Tweet_Vote_Type
	 */
	function getType(){
		return new Application_Model_Tweet_Vote_Type($this->info['type']);
	}
	/**
	 * 
	 * @param Application_Model_Tweet_Vote_Type $type
	 * @return void
	 */
	function setType($type){
		$this->info['type']=$type->type;
	}
	/**
	 * 
	 * @param Application_Model_Tweet_User $user
	 * @return void
	 */
	function setUser($user){
		$this->info['user_id']=$user->forceID();
	}

	protected $_tweet=null;
	/**
	 * Выставить твит:
	 * @param $tweet
	 * @return void
	 */
	function setTweet($tweet){		
		$this->info['tweet_id']=$tweet->id_str;
		$thid->info['user_id']=Application_Model_Tweet_User::loadByTweet($tweet)->getID();
	}
	
	function getTweet(){
		return $this->_tweet;
	}
	
}
?>