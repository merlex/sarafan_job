<?php
class Op_Iterator extends Op_Model implements Iterator, Countable{
	protected $res=array();
	/**
	 * ctor:
	 * @param ресурс для итерации $res
	 * @param данные $info
	 * @return void
	 */
	function __construct($res, $info){
		$this->res=$res;
		parent::__construct($info);
	}
	/************ Iterator **********/
	public function current(){
		return current($this->res);
	}	
	public function key(){
		return key($this->res);
	}
	public function next(){
		return next($this->res);
	}
	public function rewind(){
		return reset($this->res);
	}
	public function valid(){
		return valid($this->res);
	}
	/********** Coutable ***********/
	public function count(){
		return count($this->res);
	}
	/********** CallEach ***********/	
	function __call($method, $arguments){		
		
		foreach ($this->res as $key=>$obj){
			if (is_object($obj) && method_exists($obj, $method)){
				//Use only first argument:
				$ret[$key]= $this->$method($arguments[0]);
			}
		}
	}
}
?>