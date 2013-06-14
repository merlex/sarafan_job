<?php
include_once 'Op/Mapper.php';
/**
 * Котегории
 * @author atukmanov
 *
 */
class Application_Model_AdMapper extends Op_Mapper {
	var $table='ad';
	
	var $listFields=array(
		'id','title','announce','create_ts','final_ts'
	);
	
	function getOrderBy(){
		return 'create_ts DESC';
	}
	/**
	 * 
	 * @param $offset
	 * @param $limit
	 * @return array
	 */
	function selectPastActive($limit=5){		
		return $this->getActiveSelect()->limit($limit)->query()->fetchAll();									
	}
	
	function getArchiveSelect(){
		return $this->getListSelect()->where('is_active=?',0)->where('final_ts > 0');
	}	
	/**
	 * Получить категории;
	 * @return boolean
	 */
	function selectCategories(){
		$mc= new Application_Model_CategoryMapper();
		$mc_sel=$mc->getListSelect();			
		$mc_sel->join('ad','category.id=ad.category_id',array('is_active'));
		$mc_sel->where('is_active=?',1);
		$mc_sel->group('id');
		$mc_sel->columns(array('id','title','total'=>'count(*)'));		
		return $mc->fetchList($mc_sel->query()->fetchAll());
	}
	/**
	 * 
	 * @return Zend_Db_Select
	 */
	function getActiveSelect(){
		return $this->getListSelect()->where('is_active=?',1);
	}
	/**
	 * Выборка по таблице
	 * @return Zend_Db_Select
	 */
	function getListSelect(){
		return parent::getListSelect()->joinLeft(array('c'=>'category'),'ad.category_id = c.id',array('category_title'=>'title'));		
	}
	
	function selectCategoryAd(){
		
	}
}
?>