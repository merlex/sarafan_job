<?
include_once 'Op/Model.php';
/**
 * Категория:
 * @author 1
 *
 */
class Application_Model_Category extends Op_Model {
	/**
	 * Инициация нового объекта:
	 * @return void
	 */
	function initNew(){
		$table= new Application_Model_DbTable_Category();
		$rows=$table->fetchAll(null,'order_by DESC',1,0);
		if (count($rows)){
			foreach ($rows as $row) $this->setValue('order_by',$row->order_by+1);
		}
		else {
			$this->setValue('order_by',0);
		}
	}
}
?>