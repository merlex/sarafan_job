<?php
include_once 'Op/Controller/Admin.php';

class PostController extends Op_Controller_Admin {
	function prepareFilters(){
		$request=$this->getRequest();
		if ($adID=$request->getParam('ad')){
			$adMapper= new Application_Model_AdMapper();
			$this->view->ad=$this->ad=$adMapper->find($adID);
		}
		
	}
	
	function getSelect(){
		$request=$this->getRequest();
		$mapper=$this->getMapper();
		$sel= parent::getSelect();
		$userMapper=new Application_Model_Facebook_UserMapper();
		if ($this->view->user=$userMapper->find($request->getParam('user',0))){
			$sel->where('user_id=?',$this->view->user->id);
		}
		else {
			$sel->join(array('user'=>$userMapper->table),'user.id='.$mapper->table.'.user_id',array('user_name'=>'name'));
		}
		$adMapper= new Application_Model_AdMapper();
		if ($this->view->ad=$adMapper->find($request->getParam('ad',0))){
			$sel->where('ad_id=?',$this->view->ad->id);
		}
		else {
			$sel->join(array('ad'=>$adMapper->table),'ad.id='.$mapper->table.'.ad_id',array('ad_title'=>'title'));
		}
		
		return $sel;
	}
}
?>