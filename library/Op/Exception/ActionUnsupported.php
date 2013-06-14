<?php
/**
 * Действие не поддерживается:
 * @author atukmanov
 *
 */
class Op_Exception_ActionUnsupported extends Exception {
	var $id=0;
	var $table='';
	var $action='';
	function __construct($id, $table, $action){
		$this->id=$id;
		$this->table=$table;
		$this->action=$action;
		parent::__construct('Action '.$action.' not supported for object '.$id.' in table '.$table);
	}
}
?>