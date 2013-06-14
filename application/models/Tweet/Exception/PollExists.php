<?php
/**
 * Такой опрос уже есть:
 * @author 1
 *
 */
class Application_Model_Tweet_Exception_PollExists extends Exception {
	/**
	 * Опрос:
	 * @var Application_Model_Tweet_Poll
	 */
	protected $_poll;
	
	function getPoll($poll){
		return $this->_poll;
	}
	/**
	 * 
	 * @param $poll
	 * @return unknown_type
	 */
	function __construct($poll){
		$this->_poll=$poll;
		parent::__construct('Poll with tag #'.$poll->tag.' just exists');
	}
}
?>