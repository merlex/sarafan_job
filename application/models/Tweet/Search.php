<?php
class Application_Model_Tweet_Search {
	protected $_query;
	/**
	 * Создать:
	 * @return 
	 */
	function __construct($query){
		$this->_query=$query;
	}
	/**
	 * Последнее обновление:
	 * @var int
	 */
	protected $_sinceID=null;
	/**
	 * Последнее изменение:
	 * @param $ts
	 * @return void
	 */
	function sinceID($id){
		$this->_sinceID=$id;
	}
	/**
	 * API URL
	 * @var string
	 */
	var $url='http://search.twitter.com/search.json';
	
	var $maxPage=15;
	/**
	 * Список результатов:
	 * @return array
	 */
	function fetchList(){
		for ($i=1; $i<=$this->maxPage; $i++){
			if (!$this->fetchPage($i)){
				Op_Util::p($i,'break');
				break;
			}
		}
		return $this->results;
	}
	
	protected $results=array();
	
	protected $_rpp=100;
	
	protected function fetchPage($page){
		$q= new Zend_Http_Client($this->url);
		$q->setParameterGet('q',$this->_query);
		$q->setParameterGet('page',$page);
		$q->setParameterGet('rpp',$this->_rpp);
		if ($this->_sinceID){
			$q->setParameterGet('since_id',$this->_sinceID);
		}		
		$resp=json_decode($q->request(Zend_Http_Client::GET)->getBody());
		if ($resp->results){
			
			foreach ($resp->results as $tweet){
				if (!count($this->results)) Op_Util::p($tweet);
				$this->results[]=$tweet;
			}
			return (count($resp->results)<$this->_rpp)?false:true;
		}
		else {
			return 0;
		}
	}
}
?>