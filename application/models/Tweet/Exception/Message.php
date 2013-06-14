<?php
class Application_Model_Tweet_Exception_Message extends Exception {
	protected $_tweet=null;
	protected $_errorType=null;
	
	const pollTagInvalid=1;
	
	function __construct($tweet, $errorType){
		$this->_tweet=$tweet;
		parent::__construct('Message "'.$tweet.'" invalid', $errorType);
		
	}
}
?>