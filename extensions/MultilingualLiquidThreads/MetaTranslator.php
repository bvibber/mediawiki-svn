<?php

require_once('HTMLTranslator.php');

class MetaTranslator {
  /* Translation parameters */
  private $sourceLang;

  /* in MetaTranslation process */
  private $noTranslations;
  private $tags_begin=array();
  private $tags_end=array();
  
  /* Constant */
  private $TAG_BEGIN="<notranslate>";
  private $TAG_END="</notranslate>";  
  private $TAG_RELAXED='__________';
  private $LANG_TAG_BEGIN='[';
  private $LANG_TAG_END=']';
  
  
  
  /**
   * Constructor
   * @param $sourceLang SourceLanguage
   */
  public function __construct($sourceLang) {
  	$this->setSourceLanguage($sourceLang);
  }
  
  /**
   *
   */
  public function metaTranslate($func, $param, $order, $meta=true, $attach=true, $resultFunc, $statusFunc) {
	$sourceLang=$param[$order[0]];
	$targetLang=$param[$order[1]];
	$source=$param[$order[2]];
	
	
	$encode=$this->preTranslation($source, $meta);
	
	$trans=$this->doTranslate($func, $param, $order, $sourceLang, $targetLang, $encode, $resultFunc, $statusFunc);
	
	if($trans==null) {
	  //error occured  
	  return null;
	}
	
	$newparam=$param;
    $newparam[$order[2]]=$trans;
  
	$decode=$this->postTranslation($func, $newparam, $order, $meta, $attach, $resultFunc, $statusFunc);
	
	return $decode;
  }
  
  /**
   * PreProcess
   * @param $source  Translated sentence
   * @param $meta  MetaTranslate or not
   * @return Encoded sentence
   */
  public function preTranslation($source, $meta=true) {
  	$this->noTranslations=array();
  	$escEncode='';
  	
	/* no-MetaTranslate */
  	if($meta) {
	  $this->tags_begin[]='[relax]';
	  $this->tags_begin[]=$this->TAG_BEGIN;
  
	  $this->tags_end[]='[/relax]';
	  $this->tags_end[]=$this->TAG_END;
	}
  	
	/* LanguageTags */
  	
	
  	/* Encoding */
  	$escEncode=$this->encodeEscapes_relaxed($source);
  	
  	return $escEncode;
  }
  
  
  /**
   * PostProcess
   * @param  $func  Translator 
   * @param  $param  Parameters for $func
   * @param  $order  Parameter position : $param[$order[0]]=SourceLanguage,  $param[$order[1]]=TargetLanguage, $param[$order[2]]=Source
   * @param  $meta  MetaTranslate or not
   * @param  $attach  Whether Description is necessary : Unnecessary->false
   * @param  $resultFunc  Such function that $resultFunc($func($param)) returns sentence
   * @return Sentence
   */
  public function postTranslation($func, $param, $order, $meta=true, $attach=true, $resultFunc=null, $statusFunc) {
  	$source=$param[$order[2]];
  	
  	/* attach Descriptions */
	if($meta) {
  	  $this->attachDescriptions($func, $param, $order, $meta, $attach, $resultFunc, $statusFunc);
	}
  	
  	/* Decode */
  	$result=$this->decodeEscapes($source);
  	
  	
  	return $result;
  }
  
  /**
   * Replacing Intermediate Code into Escapes
   * @param $source  Encoded sentence
   * @return Decoded sentence
   */
  private function decodeEscapes($source) {
  	for($i=0; $i<count($this->noTranslations); $i++) {
  	  $source=str_ireplace($this->noTranslations[$i]['code'],
  	                      $this->noTranslations[$i]['escape'], $source);
  	}
  	
  	return $source;
  }
  
  /**
   * Attaching Descriptions
   */
  private function attachDescriptions($func, $param, $order, $meta, $attach, $resultFunc, $statusFunc) {
    $sourceLang=$param[$order[0]];
    $targetLang=$param[$order[1]];
    
    
    /* for all noTranslations */
    for($i=0; $i<count($this->noTranslations); $i++) {
      if($attach) {
        /* Recursive MetaTranslation */
        $meta=new MetaTranslator($sourceLang);
        $encode=$meta->preTranslation($this->noTranslations[$i]['escape']);
        
        $trans=$this->doTranslate($func, $param, $order, $sourceLang, $targetLang, $encode, $resultFunc, $statusFunc);
        
        $newparam=$param;
        $newparam[$order[2]]=$trans;
        $description=$meta->postTranslation($func, $newparam, $order, true, true, $resultFunc, $statusFunc);
        
        /* attaching */
		if($this->canAttach($this->noTranslations[$i]['escape'], $description)) {
          $this->noTranslations[$i]['escape'].='('.$description.')';
		}
      }
      
      $tagnum=$this->noTranslations[$i]['tag'];
	  if($tagnum!=0) {
        $this->noTranslations[$i]['escape']=$this->tags_begin[$tagnum].
      	                                    $this->noTranslations[$i]['escape'].
                                            $this->tags_end[$tagnum];
	  }else {
        $this->noTranslations[$i]['escape']=$this->TAG_RELAXED.
      	                                    $this->noTranslations[$i]['escape'].
                                            $this->TAG_RELAXED;
      } 
    }
  }
  
  /**
   * Whether to Attach Descriptions
   */
  private function canAttach($escape, $description) {
  	/* punctuations */
	$pattern='[\.| |．|,|，|　|:|：|;|。|、|!|！|\?|？]';	
	$comp1=@preg_replace($pattern, '', $escape);
	$comp2=@preg_replace($pattern, '', $description);
	
	return $comp1!==$comp2;
  }
  
  /**
   * Translate body
   */
  private function doTranslate($func, $param, $order, $sourceLang, $targetLang, $source, $resultFunc, $statusFunc) {
  	$param[$order[0]]=$sourceLang;
  	$param[$order[1]]=$targetLang;
  	$param[$order[2]]=$source;
  	
	$ht=new HTMLTranslator();
	
	
	$result = $ht->HTMLTranslate($func, $param, $order, $sourceLang, $targetLang, $source, $resultFunc, $statusFunc);
	
	return $result;
  }
  
  /**
   * Replacing Escapes into Intermediate Code : relaxed mode
   */
  private function encodeEscapes_relaxed($source) {
  	$array=explode($this->TAG_RELAXED, $source);
	
  	$relaxed='';
	for($i=0; $i<count($array)-1; $i++) {
      $relaxed.=$array[$i];
	  
	  if($i%2==0) {
	  	$relaxed.=$this->tags_begin[0];
	  }else {
	  	$relaxed.=$this->tags_end[0];
	  }
	}
	$relaxed.=$array[count($array)-1];
  	
	return $this->encodeEscapes_strict($relaxed);
  }
  
  /**
   * Replacing Escapes into Intermediate Code : strict mode
   */
  private function encodeEscapes_strict($source) {
  	$encode='';
  	$escape='';
  	$indent=0;
  	$id=0;
	$tagStart=0;
	$tagEnd=0;
  	
  	$buf='';
  	for($i=0; $i<mb_strlen($source); $i++) {
  	  $buf.=mb_substr($source, $i, 1);
  	  
  	  /* escape begins */
	  $len=$this->startsWith($this->tags_begin, $buf);
	  if($len!=-1) {
	    /* before tag, do not escape */
    	if($indent==0) {
   	  	  $encode.=mb_substr($buf, 0, mb_strlen($buf)-$len);
		  $tagStart=$i;
   	    }else {
   	      $escape.=$buf;
   	    }
   	    
   	    $buf='';
   	  
        $indent++;
		continue;  	  
  	  }
  	  
  	  
  	  /* escape ends */
	  $len=$this->startsWith($this->tags_end, $buf, $tagNumber);
  	  if($len!=-1) {
	    $indent--;
  	    
  	    $escape.=$buf;
  	    /* after tag, do not escape */
    	if($indent==0) {
    	  $escape=mb_substr($escape, 0, mb_strlen($escape)-$len);
    	  $escape=$this->removeDescription($escape);
    	  $escape=trim($escape);
		  $tagEnd=$i;
    	  $interCode=$this->getInterCode($tagStart, $tagEnd);  	
  	   	  $this->noTranslations[$id]=array('escape'=>$escape,
  	  	                                   'code'=>$interCode,
										   'tag'=>$tagNumber);
  	  	  $id++;
    	  $encode.=' '.$interCode.' ';
    	  $escape='';
   	    }
   	    
   	    $buf='';
		continue;
  	  }
  	  
  	}
  	return $encode.$buf;
  }
  
  /**
   * if $str startsWith one of $array, return strlen($array[$i]), else -1
   */
  private function startsWith($array, $str, &$num=null) {
  	for($i=0; $i<count($array); $i++) {
	  if(strpos($str, $array[$i])!==false) {
	    $num=$i;
	  	return mb_strlen($array[$i]);
	  }
	}
	
	return -1;
  } 
  
  /**
   * Removing Descriptions
   */
  private function removeDescription($source) {
  	$ret='';
  	$indent=0;
  	
  	for($i=0; $i<mb_strlen($source); $i++) {
  	  $buf=mb_substr($source, $i, 1);
  	  
  	  if($buf=='(') {
  	  	$indent++;
  	  }
  	  
  	  if($indent==0) {
  	  	$ret.=$buf;
  	  }
  	  
  	  if($buf==')') {
  	  	$indent--;
  	  }
  	}
  	
  	return $ret;
  
  }
  	
  
  private function getInterCode($tagStart, $tagEnd) {
    $alt=dechex($tagStart).'x'.dechex($tagEnd);
  	$search=array('0','1','2','3','4','5','6','7','8','9');
	$replace=array('g','h','i','j','k','l','m','n','o','p');

	$result=str_replace($search, $replace, $alt);
	
  	return 'xxx'.$result.'xxx';
  }
  
  /**
   * Setter
   */
  public function setSourceLanguage($sourceLang) {
  	$this->sourceLang=$sourceLang;
  }
  
  public function getSourceLanguage() {
  	return $this->sourceLang;
  }
}
