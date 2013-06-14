<?php
/**
 * Тип голоса:
 * @author 1
 *
 */
class Application_Model_Tweet_Vote_Type {
	const pro='pro';
	const contra='contra';
	/**
	 * Тип:
	 * @var string
	 */
	public $type;
	/**
	 * 
	 * @param $type
	 * @return Tweet_Vote_Type
	 */
	function __construct($type){
		if ($type==self::pro||$type==self::contra){
			$this->type=$type;
		}
		else {
			throw new InvalidArgumentException('Invalid vote type');
		}
	}
	/**
	 * Голос "за"
	 * @return Application_Model_Tweet_Vote_Type
	 */
	static function pro(){
		return new Application_Model_Tweet_Vote_Type(self::pro);
	}
	/**
	 * Голос "против"
	 * @return Application_Model_Tweet_Vote_Type
	 */
	static function contra(){
		return new Application_Model_Tweet_Vote_Type(self::contra);
	}	
	/**
	 * Значение рейтига:
	 * @return boolean
	 */
	function toRate(){
		return ($this->type==self::pro)?1:-1; 	
	}
	/**
	 * ==
	 * @return boolean
	 */
	function equal(Application_Model_Tweet_Vote_Type  $compare){		
		if ($compare->type==$this->type){
			return true;
		}
		else {
			return false;
		}
	}
	
	function __toString(){
		return $this->type;
	}
}
?>