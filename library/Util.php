<?php
class Util {
	/**
     * Вывод отладки
     * @return unknown_type
     */    
    static function p(){
    	$this->_debugID++;
    	$a= func_get_args();
    	echo '<div style="border:1px solid red; padding:10px 5px;">';
    	$i=0;
    	foreach ($a as $arg){
    		if ($i) echo '<hr/>';
    		if ($arg==false) echo '<code>false</code>';
    		elseif ($arg==null) echo '<code>null</code>';
    		if (is_string($arg)) highlight_string($arg);
    		elseif (is_numeric($arg)) echo $arg;
    		else highlight_string(print_r($arg,true));
    		$i++;
    	}
    	$dbt=debug_backtrace(false);
    	//print_r($dbt);
    	echo '<div style="background:#CCCCCC; font-size:10px;">';
    		$line=$dbt[0]['line'];
    		$count=count($dbt);
    		for ($i=1; $i<count($dbt); $i++){
    			echo $dbt[$i]['class'],'::',$dbt[$i]['function'],' <b>',$line,'</b><br/>';
    			$line=$dbt[$i]['line'];
    			if ($i==5){
    				$more=$count-6;   			
    				echo '<a href="#dbt',$this->_debugID,'" onclick="this.style.display=\'none\'; document.getElementById(\'dbt',$this->_debugID,'\').style.display=\'block\'">+',$more,'</a><div id="dbt',$this->_debugID,'" style="display:none;">';
    			}
    		}
    		if (count($dbt)>5) echo '</div>';
    	echo '</div>';
    	echo '</div>';    	
    } 
}
?>