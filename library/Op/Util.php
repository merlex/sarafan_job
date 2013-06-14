<?php
class Op_Util {
	
	static $_debugID=0;
	/**
	 * Вывод отладки:
	 * @var Op_Debug_Abstract
	 */
	static $debugMode=null;
	/**
     * Вывод отладки
     * @return unknown_type
     */    
    static function p(){
    	if (!self::$debugMode) return null;
    	self::$_debugID++;
    	self::$debugMode->printHead();
    	$a= func_get_args();
    	
    	$i=0;
    	foreach ($a as $arg){
    		if ($i>0) self::$debugMode->printDelimiter();
    		self::$debugMode->printItem($arg);
    		$i++;	
    	}
    	//Путь:    
    	$dbt=debug_backtrace(false);
    	self::$debugMode->printBacktrace($dbt);
    	//Хвост:
    	self::$debugMode->printTail();
    	
    	    	
    }
    /**
     * Переиндексировать массив:
     * @param $arr
     * @param $keyField
     * @param $valueField
     * @return array
     */
    static function ArrayReindex($arr, $keyField='id', $valueField='title'){
    	$ret=array();    	
    	foreach ($arr as $obj){
    		$ret[$obj[$keyField]]=($valueField)?$obj[$valueField]:$obj;
    	}
    	return $ret;
    }
}
?>