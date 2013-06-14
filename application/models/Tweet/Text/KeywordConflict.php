<?php
class Application_Model_Tweet_Text_KeywordConflict extends Exception {
	protected $text;
	
	function getText(){
		return $text;
	}
	
	function __construct($text){
		$this->text=$text;
		return parent::__construct('Keyword conflict');
	}
}
?>