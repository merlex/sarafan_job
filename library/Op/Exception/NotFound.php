<?php
/**
 * Объект не найден:
 * @author atukmanov
 *
 */
class Op_Exception_NotFound extends Exception {
	
	var $id=0;
	var $table=null;
	
	function __construct($id, $table){
		$this->id=$id;
		$this->table=$table;
		parent::__construct('Object with id='.$id.' not found in table '.$table, 404);
	}
}
?>