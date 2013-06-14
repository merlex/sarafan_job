<?
class Application_Model_Publisher {
	protected $appID;
	protected $appSecret;
	/**
	 * Публикатор:
	 * @param $appID
	 * @param $appSecret
	 * @return unknown_type
	 */
	function __construct($appID, $appSecret){
		$this->appID=$appID;
		$this->appSecret=$appSecret;
	}
	/**
	 * Выполнить:
	 * @return boolean
	 */
	function execute(){
		//1. Загружаем пользователей:
		if (!$this->loadUsers()){
			Op_Util::p('users?');
			//Пользователей нет:
			return;
		}
		if (!$this->loadAds()){
			//Объявлений нет:
			return;
		}
		Op_Util::p($this->userAds, $this->adRank);
		//Проходимся по пользователям и публикуем сообщения для тех пользователей у которых "без вариантов":
		foreach ($this->users as $userID=>$user){
			if (count($this->userAds[$userID])==1){
				//Без вариантов:
				//Op_Util::p('only one');
				$this->providePost($userID, $this->userAds[$userID][0]); 
			}
		}
		Op_Util::p('start iterate');
		//Итерируем:
		while (count($this->userAds)){
			//Публикуем пока есть доступные слоты:
			$this->iterate();
		}
	}
	
	protected $users=array();
	/**
	 * Загрузить пользователей:
	 * @return void
	 */
	protected function loadUsers(){
		$m= new Application_Model_Facebook_UserMapper();
		$this->users=$m->selectActive();
		
		if (!count($this->users)) return false;
		//Подгребаем категории:
		$m= new Application_Model_Facebook_ChannelMapper();
		foreach ($m->getUsersChannels($this->users) as $channel){
			$this->categoryUsers[$channel->category_id][]=$channel->facebook_user_id;
		}
		return true;					
	}
	/**
	 * Итерация публикации:
	 * @return bo
	 */
	function iterate(){
		$postAd=0;	
		foreach ($this->ads as $adID=>$ad){
		if (!$postAd){
				//Первая:
				$postAd=$adID;
			}
			elseif ($ad->posts< $this->ads[$postAd]->posts){
				//Менее значимая:
				$postAd=$adID;
			}
			elseif ($ad->posts== $this->ads[$postAd]->posts){				
				//Такая-же (берем с меньшим кол-вом постов):
				if ($this->adRank[$postAd]>$this->adRand[$adID]){
					$postAd=$adID;
				}
			}
		}	
		
		//Ищем наиболее "тухлого" пользователя:
		$postUser=0;
		foreach ($this->userAds as $userID=>$ads){
			if (in_array($postAd, $ads)){
				if (!$postUser){
					$postUser=$userID;
				}
				elseif (count($this->userAds[$postUser])>count($ads)){
					//Меньше объявлений- меньше привлекательность для дальнейших туров:
					$postUser=$userID;
				}
			}
		}
		
		$this->providePost($postUser, $postAd);
	}
	/**
	 * Выпустить пост:
	 * @param $userID
	 * @param $adID
	 * @return void
	 */
	function providePost($userID, $adID){
		Op_Util::p("post user={$userID} ad={$adID}");
		Application_Model_Post::create($this->ads[$adID],$this->users[$userID],'aaa');
		//Пересчитываем рейтинг:
		foreach ($this->userAds[$userID] as $adID){
			$this->adRank[$adID]-=1;
			if ($this->adRank[$adID]==0){
				//Нет доступных слотов:
				Op_Util::p("unset rank {$adID}");
				unset($this->adRank[$adID]);
			}			
		}
		unset($this->userAds[$userID]);
	}
	/**
	 * Каналы пользоваетелй:
	 * @var unknown_type
	 */
	protected $categoryUsers=array();
	/**
	 * Объявления:
	 * @var array
	 */
	protected $ads=array();
	/**
	 *  
	 * @var boolean
	 */
	protected $userAds=array();
	/**
	 * 
	 * @var array
	 */
	protected $adRank=array();
	/**
	 * Собрать объявления:
	 * @return void
	 */
	protected function loadAds(){
		$am= new Application_Model_AdMapper();
		$this->ads= $am->fetchAll($am->getActiveSelect()->query());
		if (!count($this->ads)) return false;
		//Получаем что уже было кем опубликовано:
		$justPosted=$this->preparePosted();
		//Вычисляем что-кому доступно для публикации:
		$total=0;		
		foreach ($this->ads as $ad){
			if (isset($this->categoryUsers[$ad->category_id])){
				
				foreach ($this->categoryUsers[$ad->category_id] as $userID){
					//Check if posted:
					if (!isset($justPosted[$ad->id]) || !in_array($userID, $justPosted[$ad->id])){
						//Записываем что пользователь может запостить:
						$this->userAds[$userID][]=$ad->getID();
						//Увеличиваем кол-во доступных слотов:
						$this->adRank[$ad->id]+=1;
						$total++;
					}
				}
			}
		}		
		return $total;
	}
	
	protected function preparePosted(){
		$mapper= new Application_Model_PostMapper();
		$sel=$mapper->getListSelect(Op_Mapper::skipOrder)
			->where('user_id IN ('.implode(',',array_keys($this->users)).')')
			->where('ad_id IN ('.implode(',',array_keys($this->ads)).')');
		
		
		foreach ($sel->query() as $obj){
			$ret[$obj['ad_id']][]=$obj['user_id'];
		}
		return $ret;
	}
}

?>