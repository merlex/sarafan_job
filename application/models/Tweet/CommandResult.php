<?php
class Application_Model_Tweet_CommandResult {
	
	const OK=true;
	const Error=false;
	
	protected $status=null;
	protected $message=null;
	protected $tweet=null;
	protected $meta=null;
	/**
	 * Создать сообщение:
	 * @param $status
	 * @param $message
	 * @param $tweet
	 * @param $meta
	 * @return unknown_type
	 */
	function __construct($status, $message, $tweet, $meta){
		$this->status=$status;
		$this->message=$message;
		$this->tweet=$tweet;
		$this->meta=$meta;
	}
	/**
	 * В случае ошибки:
	 * @param $message
	 * @param $tweet
	 * @param $meta
	 * @return Application_Model_Tweet_CommandResult
	 */
	static function onError($message, $tweet, $meta=null){
		return new Application_Model_Tweet_CommandResult(
			self::Error, $message, $tweet, $meta
		);
	}
	/**
	 * В случае успеха:
	 * @param $message
	 * @param $tweet
	 * @param $meta
	 * @return Application_Model_Tweet_CommandResult
	 */
	static function onSuccess($message, $tweet, $meta){
		return new Application_Model_Tweet_CommandResult(
			self::OK, $message, $tweet, $meta
		);
	}
	
	/**
	 * Имя пользователя:
	 * @return string
	 */
	function getUserName(){
		return $this->tweet->from_user;
	}
	/**
	 * Сообщение:
	 * @return string
	 */
	function getMessage(){
		//Проходимя по meta:
		if ($this->meta){
			foreach ($this->meta as $key=>$value){
				$find[]='%'.$key.'%';
				$replace[]=$value;
			}			
		}
		//Проходимся по твиту:
		foreach ($this->tweet as $key=>$value){
			$find[]='%'.$key.'%';
			$replace[]=$value;
		}
		//Сообщение:
		return str_replace($find,$replace,$this->message);		
	}
	
	function isOK(){
		return $this->status===self::OK;
	}
	
	function isError(){
		return $this->status!=self::OK;
	}
}
?>