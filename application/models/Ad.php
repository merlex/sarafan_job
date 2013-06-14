<?php
include_once 'Op/Model.php';
/**
 * 
 * @author atukmanov
 *
 */
class Application_Model_Ad extends Op_Model {
	/**	 
	 * Проставляем время создания
	 */
	function initNew(){
		$this->setValue('create_ts',time());
	}	
	/**
	 * Опубликовать:
	 */
	function publish(){
		if ($this->is_active) throw new Op_Exception_ActionUnsupported($this->getID(),'ad','publish');		
		$this->setValue('is_active',1);
	}
	/**
	 * Снять публикацию
	 */
	function unpublish(){
		if (!$this->is_active) throw new Op_Exception_ActionUnsupported($this->getID(),'ad','unpublish');		
		$this->setValue('is_active',0);
		$this->setValue('final_ts',time());
	}
}
?>