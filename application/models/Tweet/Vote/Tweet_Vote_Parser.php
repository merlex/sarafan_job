<?php
/**
 * Парсер голосов:
 * @author 1
 *
 */
class Application_Tweet_Vote_Parser {
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
	
	function __construct($pro, $contra){
		$this->pro=$pro;
		$this->contra=$contra;	
	}
	/**
	 * Проверка типа голоса:
	 * @return string
	 */
	function fetchVoteType($text){
		$isPro=$this->pro->test($text);
		$isContra=$this->contra->test($text);
		if ($isPro && $isContra){
			//Голос не может быть одного типа:
			throw new Application_Tweet_Text_Keyword_Conflict($text);
		}
		elseif ($isPro){
			return Application_Model_Tweet_Vote_Type::pro();
		}
		elseif ($isContra){
			return Application_Model_Tweet_Vote_Type::contra();
		}
		else {
			//Нет ключевых слов:
			return null;
		}
		
	}	
}
?>