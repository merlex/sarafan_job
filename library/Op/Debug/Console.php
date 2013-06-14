<?php
include_once 'Op/Debug/Abstract.php';
/**
 * Отладка в консоле:
 * @author 1
 *
 */
class Op_Debug_Console extends Op_Debug_Abstract{
	function printHead(){
		echo PHP_EOL,str_repeat('_',10),PHP_EOL;
	}
	
	function printDelimiter(){
		echo PHP_EOL;
	}
	
	function printTail(){
		echo PHP_EOL,str_repeat('- ',5),PHP_EOL;
	}
}
?>