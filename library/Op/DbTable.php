<?php
/**
 * Базовый класс таблицы:
 * @author 1
 *
 */
class Op_DbTable extends Zend_Db_Table_Abstract {
	protected $_name=null;
	/**
	 * Фабрика
	 * @param $table
	 * @return Op_DbTable
	 */
	static function factory($table){
		$ret= new Op_DbTable();
		$ret->_name=mb_strtolower($table);
		return $ret;
	}
}
?>