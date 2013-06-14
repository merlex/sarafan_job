<?php
include_once 'Tweet/Text/Keyword.php';
/**
 * Тест для типа голоса:
 * @author Atukmanov
 *
 */
class Tweet_Vote_Text_Ketword_TestCase extends PHPUnit_Framework_TestCase {
	function testPlain(){
		$test= new Application_Model_Tweet_Text_Keyword(array('test'));
		$this->assertTrue($test->test('hello i am test text'),'test plain exists');
		$this->assertFalse($test->test('hello world'),'test plain text not exists');
	}
	
	function testPreg(){
		$test= new Application_Model_Tweet_Text_Keyword(array('/\b\d+\b/'));
		$this->assertTrue($test->test('hello 323 i am test text'),'test preg exists');
		$this->assertFalse($test->test('hello world'),'test preg not exists');
	}
	
	function testMulti(){
		$test= new Application_Model_Tweet_Text_Keyword(array('one','two'));
		$this->assertTrue($test->test('This is one test'),'first keyword');
		$this->assertTrue($test->test('This is test two'),'second keyword');
		$this->assertFalse($test->test('This is test tow'),'second keyword');
	}
	
	function testWholeWord(){
		
		$kw= new Application_Model_Tweet_Text_Keyword(array(
			Application_Model_Tweet_Text_Keyword::wholeWord('unit test'),
			Application_Model_Tweet_Text_Keyword::wholeWord('мама'),
		));
	
		$this->assertTrue($kw->test('it is a unit test'),'at begin lat');
		$this->assertTrue($kw->test('мама мыла раму'),'at begin cyr');
		$this->assertTrue($kw->test('мыла мама раму'),'at middle cyr');
		$this->assertTrue($kw->test('мыла раму мама'),'at end cyr');
		$this->assertFalse($kw->test('вкусная мамалыга'),'word part cyr');
	}
}
?>