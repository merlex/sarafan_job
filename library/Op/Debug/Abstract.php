<?php
/**
 * Базовый класс вывода отладки:
 * @author 1
 *
 */
abstract class Op_Debug_Abstract {
	abstract function printHead();
	
	abstract function printDelimiter();
	
	abstract function printTail();
	/**
	 * Запись:
	 * @param $arg
	 * @return unknown_type
	 */
	function printItem($arg){
				
    	if ($arg===false) return 'bool(false)';
    	elseif ($arg===null) echo 'null';
    	if (is_string($arg)) echo $arg;    	
    	else print_r($arg);    
	}
	/**
	 * Цепочка вызовов:	 
	 */
	function printBacktrace(){
		return;
	}
}
?>