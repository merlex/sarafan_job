<?php
/**
 * Ключевое слово для продвижения:
 * @author 1
 *
 */
class Application_Model_Tweet_Poll_Keyword extends Op_Model {
	/**
	 * Чтец твиттера:
	 * @var Application_Model_Tweet_Search
	 */
	protected $reader=null;
	
	function setReader($reader){
		$this->reader=$reader;
	}		
	/**
	 * Выполнить:
	 * @return booelan
	 */
	function execute(){
		//Ищем по ключевому слову "начала" и "конца", что позволяет прибить старые:
		$search=$this->reader->search('#'.$this->start_word.' OR #'.$this->stop_word);	
		$search->sinceID($this->since_id);
		//Проходимся по сообщениям:
		$ret=array();			
		foreach ($search->fetchList() as $tweet){
			$ret[]=$this->executeCommand($tweet);
		}
		return $ret;
	}
	
	const start='start';
	const stop='stop';
	/**
	 * Выполнить команду:
	 * @param $tweet
	 * @return void
	 */
	protected function executeCommand($tweet){
		switch ($this->getCommandType($tweet)){
			case self::start:
				return $this->executeStartCommand($tweet);
			break;
			case self::stop:
				return $this->executeStopCommand($tweet);
			break;
			default:
				//Не найден тех:
				return Application_Model_Tweet_CommandResult::onError(self::noTagInStartCommand, $tweet);
			break;
		}
	}
	/**
	 * Начать голосование:
	 * @param $tweet
	 * @return Application_Model_Tweet_Poll
	 */
	protected function executeStartCommand($tweet){
		if ($tag=$this->fetchTag($tweet->text)){
			$pollMapper= new Application_Model_Tweet_PollMapper();
			if ($poll=$pollMapper->loadNonFinishedPoll($tag)){
				//Уже есть такой опрос и он не завершен:
				return Application_Model_Tweet_CommandResult::onError(self::pollExists, $tweet, $poll);				
			}
			else {
				//Создаем опрос:
				$poll= Application_Model_Tweet_Poll::create($tag, $tweet);				
				//Возвращаем ответ:				
				return Application_Model_Tweet_CommandResult::onSuccess(self::pollStarted,$tweet,$poll);
			}
		}
		else {
			//Не найден тех:
			return Application_Model_Tweet_CommandResult::onError(self::noTagInStartCommand, $tweet);			
		}
	}
	/**
	 * Команда на остановку
	 * @param $tweet
	 * @return $poll остановленный запрос
	 */
	protected function executeStopCommand($tweet){
		if ($tag=$this->fetchTag($tweet->text)){
			$pollMapper= new Application_Model_Tweet_PollMapper();
			if ($poll=$pollMapper->loadNonFinishedPoll($tag)){
				//Есть такой опрос:
				$user=Application_Model_Tweet_User::loadByTweetLazy($tweet);
				if ($poll->checkPermission(Application_Model_Tweet_Poll::stop, $user)){
					//Опрос начат другим пользователем:
					return Application_Model_Tweet_CommandResult::onError(self::stopDenied,$tweet,$poll);					
				}
				else {
					//Завершаем запрос:
					$poll->stop();
					return Application_Model_Tweet_CommandResult::onSuccess(self::pollStoped, $tweet, $poll);
				}
			}
			else {
				//Нет активного запроса с таким тегом:
				return Application_Model_Tweet_CommandResult::onError(self::pollToStopNotExists, $tweet, array('tag'=>$tag));				
			}
		}
	}
	
	const pollExists='Poll with tag %tag% just exists';	
	const noTagInStartCommand='No start tag in command %command% from %user%';
	const pollStarted='Poll with tag #%tag% started';
	const stopDenied='Unable to stop poll #%tag%';
	const pollToStopNotExists='No poll with tag #%tag% to stop';
	/**
	 * Ошибка обработки:
	 * @param string $error
	 * @param object $tweet
	 * @param string $sendTo имя пользователя которому предназначено сообщение
	 * @return false
	 */
	protected function throwError($error, $args, $sendTo){
		$this->log($error, $args, 'error');
	}
	/**
	 * Логирование:
	 * @param $message
	 * @param $object
	 * @param $level
	 * @return boolean
	 */
	protected function log($message, $obj, $level='debug'){
		$find=array();
		$replace=array();
		foreach ($obj as $f=>$v){
			$find[]='%'.$f.'%';
			$replace[]=$v;			
		}
		Op_Util::p(str_replace($find, $replace, $message),$level);
		return false;
	}
	
	
	/**
	 * Регулярка для вычленения хэштегов:
	 * @var string
	 */
	const tagPreg='/\b\#(.+?)\b/u';
	/**
	 * Получить #тег из сообщения
	 * 1. Игнорируем start и stop комманды
	 * 2. Возвращаем первый тег в тексте
	 * @return string
	 */
	protected function fetchTag($tweet){
		$text=$tweet->text;
		
		//Проходимся регуляркой:			
		if (preg_match_all(self::tagPreg, $message, $tags)){
			$tag=null;		
			foreach ($tags[1] as $tag){
				if  ($tag!=$this->start_word && $tag!=$this->stop_word){
					return $tag;
				}
			}			
		}
		return null;
	}
	/**
	 * Получить сообщение из твита:
	 * @param $tweet
	 * @return string
	 */
	function fetchMessage($tweet){
		//Делаем нормальные пробелы (для работы \b)
		return str_replace('&nbsp;',' ',$tweet->text);
	}
	/**
	 * Тип команды: 
	 * @param $tweet
	 * @return string
	 */
	protected function getCommandType($tweet){
		$parser= new Application_Model_Tweet_Vote_Parser();
		$parser->addKeyword(Application_Model_Tweet_Text_Keyword::forHash($this->start_word),self::start);
		$parser->addKeyword(Application_Model_Tweet_Text_Keyword::forHash($this->stop_word),self::stop);
		return $parser->execute($tweet->text); 		
	}
}
?>