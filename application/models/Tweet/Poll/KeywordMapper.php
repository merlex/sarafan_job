<?php
/**
 * Ключевое слово для продвижения:
 * @author 1
 *
 */
class Application_Model_Tweet_Poll_KeywordMapper extends Op_Mapper {
	protected $table='tweet_poll_keyword';
	/**
	 * Загрузить по стартовому слову
	 * @return boolean
	 */
	function save($obj){
		if ($this->_loadByField('start_word',$obj->start_word)) throw new Op_Exception_DuplicateEntry()
		parent::save($obj);
	}
	
	function _loadByField($field, $value){
		return $this->fetchObject($this->getListSelect()->where($field.'=?',$value)->query());
	}
}
?>