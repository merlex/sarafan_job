<?php
class Op_Exception_DuplicateEntry extends Exception {
	protected $obj;
	protected $table;
	protected $column;
	
	function __construct($obj, $table, $column, $code=0){
		$this->obj=$obj;
		$this->table=$table;
		$this->column=$column;
		parent::__construct("Record with {$column} '".$obj[$column]."' just exists in table {$table}");
	}	
}
?>