<?php
class Op_Debug_Html extends Op_Debug_Abstract{
	
	function printHead(){
		echo '<div style="border:1px solid red; padding:10px 5px;">';	
	}
	
	function printDelimiter(){
		echo '<hr/>';
	}
	
	function printItem($arg){		
    	ob_start();
    	parent::printItem($arg);
    	return highlight_string(ob_get_clean());
	}
	
	function printTail(){
		echo '</div>';
	}
}
?>