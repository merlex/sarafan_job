<?php
include_once 'Op/Form.php';
class Application_Form_Category extends Op_Form
{
	var $submitText='Сохранить';
		
    public function init()
    {    	
        // Set the method for the display form to POST
        $this->setMethod('post');

        // Add an email element
        $this->addElement('text', 'title', array(
            'label'      => 'Заголовок',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 100))
            )
        ));      
       	parent::init();
    }
}
