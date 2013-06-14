<?php
/**
 * twitter опрос:
 * @author 1
 *
 */
class Application_Model_Tweet_Poll extends Op_Model{
	/**
	 * Команды "за":
	 * @var Application_Model_Tweet_Vote_Parser
	 */
	var $parser=null;
	
	/**
	 * Выполнить поиск:
	 * @return unknown_type
	 */
	function execute(){
		$search= new Application_Model_Tweet_Search('#'.$this->tag);
		$search->sinceID($this->last_tweet);
		$tweets= $search->fetchList();
		if (count($tweets)){
			//Обновить:				
			foreach ($tweets as $tweet){				
				$result=$this->executeTweet($tweet);
			}
			
		}		
		//Обновляем время последнего просмотра:
		$this->last_check_ts=time();
		if ($this->end_tweet){
			//Если опрос закрыт помечаем как завершенный:
			$this->end_ts=time();			
		}
		$this->save();
	}
	/**
	 * Изменить since_id (обновляется только на большее)
	 * @return void
	 */
	function setSince_ID($value){
		if ($this->since_id<$value){
			$this->info['since_id']=$value;
			$this->last_update_is=time();
			$this->markChanged('since_id');			
		}
	}	
	/**
	 * Выполнить tweet:
	 * @param object $tweet
	 * @return Application_Model_Tweet_CommandResult
	 */
	function executeTweet($tweet){
		$this->since_id=$tweet->id_str;
		/**
		 * Вычисляем тип комманды:
		 */
		try {
			$tweetType=$this->getTweetType($tweet);
		}
		catch (Application_Model_Tweet_Text_Keyword_Conflict $e){
			//Конфликт команд:
			return Application_Model_Tweet_CommandResult::onError(self::commandConflict, $tweet);
		}
		//Проверяем что есть команда:
		if (!$tweetType){
			//Нет команды:
			return Application_Model_Tweet_CommandResult::onError(self::noCommand, $tweet);
		}
		$vote=$this->createVote($tweet);
		$vote->setTweet($tweet);
		$vote->setType($tweetType);			
		/**
		 * Проверяем на уникальность:
		 */		
		return $this->executeVote($vote);		
	}
	/**
	 * Отменить последнюю команду:
	 * @param Application_Model_Tweet_Vote $vote
	 * @return void
	 */
	function discardVote($vote){
		$type=$vote->getType();
		$this[$type->type]-=1;				
		$this['rate']-=$type->toRate();
	}
	/**
	 * Применить голос:
	 * @param Application_Model_Tweet_Vote $vote
	 * @return void
	 */
	function applyVote($vote){
		$type=$vote->getType();
		$this->info[$type->type]+=1;
		$this->info['rate']+=$type->toRate();
	}
	
	/**
	 * Создать голос:
	 * @return Application_Model_Tweet_Vote
	 */
	function createVote($tweet){
		$vote=Application_Model_Tweet_Vote();
		$vote->setPoll($this);		
		return $vote;
	}
	/**
	 * Проверить голос:
	 * @return 
	 */
	function executeVote(Application_Model_Tweet_Vote $vote){
		//Получаем пользователя:		
		if ($lastCommand=$this->fetchLastCommand($vote->user_id)){
			//Проверяем что он посылал ранее:			
			if ($tweetType->equal($lastCommand->getType())){
				//Повтор голоса:
				$ret= Application_Model_Tweet_Vote_Result::onError(self::duplicateVoice);
			}
			else {				
				//Отвеняем последний голос:
				$this->discardLastCommand($lastCommand);
				//Результат OK:
				$ret= Application_Model_Tweet_Vote_Result::onSuccess(self::voteChanged);
			}
		}
		else {
			$ret=Application_Model_Tweet_Vote_Result::onSuccess(self::voteCreated);
		}				
		//Сохраняем запись о голосе:
		$vote->accepted=$result->isOK();		
		$vote->save();
		//Связваем tweet и голос в отчете:
		$ret->
		$this->applyVote($vote);
	}
	
	/**
	 * Получить последнюю команду пользователя в данном опросе:
	 * @param $user
	 * @return Application_Model_Tweet_Poll_Vote
	 */
	function fetchLastCommand($user_id){
		//Экономим на спичках (новый пользователь- не проверяем голоса):		
		if (!$user->getID()) return null;
		$mapper= new Application_Model_Tweet_VoteMapper();
		return $mapper->getLastUserVote($user_id, /*in poll */$this->getID());
	}	
	/**
	 * Голос обратный данному:
	 * @param $tweetType
	 * @return string
	 */
	protected function not($tweetType){
		return ($tweetType==self::pro)?self::contra:self::pro;
	}
	/**
	 * Получить изменение рейтира для типа:
	 * @param $tweetType
	 * @return unknown_type
	 */
	protected function rate($tweetType){
		return ($tweetType==self::pro)?1:-1;
	}
	
	const noCommand='No commads discovered in tweet %text%';
	
	
	/**
	 * Получить тип голоса:
	 * @param object $tweet
	 * @return Application_Model_Tweet_Vote_Type
	 */
	protected function getTweetType($tweet){
		//Проверяем на команды "за":
		return $this->parser->fetchVoteType($tweet->text);		
	} 
	
	/**
	 * Создать новый опрос:
	 * @param string $tag
	 * @param object $tweet
	 * @param Application_Model_Tweet_Poll_Keyword $keyword
	 * @return Application_Model_Tweet_Poll
	 */
	static function create($tag, $tweet, $keyword){
		$poll= new Application_Model_Tweet_Poll(array(
			'tag'=>$tag,
			'keyword_id'=>$keyword->getID(),
			//Пользователь:
			'user_id'=>Application_Model_Tweet_User::loadByTweet($tweet)->getID(),
			//Лог запуска:
			'start_ts'=>time(),
			'start_tweet'=>$tweet->id_str,
			//Лог обновления:
			'last_update_ts'=>time(),
			'last_check_ts'=>time(),		
			'last_tweet'=>$tweet->id_str,
		));
		//Сохраняем:
		$poll->save();
		return $poll;
	}
	/**
	 * Завершить:
	 * @return boolean
	 */
	function stop($tweet){
		$this->stop_tweet=$tweet->id_str;
		$this->save();
	}
	//Остановка опроса:
	const stop='stop';
	/**
	 * Проверка прав:
	 * @param $action
	 * @param $user
	 * @return boolean
	 */
	function checkPermission($action, $user){
		switch ($action){
			case self::stop:
				return $this->user_id==$user->getID();
			break;
			default:
				return false;
			break;
		}
	}
		
}
?>