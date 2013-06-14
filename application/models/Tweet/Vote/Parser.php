<?php
include_once 'Tweet/Text/KeywordConflict.php';
/**
 * Парсер голосов:
 * @author 1
 *
 */
class Application_Model_Tweet_Vote_Parser {
	/**
	 * Проверка "за"
	 * @var Tweet_Text_Keyword
	 */
	protected $pro;
	/**
	 * Проверка "против"
	 * @var unknown_type
	 */
	protected $contra;
	
	function __construct(){
		 
	}
	
	protected $_clauses=array();
	/**
	 * 
	 * @param Application_Model_Tweet_Text_Keyword $keyword
	 * @param mixed $return
	 * @return int
	 */
	function addKeyword($keyword, $for){
		$id=count($this->_clauses);
		$this->_clauses[$id]=array($keyword, $for);	
	}
	/**
	 * Проверка типа голоса:
	 * @return mixed
	 */
	function execute($text){
		$justFounded=null;
		$ret=null;
		foreach ($this->_clauses as $clause){
			list($keyword, $return)=$clause;
			if (false) $keyword= new Application_Model_Tweet_Text_Keyword();
			if ($keyword->test($text)){
				if ($justFounded){
					//Более одного ключевого слова:
					throw new Application_Model_Tweet_Text_KeywordConflict($text);
				}
				else {
					//Уже одно:
					$justFounded=true;
					$ret=$return;					
				}
			}
		}
		
		return $ret;
	}	
}
?>