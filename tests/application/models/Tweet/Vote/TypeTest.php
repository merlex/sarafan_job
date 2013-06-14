<?php
include_once 'Tweet/Vote/Type.php';
/**
 * Тест для типа голоса:
 * @author Atukmanov
 *
 */
class Tweet_Vote_Type_TestCase extends PHPUnit_Framework_TestCase {
	/**
	 * Проверка фабрик:
	 * @return boolean
	 */
	public function testFactory(){
		$pro=Application_Model_Tweet_Vote_Type::pro();
		$this->assertEquals($pro->type, Application_Model_Tweet_Vote_Type::pro,'Pro factory');
		$contra=Application_Model_Tweet_Vote_Type::contra();
		$this->assertEquals($contra->type, Application_Model_Tweet_Vote_Type::contra,'Contra factory');
		
	}
	
	public function testEqual(){
		$pro=Application_Model_Tweet_Vote_Type::pro();
		$contra=Application_Model_Tweet_Vote_Type::contra();
		
		$this->assertEquals($pro->equal($contra),false,'pro!=contra');
		$this->assertEquals($contra->equal($pro),false,'pro!=contra');
		$this->assertEquals($pro->equal(new Application_Model_Tweet_Vote_Type(Application_Model_Tweet_Vote_Type::pro)),true,'pro==pro');
		$this->assertEquals($contra->equal(new Application_Model_Tweet_Vote_Type(Application_Model_Tweet_Vote_Type::contra)),true,'pro==pro');
	}
	
	public function testInt(){
		$pro=Application_Model_Tweet_Vote_Type::pro();
		$this->assertEquals($pro->toRate(),1,'pro to rate');
		$contra=Application_Model_Tweet_Vote_Type::contra();
		$this->assertEquals($contra->toRate(),-1,'contra to rate');
	}
	/**
     * @expectedException InvalidArgumentException
     */
    public function testException()
    {
    	$type= new Application_Model_Tweet_Vote_Type(1);
    }
	
}
?>