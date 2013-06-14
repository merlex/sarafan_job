<?php
/**
 * Ключевые слова:
 * @author 1
 *
 */
class Application_Model_Tweet_Text_Keyword {
	/**
	 * generate preg for whole word:
	 * @return string
	 */
	static function wholeWord($word){
		return '/\b'.preg_quote($word,'/').'\b/ui';
	}
	/**
	 * Ключевые слова:
	 * @var array
	 */
	protected $keywords=array();
	/**
	 * 
	 * @param $keywords
	 * @return
	 */
	function __construct($keywords=array()){
		$this->keywords=$keywords;
	}
	/**
	 * Проверить текст:
	 * @param $text
	 * @return boolean
	 */
	function test($text){
		foreach ($this->keywords as $word){
			if ($this->testWord($word, $text)) return true;
		}
		return false;
	}
	/**
	 * Для слова:
	 * @param $keyword
	 * @return Tweet_Text_Keyword
	 */
	static function forWord($keyword){
		return new Application_Model_Tweet_Text_Keyword(array($keyword));	
	}
	/**
	 * Для хэштега:
	 * @param $hash
	 * @return Tweet_Text_Keyword
	 */
	static function forHash($hash){
		return new Application_Model_Tweet_Text_Keyword(array('/\b\#'.$hash.'\b/'));
	}
	/**
	 * Является ли слово регулярным выражением:
	 * @param $word
	 * @return boolean
	 */
	protected function isPreg($word){
		return preg_match('@^/(.*)/(\w*)$@',$word);
	}
	/**
	 * Проверить слово на вхождение в текст:
	 * @param string $text
	 * @param string $word
	 * @return void
	 */
	protected function testWord($word, $text){
		if ($this->isPreg($word)){			
			return $this->testPreg($word,$text);
		}
		else {
			return $this->testSubstring($word,$text);
		}
		
	}
	/**
	 * Проверить регулярку:
	 * @param $preg
	 * @param $word
	 * @return boolean
	 */
	protected function testPreg($preg, $text){
		return preg_match($preg, $text);
	}	
	/**
	 * Проверить подстроку
	 * @param $word
	 * @param $text
	 * @return boolean
	 */
	protected function testSubstring($substring, $text){
		return (false!==strpos($text,$substring));
	}
}
?>