<?php
/**
 * Посещения:
 */
class Application_Model_Ad_VisitMapper extends Op_Mapper {
	
	var $table='ad_visit';
	
	function getOrderBy(){
		return 'ts DESC';
	}
}
?>