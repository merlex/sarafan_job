<?php
include_once 'Tweet/Vote/Type.php';
include_once 'Tweet/Vote/Parser.php';

class Tweet_Vote_Parser_Test extends PHPUnit_Framework_TestCase {
	function testParser(){
		$parser= new Application_Model_Tweet_Vote_Parser();
		$parser->addKeyword(Application_Model_Tweet_Text_Keyword::forWord('pro'), Application_Model_Tweet_Vote_Type::pro());
		$parser->addKeyword(Application_Model_Tweet_Text_Keyword::forWord('contra'), Application_Model_Tweet_Vote_Type::contra());
		$this->assertEquals($parser->execute('pro text'),Application_Model_Tweet_Vote_Type::contra());//,Application_Model_Tweet_Vote_Type::pro());
		$this->assertEquals($parser->execute('contra text'),Application_Model_Tweet_Vote_Type::contra());
		$this->assertNull($parser->execute('just text'));  
	}
	
	function testExcept(){
		try {
			$parser= new Application_Model_Tweet_Vote_Parser();
			$parser->addKeyword(Application_Model_Tweet_Text_Keyword::forWord('yes'), Application_Model_Tweet_Vote_Type::pro());
			$parser->addKeyword(Application_Model_Tweet_Text_Keyword::forWord('no'), Application_Model_Tweet_Vote_Type::contra());
			$parser->execute('may be yes may be no');
		}
		catch(Application_Model_Tweet_Text_KeywordConflict $e){
			return;
		}
		$this->fail('No conflict exception');
	}
}
?>