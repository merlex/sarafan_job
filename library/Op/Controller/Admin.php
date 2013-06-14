<?php
include_once 'Op/Controller.php';
/**
 * Базовый контроллер адм интерфейса:
 * @author 1
 *
 */
class Op_Controller_Admin extends Op_Controller {

	function dispatch($action){
		if ($this->checkAuth()){			
			//Идем дальше:
			return parent::dispatch($action);	
		}
		else {
			//Обрабатываем отсутсвие доступа:
			return parent::dispatch('accessDenied'); 
		}
	}
	
	function accessDenied(){
		$this->_helper->layout->setLayout('message');
		return $this->render('403',null,true);
	}
	/**
	 * Запуск:
	 * @see Controller/Zend_Controller_Action#init()
	 */
	function init(){
		$this->_helper->layout->setLayout('admin');				
	}
		
	/**
	 * Проверка авторизации:
	 * @return boolean
	 */
	function checkAuth(){
		if ($this->initAuth()){
			if ($this->user->is_admin){
				return true;
			}
		}
		//Not allowed:
		return false;
	}
	/**
	 * Пользователь:
	 * @var Application_Model_Facebook_User
	 */
	protected $user=null;
	/**
	 * Инициация авторизации:
	 * @return boolean
	 */
	function initAuth(){
		//Facebook код:
		$cfg=Zend_Registry::get('config');			
		$connect= new Facebook_Connect(
			$cfg->production->facebook->appID,
			$cfg->production->facebook->appSecret
		);
		$url='http://'.$_SERVER['SERVER_NAME'].$this->view->url();
		
		$session= new Zend_Session_Namespace('admin');
		if ($session->user){
			//Из сессии:
			$this->user=unserialize($session->user);
			return true;
		}
		//Facebook аутентификация:
		if ($code=$this->getRequest()->getParam('code')){
			
			
			if ($connect->fetchAccessToken($code, $url)){
				//Токен удачен:
				$mapper= new Application_Model_Facebook_UserMapper();
				$this->user=$mapper->loadUserByForeignID($connect->loadUserInfo()->id);
				//Обновляем @todo вынести в модель:				
				$this->user->is_post=1;
				$this->user->access_token=$connect->getAccessToken();
				$this->user->save();
				$session->user=serialize($this->user); 
				//Все OK:
				return true;				
			}
		}
		$this->view->authURL=$connect->buildAuthURL($url, Facebook_Connect::writeScope);
		return false;		
	}
	/**
	 * Выход
	 * @return unknown_type
	 */
	function exitAction(){
		$session= new Zend_Session_Namespace('admin');
		$session->user=null;
		$this->render('exit',null,'IndexController');
	}
	/**
	 * Выводи списка:
	 * @return string
	 */
	function indexAction(){
				
		$this->prepareFilters();
		$sel=$this->getSelect();
		
		$paginator= new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($sel));
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$paginator->setItemCountPerPage($this->getLimit());
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_system/pages.phtml');
		$this->view->pages=$paginator;		
		$this->view->list=$this->getMapper()->fetchList($paginator);		
	}

	const add='add';
	const edit='edit';
	const delete='delete';
	/**
	 * Nod add, no edit, no delete, no career, no future
	 * @param $action
	 * @return boolean
	 */
	function checkActionPermissions($action){
		return true;
	}
	
	function getLimit(){
		return 20;
	}
	/**
	 * Подготовка фильтров:
	 */
	function prepareFilters(){}
	/**
	 * Получить выборку:
	 * @return Zend_Db_Select
	 */
	function getSelect(){
		return $this->getMapper()->getListSelect();	
	}
	/**
	 * Добавление:
	 * @return void
	 */
	function addAction(){
		if (!$this->checkActionPermissions(self::add)){
			return $this->render('403');
		}
		$form=$this->getForm();
		$request=$this->getRequest();
		if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $model=$this->getModel();
                $model->setInfo($form->getValues());
                $this->getMapper()->save($model);                
                return $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;	
	}
	/**
	 * Подтверждаемое действие:
	 */
	function _confirmAction(){
		$mapper=$this->getMapper();
		$request=$this->getRequest();
		if (!$obj=$mapper->find($request->getParam('id'))){
			throw new Op_Exception_NotFound($request->getParam('id'),$this->getTable());
		}
		$form= new Application_Form_Confirm();
		if ($request->isPost()){
			if ($form->isValid($request->getPost())){
				return $obj;
			}
		}
		$this->view->obj=$obj;
		$this->view->form=$form;
		return null;
	}	
	
	function redirectBack(){
		return $this->_helper->redirector('index');
	}
	/**
	 * Удаление
	 * @return boolean
	 */
	function editAction(){
		if (!$this->checkActionPermissions(self::edit)){
			return $this->render('403');
		}	
		
		$form=$this->getForm();
		$request=$this->getRequest();
		$mapper=$this->getMapper();
		if (!$obj=$mapper->find($request->getParam('id'))){			
			throw new Exception('404');
		}
		if ($request->isPost()){			
			if ($form->isValid($request->getPost())){				
				$obj->setInfo($form->getValues());
				$mapper->save($obj);
				return $this->redirectBack();
			}
		}
		else {
			
			$form->populate($obj->getInfo());
		}
		$this->view->obj=$obj;
		$this->view->form =$form;
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function deleteAction(){
		if (!$this->checkActionPermissions(self::delete)){
			return $this->render('403');
		}		
		
		if (!$obj=$this->getMapper()->find($this->getRequest()->getParam('id'))){
			throw new Exception('404');	
		}
		$this->view->obj=$obj;
		$request=$this->getRequest();
		$deleteLocks=$this->getDeleteLocks($obj);
		if ($deleteLocks){
			$this->view->deleteLocks=$deleteLocks;
			return;
		}
		$form= new Application_Form_Confirm(array(
			Application_Form_Confirm::attrSubmitText=>'Удалить',
			Application_Form_Confirm::attrCancelURL=>'Отмена',
			Application_Form_Confirm::attrCancelURL=>'../../',
		));	
		
		if ($request->isPost()){
			
			if ($form->isValid($request->getPost())){
				$this->getMapper()->delete($obj);
				return $this->redirectBack();
			}
			
		}
		$this->view->form=$form;	
			
	}
	/**
	 * Локи для удаления:
	 * @return array
	 */	
	protected function getDeleteLocks(){return null;}	
}
?>