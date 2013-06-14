<?php
class Application_Model_Tweet_PollException extends Exception {
	const invalidMessage=1;	
	const justExists=2;
	/**
	 * Создать:
	 * @param $message
	 * @param $code
	 * @return boolean
	 */
	function __construct($message, $code){	
		parent::__construct(($message)?$message:'twit_poll_error_'.$code, $code);
	}
}
?>