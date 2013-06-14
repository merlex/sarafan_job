<?php
include_once 'Op/Form.php';
/**
 * Форма подтверждения действия
 * @author 1
 *
 */
class Application_Form_Confirm extends Op_Form {
	/**
	 * 
	 * @return confirm form:
	 */
	const attrSubmitText='submitText';
	const attrCancelText='cancelText';
	const attrCancelURL='cancelURL';
	
	var $submitText='OK';
	var $cancelText='Отмена';
	var $cancelURL='/';
	
	var $tplName='confirm';
	/**
	 * 
	 * @return unknown_type
	 */
	function init(){
		parent::init();		
	}
	/**
	 * Текст для кнопки "отправить"
	 * @return boolean
	 */
	function setSubmitText($submitText){
		//Op_Util::p($this->submit);
		$this->submit->setLabel($submitText);
	}
	
	public $attr2Property=array(self::attrCancelText,self::attrSubmitText,self::attrCancelURL);	
}
?>