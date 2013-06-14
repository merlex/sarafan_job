<?php
include_once 'Op/Controller/Admin.php';
/**
 * Управление твотами:
 * @author 1
 *
 */
class TvoteController extends Op_Controller_Admin{
	/**
	 * (non-PHPdoc)
	 * @see Op/Controller/Op_Controller_Admin#indexAction()
	 */	
	function indexAction(){
		$search= new Application_Model_Tweet_Search('#HelpJapan');
		$result= $search->fetchList();
		
		Op_Util::p(count($result)); 
	}
	/**
	 * (non-PHPdoc)
	 * @see Op/Controller/Op_Controller_Admin#addAction()
	 */
	function addAction(){
		
	}
}
?>