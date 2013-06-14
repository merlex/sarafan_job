<?php
/**
 * Форма управления:
 * @author 1
 *
 */
class Application_Form_Channel extends Op_Form {
	
	function init(){
		
		$this->addTextareaInput('message','Добавлять к объявлению текст',255,false);
		
		//Список вариантов:
		$cat_checkboxes= new Zend_Form_Element_MultiCheckbox('cat');			
			$cat_checkboxes->setLabel('Категории');
			$cat_mapper= new Application_Model_CategoryMapper();
			$cat_checkboxes->setMultiOptions(Op_Util::ArrayReindex($cat_mapper->fetchAll(), 'id','title'));
			$cat_checkboxes->setRequired(true);
			$this->addElement($cat_checkboxes);
		parent::init();
	}
}
?>