<?php
include_once 'Op/Mapper.php';
/**
 * Маппер голосований:
 * @author 1
 *
 */
class Application_Model_Tweet_PollMapper extends Op_Mapper{
	var $table='tweet_poll';
	/**
	 * Загрузить активное голосование:
	 * @param string $tag хэш тег голосования
	 * @return Application_Model_Tweet_Poll
	 */
	function loadNonFinishedPoll($tag){
		$sel=$this->getListSelect()
			->where('end_ts=0')		//Активное
			->where('tag=?',$tag);	//С данным тегом
		return $this->fetchObject($sel->query());
	}
	/**
	 * Список активных голосований:
	 * @return array of Appl
	 */
	function getActivePolls(){
		$sel=$this->getListSelect()
			->where('end_ts=0');
		return $this->fetchList($sel->query());			
	}
}
?>