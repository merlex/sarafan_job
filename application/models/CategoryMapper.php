<?php
include_once 'Op/Mapper.php';
/**
 * Котегории
 * @author atukmanov
 *
 */
class Application_Model_CategoryMapper extends Op_Mapper {
	var $table='Category';
	var $listFields=array('id','title','order_by');
	
	function getOrderBy(){
		return 'order_by ASC';
	}
	
}
?>
