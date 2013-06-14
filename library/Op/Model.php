<?php
/**
 * Базовый класс модели с поддержкой ArrayAccess
 * @author 1
 *
 */
class Op_Model implements ArrayAccess {
	/**
	 * Данные:
	 * @var array
	 */
	protected $_info;
	protected $_scheme=array('id');
	/**
	 * Конструктор
	 * @param $info
	 * @return Op_Model
	 */
	function __construct($info=null){
		$this->setInfo($info);
		$this->_changedKeys=array();
	}
	/**
	 * Задать информацию:
	 * @param $info
	 * @return boolean
	 */
	function setInfo($info){
		if ($info){			
			foreach ($info as $k=>$v){
				$this->setValue($k, $v);
			}
		}
	}	
	/**
	 * Получить данные:
	 * @return boolean
	 */
	function getInfo(){
		return $this->_info;
	}
	
	protected $_changedKeys=null;
	protected function markChanged($key){
		if ($this->_changedKeys===null) return;//ctor
		else $this->_changedKeys[$key]=$key;
		return $this;	
	}
	/**
	 * Пометить как сохраненное:
	 * @param $id
	 * @return int
	 */
	function saved($id){
		//Записываем id:
		$this->_info['id']=$id;
		//Сбрасываем сохранение:
		$this->_changedKeys=array();
		return $id;
	}
	
	function getChanged(){
		//Данные для нового объекта:
		if (!$this->getID()) $this->initNew();
		$ret=array();		
		foreach ($this->_changedKeys as $k){
			$ret[$k]=$this->getValue($k);
		}
		return $ret;
	}	
	/**
	 * Предзагрузчик:
	 * @return boolean
	 */
	function initNew(){
		
	}
	/**
	 * Кэш:
	 * @var array
	 */
	protected $_cache=array();
	/**
	 * Зброс кэша:
	 * @return void
	 */
	function dropCache(){
		return $this->_cache=array();
	}
	
	function getValue($key){
		$get='get'.$key;
		$load='load'.$key;		
		if (method_exists($this,$get)){
			//Метод получения значения:
			return $this->$get();
		}
		elseif (method_exists($this,$load)){
			//Метод загрузки значения (кэшируется):
			if (isset($this->_cache[$key])){
				return $this->_cache[$key];	
			}
			else {
				return $this->_cache[$key]=$this->$load();
			}
		}
		elseif (isset($this->_info[$key])){
			return $this->_info[$key];
		}
		else {
			return null;
		}
	}
	
	function setValue($key, $value){
		$method='set'.$key;
		if (method_exists($this,$method)){
			$this->$method($value);
		}
		else{
			$this->_info[$key]=$value;
		}
		return $this->markChanged($key);
	}
	
	function unsetValue($key){
		$method='unset'.$key;
		if (method_exists($this,$method)){
			return $this->$method($key);
		}
		else{
			unset($this->_info[$key]);
		}
	}
	
	function offsetExists($offset){
		$method='isset'.$offset;
		if (method_exists($this, $method)){
			return $this->$mehod();
		}
		if (isset($this->info[$offset])){
			return true;
		}
		else {
			return false;
		}
	}
	
	function offsetGet($offset){
		return $this->getValue($offset);
	}
	
	function offsetSet($offset, $value){
		return $this->setValue($offset,$value);
	}
	
	function offsetUnset($offset){
		return $this->unsetValue($offset);
	}
	
	public function __get($key){
		return $this->getValue($key);
	}
	
	public function __set($key, $value){
		return $this->setValue($key, $value);
	}
	
	public function getID(){
		return isset($this->_info['id'])?$this->_info['id']:null;
	}
	
	public function setID($id){
		$this->_info['id']=$id;
	}
	/**
	 * Получить маппер:
	 * @return Op_Mapper
	 */
	public function getMapper(){
		$class=get_class($this);
		$class.='Mapper';
		return new $class();
	}
	
	public function save(){		
		$this->getMapper()->save($this);
	}
}
?>