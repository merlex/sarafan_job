<?php
include_once 'Op/Controller.php';
include_once 'Op/Form.php';
/**
 *
 * Прототип:
 * @author 1
 *
 */
class PrototypeController extends Op_Controller {
	function indexAction(){
		$request=$this->getRequest();
		$this->view->mode=$request->getParam('mode','ad');
		$this->view->auth=$request->getParam('auth','no');
		$this->view->is_post=$request->getParam('is_post');
			
	}
	
	protected $categories=array();
	protected $rnd=array();
	
	function randomAction(){
		$form= new Op_Form();
		$form->addTextInput('users_count','Количество пользователей');
		$form->addTextInput('one_probability','Вероятность одной категории');
		$form->addTextInput('two_probability','Вероятность двух категорий');
		$form->addTextInput('three_probability','Вероятность трех категорий');
		$this->view->form=$form;
		//
		$cm= new Application_Model_CategoryMapper();
		$this->categories=array_keys($cm->fetchAll());
		
		
		if ($this->getRequest()->isPost()){
			if ($form->isValid($this->getRequest()->getPost())){
				$values=$form->getValues();
				$probability=array();
				$probability[2]=$values['one_probability'];
				$probability[3]=$probability[2]+$values['two_probability'];
				$probability[1]=0;
				$maxrand=$probability[3]+$values['three_probability'];			
				//Получаем значение rnd:
				$um= new Application_Model_Facebook_UserMapper();
				foreach ($um->getListSelect()->columns(array('r'=>'MAX(rnd)'))->query() as $r){
					$rndID=$r['r']+1;
				}		
				
				for ($i=0; $i<$values['users_count'];$i++){
					$rnd=rand(0,$maxrand);
					
					if ($rnd>$probability[3]) $count=3;
					elseif ($rnd>$probability[2]) $count=2;
					else $count=1;
					
					$users[]=$this->createUser($i,$count,$rndID);
				}
				$this->view->users=$users;
			}
		}
	}
	
	function createUser($i, $count,$rndID){
		$categories=array();
		while (count($categories)<$count){
			$catID=$this->categories[rand(0, count($this->categories)-1)];		
			if (!in_array($catID, $categories)) $categories[]=$catID;			
		}	
		$user= new Application_Model_Facebook_User();
		$user->setValue('rnd',$rndID);
		$user->setValue('name','user_'.$rndID.'_'.$i);
		$user->setValue('post_period',60*60*24*7);
		$user->save();
		$user->setCategories($categories);
		return array(
			'name'=>'user_'.$i,
			'count'=>$count,
			'categories'=>implode(',',$categories)
		);
		
	}
	
	function debugAction(){
		$p= new Application_Model_Publisher('aa','bb');
		$p->execute();
	}
}
?>