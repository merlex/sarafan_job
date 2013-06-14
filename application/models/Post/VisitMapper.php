<?php
/**
 * Посещения:
 */
class Application_Model_Post_VisitMapper extends Op_Mapper {
	function getVisitsCount($post){
		$sel=$this->getListSelect()->columns(array('count(*) AS total'))->where('post_id=?',$this->objectID($post));		
		if ($obj=$this->fetchObject($sel->query())){		
			return $obj->total;
		}
		else {
			return 0;
		}
	}
	/**
	 * Уникальные пользователи:
	 * @param $post
	 * @return boolean
	 */
	function getUniqueCount($post){
		$sel=$this->getListSelect(self::skipOrder)->columns(array('count(DISTINCT user_id) AS total'))->where('post_id=?',$this->objectID($post));		
		if ($obj=$this->fetchObject($sel->query())){		
			return $obj->total;
		}
		else {
			return 0;
		}
	}
	/**
	 * Получить список пользователей:
	 * @return boolean
	 */
	function getVisitors($post){
		$users= new Application_Model_Facebook_UserMapper();
		$sel=$this->getListSelect(self::skipOrder)->where('post_id=?',$this->objectID($post))->group('user_id')->join(array('u'=>$users->table),'u.id='.$this->table.'.user_id');
		return $users->fetchList($sel->query(),'user_id');		
	}
	
	var $table='post_visit';
	
	function getOrderBy(){
		return 'ts DESC';
	}
}
?>