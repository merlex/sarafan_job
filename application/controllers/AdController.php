<?php
include_once 'Op/Controller/Admin.php';
/**
 * Объявления
 * @author 1
 *
 */
class AdController extends Op_Controller_Admin {
	function init(){
		$cfg=Zend_Registry::get('config');
		$this->view->canvasURL=$cfg->production->facebook->canvasURL;
		parent::init();
	}
	/**
	 * Публикация:	 
	 */
	function publishAction(){
		try {
			if ($obj=$this->_confirmAction()){
				$obj->publish();
				$this->getMapper()->save($obj);
				return $this->redirectBack();
			}			
		}
		catch (Op_Exception_NotFound $e){
			return $e->getMessage();
		}
		catch (Op_Exception_ActionUnsupported $e){
			return $e->getMessage();
		}
	}
	/**
	 * Публикация:	 
	 */
	function unpublishAction(){
		try {
			if ($obj=$this->_confirmAction()){
				$obj->unpublish();
				$this->getMapper()->save($obj);
				return $this->redirectBack();
			}			
		}
		catch (Op_Exception_NotFound $e){
			return $e->getMessage();
		}
		catch (Op_Exception_ActionUnsupported $e){
			return $e->getMessage();
		}
	}
		
	protected $mode;
	function indexAction(){
		
		
		$mode=$this->getRequest()->getParam('show','all');
		if (!isset($this->view->modes[$mode])) $mode='all';
		$this->view->mode=$mode;
				
		parent::indexAction();
	}
	
	protected $show=null;
	protected $categoryID=0;
	/**
	 * Подготовить фильтры:
	 * @return boolean
	 */
	function prepareFilters(){
		
		$request=$this->getRequest();
		$categories= new Application_Model_CategoryMapper();
		$this->view->categories= Op_Util::ArrayReindex($categories->fetchAll());
		
		if ($this->categoryID=$request->getParam('category',0)){
			if (!isset($this->view->categories[$this->categoryID])){
				throw new Op_Exception_NotFound($this->categoryID,'category');
			}					 	
		}	
		$this->view->categoryID=$this->categoryID;
		
		/**
		 * Обработка вывода:
		 */
		$this->view->show_modes=array(
			'all'=>array('title'=>'Все','url'=>$this->getURL('index','show','all','category',$this->categoryID)),
			'published'=>array('title'=>'Опубликованные','url'=>$this->getURL('index','show','published','category',$this->categoryID)),
			'archive'=>array('title'=>'Архив','url'=>$this->getURL('index','show','archive','category',$this->categoryID)),
		);
		
		$this->show=$request->getParam('show','all');
		if (!isset($this->view->show_modes[$this->show])){
			throw new Op_Exception_ActionUnsupported($this->show,'ad','show');
		}
		$this->view->show_mode=$this->show;
		//Запоминаем метод:
		$this->view->categories_action=$this->getURL('index','show',$this->show,'page',null,'category',null);
		
	}
	/**
	 * (non-PHPdoc)
	 * @return Application_Model_AppMapper
	 */
	function getMapper(){
		return parent::getMapper();
	}
	/**
	 * Получить выборку:
	 * @return Zend_Db_Select
	 */
	function getSelect(){
		$mapper=new Application_Model_AdMapper();
		
		
		switch ($this->show){
			case 'all':
				$sel= $mapper->getListSelect();
			break;
			case 'published':				
				$sel= $mapper->getActiveSelect();				
			break;
			case 'archive':
				$sel= $mapper->getArchiveSelect();
			break;
		}
		if ($this->categoryID){
			$sel->where('category_id=?',$this->categoryID);
		}
		return $sel;	
	}
}
?>