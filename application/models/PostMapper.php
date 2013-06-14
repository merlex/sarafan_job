<?php
/**
 * Управление постами:
 * @author atukmanov
 *
 */
class Application_Model_PostMapper extends Op_Mapper{
	var $table='post';
	
	var $listFields=array(
		'id','user_id','ad_id','foreign_id','ts'
	);
	
	function getOrderBy(){
		return 'id ASC';
	}
	/**
	 * Получить пост по пользователю и объявлению:
	 * @param Application_Model_Facebook_User $user
	 * @param Application_Model_Ad $ad
	 * @return Application_Model_Post
	 */
	function loadUserAd($user, $ad){
		$sel= $this->getListSelect()
			->where('user_id=?',$user->getID())
			->where('ad_id=?',$ad->getID());		
		$ret= $this->fetchObject($sel->query());	
		return $ret;
	}
}
?>