<?php

/**
 * Класс для работы с facebook:
 * @author atukmanov
 *
 */
class FacebookController extends Op_Controller{	
	/**
	 * Главная страница:
	 * @return boolean
	 */
	function indexAction(){
		$this->view->is_main=true;
		$this->initAuth(null);
		$this->loadAd(0, 5);
		//$this->loadNav();
		$this->view->authURL=$this->getManageURL();
	}
	/**
	 * URL в приложении
	 * @param string $action
	 * @param array $params
	 * @return string
	 */
	function buildFaceboolURL($action, $params=null){
		$ret=$this->canvasURL.$action;
		$q=array();
		if ($params){
			foreach ($params as $k=>$v){
				if (preg_match('@^[a-z]+$@',$k) && preg_match('@^[a-z0-9]+$@',$v)){
					$ret.='/'.$k.'/'.$v;
				}
				else {
					$q[$k]=$v;
				}		
			}
			if (count($q)) $ret.='?'.http_build_query($q);
		}		
		return $ret;
	}
	/**
	 * Статистика:
	 * @return unknown_type
	 */
	function statAction(){
		if (!$this->initAuth($this->view->url())){
			$this->view->authURL=$this->buildAuthURL($this->buildFaceboolURL('state'),self::writeScope);		
			return $this->render('403');
		}
		if ($id=$this->getRequest()->getParam('id')){
			return $this->displayPostStat($id);
		}
		else {
			//Мои посты:
			$pm= new Application_Model_PostMapper();
			$this->paginatedList($pm->getListSelect()->where('user_id=?',$this->user->getID())->join(array('a'=>'ad'),'a.id='.$pm->table.'.ad_id',array('title','is_active')),$pm);
		}
	}
	/**
	 * Статистика по посту:
	 * @param $postID
	 * @return boolean
	 */
	function displayPostStat($id){
		$pm=new Application_Model_PostMapper();
		if ($post=$pm->find($id)){
			if ($post->user_id==$this->user->getID()){
				//Статистика по посту:
				$this->view->post=$post;
				return $this->render('postStat');
			}
			else {
				//Чужой пост:
				return $this->render404('Stat post not found');
			}	
			
		}
		else {
			return $this->render404('Stat post not found');
		}
	}
	/**
	 * Список объявлений:
	 * @return void
	 */
	function categoryAction(){
		if ($catID=$this->getRequest()->getParam('id',0)){
			$cm= new Application_Model_CategoryMapper();
			if (!$this->view->cat=$cm->find($catID)){
				//Неверная категория:
				return $this->render404('Category not found');
			}
		}
		//Получаем объявления:
		$am= new Application_Model_AdMapper();
		//Выбираем активные записи:
		$sel=$am->getActiveSelect();
		//Для данной категории:
		if ($catID) $sel->where('category_id=?',$catID);
		//С постраничным выводом:
		$this->paginatedList($sel,$am);		
	}
	
	function isReadable($ad){
		//Опубликованная запись:
		if ($ad->is_active) return true;
		//Админ:
		if ($this->user && $this->user->is_admin) return true;
		//Нет:
		return false;
	}
	/**
	 * Текущий URL:
	 * @var string
	 */
	protected $appUrl;
	/**
	 * Вывод объявления
	 * @return void
	 */
	function adAction(){
		$mapper= new Application_Model_AdMapper();
							
		if ($ad=$mapper->find($this->getRequest()->getParam('id'))){
			//Инициируем запись:
			$this->appUrl=$this->buildFaceboolURL('ad',array('id'=>$ad->id));
			$this->initAuth($this->appUrl);
			if ($this->isReadable($ad)){				
				$this->displayAd($ad, $this->appUrl);
			}
			else {
				return $this->displayDisabled($ad);
			}
			
		}
		else {			
			return $this->render404('Ad not found');
		}	
	}
	/**
	 * Добавить пост себе:
	 * @return boolean
	 */
	function updateAction(){	
		$mapper= new Application_Model_AdMapper();
		$ad=$mapper->find($this->getRequest()->getParam('id'));
		if (!$ad){
			//Объявление не найдено:
			return $this->render404('Ad not found');
		}
		if (!$ad->is_active){
			//Объявдение не активно:
			return $this->displayDisabled($ad);
		}
		$this->view->ad=$ad;
		//Проверяем права на запись:
		if (!$this->canPost($this->buildFaceboolURL('update',array('id'=>$ad->id)))){
			//Нет прав на запись:
			return $this->render('update403');
		}
		//Проверяем был ли уже пост:
		if (Application_Model_Post::loadUserAd($this->user, $ad)){
			$this->view->statURL=$this->getURL('stat','id',$ad->id);
			return $this->render('justUpdated');
		}
		//Выполняем добавление:
		return $this->executeUpdate($ad);
	}
	/**
	 * Добавления поста на стену:
	 */
	function executeUpdate($ad){
		$form=$this->getPostForm($ad);
		if ($this->getRequest()->isPost()){
			if ($form->isValid($this->getRequest()->getPost())){
				//Форма валидна:							
				$this->user->setValue('message',$form->getValue('customText'));
				//Добаваляем запись:
				if ($post= Application_Model_Post::create(
					$ad, 		//объявление
					$this->user, //постер
					$this->buildFaceboolURL('post').'/id/{id}'//Шаблон URL
				)){
				 	//Все пучком:
					$this->view->post=$post;
					$this->view->manageURL=$this->getURL('manage');							
					return $this->render('updated');
				}
				else {
					return $this->render('updateError');
				}
			}
		}
		$this->view->postForm=$form;
		return $this->render('repost');
	}
		
	/**
	 * Можно ли постить:
	 * @return boolean
	 */
	function canPost($url){
		if ($this->initAuth($url)){
			if ($this->user->is_post){
				return true;
			}
		}
		return false;
	}
	/**
	 * Вывод поста:
	 * @return boolean
	 */
	function postAction(){
		$pm= new Application_Model_PostMapper();
		if ($post=$pm->find($this->getRequest()->getParam('id'))){
			
			$url=$this->buildFaceboolURL('post',array('id'=>$post->id));
			
			if ($ad=$post->ad){
				if ($ad->is_active){
					$this->initAuth($url);
					$this->logPostView($post);
					$this->view->post=$post;					
					return $this->displayAd($ad, $url);
				}
				else {
					return $this->displayDisabled($ad);
				}
			}			
		}
		else {
			return $this->render404('Post not found');			
		}
	}
	/**
	 * 404 ошибка
	 * @param $error
	 * @return void
	 */
	function render404($error){
		$this->view->error=$error;
		return $this->render('404');
	}
	/**
	 * Отобразить объявление:
	 * @param $ad
	 * @param $returnURL
	 * @return mixed
	 */
	function displayAd($ad, $callbackURL){
		//Запускаем авторизацию:
		$this->initAuth($callbackURL);
		
		if ($this->user){
			//Пользователь авторизован:
			//Записыаем посещение в лог
			$this->logAdView($ad);			
			
			if ($this->user->is_post){
				$this->view->auth=self::authWrite;	
				//Может добавлять посты, выводим форму/статистику:
				$this->prepareRepostInterface($ad);								
			}
			else {			
				//Может только читать:
				$this->view->auth=self::authRead;
				//Подготавливаем ссыслку:
				$this->prepareAuthPostLink($callbackURL);
			}
			
		}
		else {
			//Пользователь не авторизован:
			$this->view->auth=self::authNo;
			$this->prepareAuthReadLink($callbackURL);
		}
		$this->view->ad=$ad;
		$this->view->user=$this->user;
		return $this->render('ad');
	}
	
	const authNo='no';
	const authRead='read';
	const authWrite='write';
	/**
	 * Подготоымть интерфейс "репоста"
	 * @param $ad
	 * @return viOd
	 */
	protected function prepareRepostInterface($ad){
		$thus->view->auth=self::authWrite;
		if ($this->view->myPost=Application_Model_Post::loadUserAd($this->user, $ad)){
			//У пользователя уже опубликован пост:
			$this->preparePostStat($this->view->myPost);			
		}
		else {
			return $this->view->postForm= $this->getPostForm($ad);
		}
	}
	/**
	 * Подготовить форму для поста:
	 * @return Zend_Form
	 */
	protected function getPostForm($ad){
		//Пользоваетель может добавить форму:
		$postForm= new Op_Form();		
		$postForm->setAction($this->getURL('update','id',$ad->getID()));
		//Поле "ваш текст":
		$postForm->addTextareaInput('customText',$this->view->translate('Your custom text'),140,false);		
		//Записали:
		return $postForm;			
	}
	/**
	 * Сгенерировать ссылку для прав на запись:
	 * @return boolean
	 */
	protected function prepareAuthPostLink($callbackURL){
		$this->view->AuthPostURL=$this->buildAuthURL($callbackURL,self::writeScope);
	}
	/**
	 * Сгененрировать ссылку для прав на чтение
	 * @param $callbackURL
	 * @return unknown_type
	 */
	function prepareAuthReadLink($callbackURL){
		$this->view->AuthReadURL=$this->buildAuthURL($callbackURL);
	}
	/**
	 * Записать просмотр объявления:
	 * @param $ad
	 * @return boolean
	 */
	protected function logAdView($ad){		
		Application_Model_Ad_Visit::factory($ad, $this->user)->save();
	}
	/**
	 * Записать просмотр поста:
	 * @param $post
	 * @return boolean
	 */
	function logPostView($post){
		
		if ($this->user && $post->user_id!=$this->user->getID()){
			//Свои посты не логируем
			Application_Model_Post_Visit::factory($post,$this->user)->save();
		}
	}
	/**
	 * Подготавливаенм статистику по посту:
	 * @return void
	 */
	function preparePostStat(){
		
	}
	/**
	 * Неактивное объявление:
	 * @param $ad
	 * @return void
	 */
	function displayDisabled($ad){
		return $this->render('disabled');
	}
	/**
	 * Управление трансляцией:	 
	 */
	function manageAction(){
		if (!$this->initAuth($this->buildFaceboolURL('manage'))){
			//Отправляем на авторизацию:
			$this->view->authURL=$this->buildAuthURL($this->buildFaceboolURL('manage'),self::writeScope);			
			return $this->render('redirect');
		}		
		$request=$this->getRequest();
		$form= new Application_Form_Channel();
		if ($request->isPost()){
			if ($form->isValid($request->getPost())){
				$this->user->setChannelData($form->getValues());
				return $this->render('saved');
			}
		}
		else {
			$form->populate($this->user->getChannelData());
		}
		$this->view->form=$form;	
	}
	/**
	 * Загрузить рекламу:
	 * @param int $categoryID
	 * @param int $limit
	 * @return unknown_type
	 */
	function loadAd($categoryID=0, $limit=0){
		$mapper= new Application_Model_AdMapper();
		$sel= $mapper->getActiveSelect();
		if ($categoryID) $sel->where('cat_id=?',$categoryID);
		if ($limit) $sel->limit($limit);
		$this->view->ad=$mapper->fetchList($sel->query());
	}
	/**
	 * Загрузка навигации
	 * @return void
	 */
	function loadNav(){
		$mapper= new Application_Model_AdMapper();
		$this->view->cat=$mapper->selectCategories();
	}
	
	const writeScope='offline_access,publish_stream';
	/***************************************************************
	 * Аутентификация:
	 */
	function getManageURL(){
		 return $this->buildAuthURL($this->buildFaceboolURL('manage'),self::writeScope);
	}
	/**
	 * Данные:
	 * @return void
	 */	
	function buildAuthURL($callbackURL, $scope=''){
		$connect= new Facebook_Connect($this->appID, $this->appSecret);
		return $connect->buildAuthURL($callbackURL, $scope);
	}
	
	const authRequest=1;
	const authSession=2;
	/**
	 * 
	 * @var Application_Model_Facebook_User
	 */
	protected $user;
	/**
	 * Аутентификация
	 * @param string $url адрес возврата
	 * @return boolean
	 */
	function initAuth($url=null){		
		if ($this->user) return true;
		//Стартуем сессию:
		$session= new Zend_Session_Namespace('facebook');
		$request= $this->getRequest();
		//Авторизация по коду:
		$facebook_connect= new Facebook_Connect($this->appID, $this->appSecret);
		$session= new Zend_Session_Namespace('facebook');
		if ($facebook_connect->parseAuthRequest($this->getRequest(),$url)){
			if ($this->user=Application_Model_Facebook_User::fromConnect($facebook_connect)){
				$session->user=serialize($this->user);
				$this->view->user=$this->user; 
				return self::authRequest;
			}
			else {
				return false;
			}
		}
		//Авторизация по сессии:
		elseif ($session->user){
			$this->view->user=$this->user=unserialize($session->user);
			return self::authSession;
		}
		else {
			return false;
		}
	}
	
	function exitAction(){
		$session= new Zend_Session_Namespace('facebook');
		$session->unsetAll();
		
	}
	
	function init(){
		$this->view->indexURL=$this->getURL();
		
		$this->initAppConfig();
		$this->initLayout();							
	}
	/***************************************************************
	 * Конфигурационные параметры:
	 * 	 
	 */
	protected $appID=null;
	protected $apiKey=null;
	protected $appSecret=null;
	protected $canvasURL=null;
	/**
	 * Применить:
	 * @var Facebook_Sign
	 */
	protected $fbSignedRequest=null;
	/**
	 * Инициация конфиг
	 * @return unknown_type
	 */
	protected function initAppConfig(){
		$cfg=Zend_Registry::get('config');
	
		$this->view->appID=$this->appID=$cfg->production->facebook->appID;		
		$this->apiKey=$cfg->production->facebook->apiKey;
		$this->appSecret=$cfg->production->facebook->appSecret;
		$this->canvasURL=$cfg->production->facebook->canvasURL;
	}
	/**
	 * 
	 * @return unknown_type
	 */
	protected function initLayout(){
		$this->_helper->layout->setLayout('facebook');
	}
}
?>