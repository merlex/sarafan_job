<?php
/**
 * Аутентификация:
 * @author 1
 *
 */
class Op_Auth {
	
	const roleRead=0;
	const rolePost=1;
	
	protected $_providers=array();
	protected $_name=null;
	/**
	 * Создать
	 * @param array $providers массив провайдеров аутентификации
	 * @param $name
	 * @return boolean
	 */
	function __construct($providers, $name){
		$this->_providers=$providers;
		$this->_name=$name;
	}
	/**
	 * Получить пользователя:
	 * @return boolean
	 */
	function getUser($request){
		
	}
}
?>