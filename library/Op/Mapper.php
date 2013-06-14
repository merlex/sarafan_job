<?php
include_once 'Op/DbTable.php';
/**
 * Базовый маппер
 *
 */
abstract class Op_Mapper {
	
	var $table=null;
	protected $_dbTable;

	const skipOrder=1;
	
    /**
     * Выборка:
     * @return Zend_Db_Select
     */
    function getListSelect($mode=0){
    	$ret= $this->getDbTable()->getAdapter()->select()->from(mb_strtolower($this->table));
    	if ($this->listFields){
    		$ret->columns($this->listFields);				
    	}
    	if (!$mode&self::skipOrder && ($orderBy=$this->getOrderBy())){
    		$ret->order($orderBy);
    	}
    	return $ret;				
    }
	/**
	 * 
	 * @return Zend_Db_Table_Abstract
	 */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            return $this->_dbTable= Op_DbTable::factory($this->table);
        }
        return $this->_dbTable;
    }
	/**
	 * Сохранить:
	 * @param Op_Model $obj
	 * @return int
	 */
    public function save($obj)
    {
        if (null === ($id = $obj->getID())) {
     		$id= $this->getDbTable()->insert($obj->getChanged());     		
        } else {        	
            $this->getDbTable()->update($obj->getChanged(), array('id = ?' => $id));            
        }
        return $obj->saved($id);        
    }
	/**
	 * Получить модель:
	 * @param $id
	 * @return int
	 */
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        
        if (0 == count($result)) {
            return false;
        }
        $className=$this->getModelClass();
        return new $className($result->current());        
    }
    /**
     * 
     * @param Op_Model $obj
     * @return 
     */
    public function delete($obj){
    	$table=$this->getDbTable();    	
    	$table->delete($table->getAdapter()->quoteInto('id = ?',$this->objectID($obj)));
    }
    /**
     * 
     * @return int
     */
    protected function objectID($obj){
    	if (is_numeric($obj)){
    		return $obj;
    	}
    	if (is_object($obj)){
    		return $obj->getID();
    	}
    	if (is_array($obj)){
    		return $obj['id'];
    	}
    }    
    
    function getModelClass(){
    	return mb_substr(get_class($this),0,-strlen('Mapper'));
    }
        

    public function fetchAll()
    {
    	$t=$this->getDbTable();
    	return $this->fetchList($resultSet = $this->getDbTable()->fetchAll(null,$this->getOrderBy()));        
    }
        
    public function fetchList($resultSet, $primaryKey=null){
    	
        $entries   = array();
        $className=$this->getModelClass();
        foreach ($resultSet as $row) {
        	if ($primaryKey) $row['id']=$row[$primaryKey];
            $obj = new $className($row);
            $entries[$obj->id]=$obj;
        }
        return $entries;	
    }
    /**
     * Получить объект 
     * @param mixed $res
     * @return Op_Model
     */
    public function fetchObject($res){
    	$entries= $this->fetchList($res);
    	if (count($entries)){
    		foreach ($entries as $obj) return $obj;
    	}
    	return null;
    }
    
    function getOrderBy(){
    	return null;
    }   
}
?>