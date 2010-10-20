<?php



class HTMLTranslator {
  /**
   * Constructor
   * @param $sourceLang SourceLanguage
   */
  public function __construct() {
  }
  
  public function HTMLTranslate($func, $param, $order, $sourceLang, $targetLang, $source, $resultFunc, $statusFunc) {
	$unTag=$this->removeTags($source);
	$param[$order[2]]=$unTag;
	
	$result=$this->doTranslate($func, $param, $sourceLang, $targetLang, $unTag, $resultFunc, $statusFunc);
	 
	return $result;
  }
  
  private function removeTags($source) {
  	return preg_replace('/<[^<]+?>/', '', $source);
  }
  
  /**
   * Translate body
   */
  private function doTranslate($func, $param, $sourceLang, $targetLang, $source, $resultFunc, $statusFunc) {
	if($resultFunc==null) {
  	  return call_user_func_array($func, $param);
	}else {
	  $response=call_user_func_array($func, $param);
	  
	  $status=call_user_func_array($statusFunc, array($response));
	  if($status == 'OK') {
	  	return call_user_func_array($resultFunc, array($response));
	  }else {
	  	return null;
	  }
	}
  }   
}
