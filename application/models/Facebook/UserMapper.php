<?php
include_once 'Op/Mapper.php';
include_once dirname(__FILE__).'/../DbTable/Facebook/User.php';
/**
 * 
 * @author atukmanov
 *
 */
class Application_Model_Facebook_UserMapper extends Op_Mapper {
	var $table='facebook_user';
	/**
	 * Получить системного по id пользователя:
	 * @param $user
	 * @return Application_Model_Channel
	 */
	function loadUserByForeignID($foreign_id){		
		$sel=$this->getDbTable()->getAdapter()->select()->from($this->table,'*')->where('foreign_id=?',$foreign_id);		
		$ret=$this->fetchObject($sel->query());						
		return $ret;
	}
	/**
	 * Активные пользователи:
	 * @return boolean
	 */
	function selectActive(){
		//Все пользователи, у которых пришло время публикации:
		$sel=$this->getListSelect()->
			where('post_ts<?',time())->
			where('post_period>0');		
		return $this->fetchList($sel->query());
		
	}
			
	function getOrderBy(){
		return 'name ASC';
	}
}
?>