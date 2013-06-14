<?php
/**
 * 
 * @author atukmanov
 *
 */
class Application_Model_Facebook_ChannelMapper extends Op_Mapper {
	var $table='facebook_channel';
	
	function getOrderBy(){
		return 'id ASC';
	}
	
	var $listFields=array('id','facebook_user_id','category_id');
	/**
	 * Получить каналы:
	 * @param $userID
	 * @return boolean
	 */
	function getUserChannels($userID){
		return $this->fetchList($this->getListSelect()->where('facebook_user_id=?',$userID)->query());
	}	
	/**
	 * Каналы пользователей:
	 * @param array of Application_Model_Facebook_User $users
	 * @return array
	 */
	function getUsersChannels($users){
		if (!$users) return null;
		$ids=array();
		foreach ($users as $user){
			$ids[]=(int)$user->getID();
		}
		if (!count($ids)) return null;
		return $this->fetchList($this->getListSelect()->where('facebook_user_id IN ('.implode(',',$ids).')')->query());
	}
}
?>