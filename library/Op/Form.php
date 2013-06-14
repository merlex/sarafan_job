<?php
class Op_Form extends Zend_Form {
	
	var $tplName='form';
	/**
	 * Стандартная форма:	 
	 * @see Zend_Form#init()
	 */
	function init(){
		$this->initAttr();
		$this->setMethod('post');
		//Hash поле:
		$hash= new Zend_Form_Element_Hash('hash');
		$hash->setDecorators(array(
			'ViewHelper',
			'Description',
			'Errors',		
		));
		$this->addElement($hash);		
		 // Add the submit button
		$submit= new Zend_Form_Element_Submit('submit',array('label'=>$this->submitText));				
		$submit->setDecorators(array(
			'ViewHelper',
	        'Description',	        
		));		
		$this->addElement($submit);
		
		$this->setDecorators(array(
			array('ViewScript',array('viewScript'=>'forms/'.$this->tplName.'.phtml'))
		));
	}
		
	var $attr2Property=array();
	/**
	 * Высталение аттрибутов:
	 * @return boolean
	 */
	protected function initAttr(){
		
		foreach ($this->attr2Property as $attr){
			if ($value=$this->getAttrib($attr)){
				$this->$attr=$value;
				$this->removeAttrib($attr);
			}
		}
	}
	/**
	 * 
	 * @param $name
	 * @param $required
	 * @param $maxlenghth
	 * @return mixe
	 */
	function addTextInput($name, $title, $maxlenghth=250, $required=true){
		$input= new Zend_Form_Element_Text($name);
		$input->setLabel($title)
		->setRequired($required);
		if ($maxlenghth){
			$input->setValidators(array(new Zend_Validate_StringLength(0,$maxlenghth)));
		}		
		$input->setFilters(array(new Zend_Filter_StringTrim(), new Zend_Filter_StripTags()));
		
		return $this->addElement($input);
	}
	/**
	 * Добавить текстовое поле:
	 * @param $name
	 * @param $title
	 * @param $required
	 * @return array
	 */
	function addTextareaInput($name, $title, $maxlength=0, $required=true, $editor=false){
		$input=new Zend_Form_Element_Textarea($name);
		$input->setLabel($title);
		$input->setRequired($required);
		$input->setFilters(array(new Zend_Filter_StringTrim()));
		//Проставляем данные для редактора:
		if ($editor){
			$input->setAttrib('editor',true);			
		}
		return $this->addElement($input);		
	}
	/**
	 * Clean stuff
	 * @see Zend_Form#getValues()
	 */
	function getValues($suppressArrayNotation = false){
		$ret=parent::getValues($suppressArrayNotation);		
		unset($ret['hash']);
		return $ret;
	}
}
?>