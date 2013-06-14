<?php
include_once 'Op/Form.php';
class Application_Form_Ad extends Op_Form {
	
	var $submitText='Сохранить';
	
	function init(){
		$this->addTextInput('title','Заголовок',250);
		//Выбор категории:
		$category =new Application_Model_CategoryMapper();
		$categoryList=Op_Util::ArrayReindex($category->fetchAll());
		
		$selectCategory= new Zend_Form_Element_Select('category_id');
		$selectCategory->setRequired(true);
		$selectCategory->setLabel('Категория');
		$selectCategory->setMultiOptions($categoryList);
		$recordExits= new Zend_Validate_Db_RecordExists('category','id');		
		$selectCategory->setValidators(array($recordExits));
		$this->addElement($selectCategory);
		
		$this->addTextInput('price','Зарплата',0,false);
		$this->addTextareaInput('announce','Анонс',0,false,false);
		$this->addTextareaInput('body','Текст',0,false,true);
		$this->addTextareaInput('contacts','Контакты',0,false,true);
		
		parent::init();	  
	}
	/**
	 * 
	 * @return unknown_type
	 */
	function indexAction(){
		$this->view->action='index';
	}
}
?>