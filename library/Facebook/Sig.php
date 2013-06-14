<?php
include_once 'Op/Model.php';
include_once 'Op/Util.php';

class Facebook_Sig extends Op_Model{
	
	protected $appID=null;
	protected $appSecret=null;
	
	function __construct($appID, $appSecret){
		$this->appID=$appID;
		$this->appSecret=$appSecret;
	}
		
	const prefix='fb_sig_';
		
	/**
	 * Обработать запрос:
	 * @param Zend_Controller_Request_Http $request
	 * @return boolean
	 */
	function readRequest($request){
		
		if (!$sign=$request->get('fb_sig'))
		{
			return null;
		}
		
		$prefixLength=mb_strlen(self::prefix);
		$fbRequest=array();
		foreach ($request->getParams() as $k=>$v){
			if (mb_substr($k,0,$prefixLength)==self::prefix){
				$fbRequest[mb_substr($k,$prefixLength)]=$v;
			}
		}			
		ksort($fbRequest);
		
		$hash='';
		foreach ($fbRequest as $k=>$v){
			$hash.=$k.'='.$v;			
		}
		$hash.=$this->appSecret;
				
		if (md5($hash)==$sign){
			$this->info=$fbRequest;
			return true;	
		}
		else {
			return false;
		}
	}
}
?>