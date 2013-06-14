<?php
include_once 'Op/Util.php';
include_once 'Op/Controller/Admin.php';
/**
 * Категории
 * @author atukmanov
 *
 */
class CategoryController extends Op_Controller_Admin {
	var $decorators=array('order');
	var $submitText='Сохранить';
	
	function reorderAction(){
		$order=$this->getRequest()->getParam('order');
		asort($order);
		$mapper=$this->getMapper();
		$i=1;
		foreach ($order as $id=>$order){
			if ($obj=$mapper->find($id)){
				$obj->order_by=$i;
				$save[]=$obj;
			}
			else {
				throw new Exception('404');
			}
			$i++;
		}
		foreach ($save as $obj){
			$mapper->save($obj);
		}
		return $this->_helper->redirector('index');		
	}
	
	function getLimit(){
		return 100;
	}
}
?>