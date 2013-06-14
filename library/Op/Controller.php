<?php
class Op_Controller extends Zend_Controller_Action {
	function throw404(){
		throw new Exception('404');
	}
	/**
	 * Получить URL:
	 * @return boolean
	 */
	function getURL(){
		$a=func_get_args();
		if (count($a)==1) $a=explode('/',$a[0]);
		$count=(count($a)-1)/2;
				
		//Reset parts:
		foreach ($this->getRequest()->getParams() as $k=>$v){
			$url[$k]=null;
		}
		$url['controller']=mb_strtolower($this->getName());
		if (count($a)){
			$url['action']=$a[0];
		}
		for ($i=0;$i<$count;$i++){
			$url[$a[2*$i+1]]=$a[2*$i+2];
		}
		
		return $this->view->url($url);
	}
	/**
	 * Получить форму
	 * @return Op_Form
	 */
	protected function getForm(){
		$formClass='Application_Form_'.$this->getTable();
		return new $formClass();
	}
	/**
	 * Получить модель:
	 * @return Op_Model
	 */
	protected function getModel($info=null){
		$modelClass='Application_Model_'.$this->getTable();
		return new $modelClass($info);
	}
	/**
	 * Получить маппер:
	 * @return Op_Mapper
	 */
	protected function getMapper(){
		$mapperClass='Application_Model_'.$this->getTable().'Mapper';
		return new $mapperClass();
	}
	
	protected $table=null;
	/**
	 * Получить таблицу
	 * @return string
	 */
	protected function getTable(){
		return $this->getName();
	}
	
	protected function getName(){
		if ($this->table) return $this->table;
		return $this->table=mb_substr(get_class($this),0,-strlen('Controller'));	
	}
	/**
	 * Вывести список:
	 * @param $select
	 * @param $itemsPerPage
	 * @return void
	 */
	protected function paginatedList($sel, $mapper, $itemsPerPage=10){
		$paginator=new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($sel));
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$paginator->setItemCountPerPage($itemsPerPage);
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_system/pages.phtml');
		$this->view->pages=$paginator;
		$this->view->list=$mapper->fetchList($paginator);
	}
}
?>