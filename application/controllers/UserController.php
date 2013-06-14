<?php
include_once 'Op/Controller/Admin.php';
/**
 * Пользователи:
 * @author 1
 *
 */
class UserController extends Op_Controller_Admin {
	
	const setAdmin='setAdmin';
	/**
	 * Nod add, no edit, no delete, no career, no future
	 * @param $action
	 * @return boolean
	 */
	function checkActionPermissions($action){
				
		if ($action==self::setAdmin){
			return true;
		}
		return false;
	}
	
	protected $search=null;
	
	function prepareFilters(){
		//Поиск по имени:
		$request=$this->getRequest();
		$this->view->search=$this->search=$request->getParam('search');
		/**
		 * Обработка вывода:
		 */
		$this->view->show_modes=array(
			'all'=>array('title'=>'Все','url'=>$this->view->url(array('show'=>'all','page'=>null))),
			'is_post'=>array('title'=>'Авторы','url'=>$this->view->url(array('show'=>'is_post','page'=>null))),
			'is_admin'=>array('title'=>'Администраторы','url'=>$this->view->url(array('show'=>'is_admin','page'=>null))),
		);
		
		$this->show=$request->getParam('show','all');
		if (!isset($this->view->show_modes[$this->show])){
			throw new Op_Exception_ActionUnsupported($this->show,'ad','show');
		}
		$this->view->show_mode=$this->show;			
	}
	
	function getSelect(){
		$sel=parent::getSelect();
		if ($this->search){
			$sel->Where('name like ?','%'.$this->search.'%');
		}
		if ($this->show=='is_post'){
			$sel->Where('is_post=1');
		}
		if ($this->show=='is_admin'){
			$sel->Where('is_admin=1');
		}
		return $sel;
	}
	/**
	 * Назначение администратора:
	 * @return void
	 */
	function adminAction(){
		$this->getRequest();
		if (!$this->checkActionPermissions(self::setAdmin)){
			return $this->render('403');
		}
		$user=$this->getMapper()->find($this->getRequest()->getParam('id'));
		if (!$user){
			return $this->render('404');
		}
		$form= new Application_Form_Confirm();
		if ($this->getRequest()->isPost()){
			if ($form->isValid($this->getRequest()->getParams())){
				//Форма отправлена:
				$user->is_admin=$this->getRequest()->getParam('admin');
				$user->save();
				$this->view->user=$user;
				return $this->_helper->redirector(array('admin'=>null));
			}
		}		
		//Выводим форму смены роли:
		$form->setAction($this->view->url(array('admin'=>1-$user->is_admin)));
		$this->view->user=$user;
		$this->view->form=$form;
		
	}
	/**
	 * (non-PHPdoc)
	 * @see Op/Op_Controller#getTable()
	 */
	function getTable(){
		return 'Facebook_User'; 
	}
}
?>