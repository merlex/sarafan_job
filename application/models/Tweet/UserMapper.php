<?php
/**
 * Маппер пользователей:
 * @author 1
 *
 */
class Application_Model_Tweet_UserMapper extends Op_Mapper {
	public $table='tweet_user';
	/**
	 * Получить по twitter_id
	 * @param $id_str
	 * @return Application_Model_Tweet_User 
	 */
	function loadByTwitterUser($id_str){
		
		return $this->fetchObject(
			$this->getListSelect()
			->where('uid=?',$id_str)
			->query()
		);
	}
}
?>