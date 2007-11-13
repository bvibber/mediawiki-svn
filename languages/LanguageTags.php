<?php
/**
 * wgLanguageWikimedia: Legacy Language Codes
 * wgLanguageNames: Native language names
 * wgLanguageIds: ISO 639-3+ Language Identifiers
**/

global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageNames, $wgMainCacheType;

// FIXME: Don't do this.
$vars=array('wgLanguageNames','wgLanguageIds','wgLanguageWikimedia','wgLanguageProper');
foreach($vars as $v) if(!(isset($$v) && is_array($$v))) $$v=array();
if($wgMainCacheType!==CACHE_NONE) {
	$cache = wfGetMessageCacheStorage(); // 80ms for 7k language codes
	$x=0; foreach($vars as $v) if(!count($$v)) ($$v=$cache->get($v)) === false ? $$v=array() : $x++;
}

/**
* Legacy to Language Tag
**/

function wgLanguageWikimedia($code='en') {
	global $wgLanguageWikimedia,$wgLanguageNames;
	if(!array_key_exists($code,$wgLanguageNames)) return $code;
	if(array_key_exists($code,$wgLanguageWikimedia)) return $wgLanguageWikimedia[$code];
	else {
		$wgLanguageWikimedia[$code] = '';
		wgLanguageTagFromConds(array('wikimedia_key'=>$code));
		return $wgLanguageWikimedia[$code];
	}
}

/**
* Unknown to ISO 639-3 per RFC 4646
**/

function wgLanguageCodeProper($str) {
        global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageNames, $wgMainCacheType, $wgLanguageProper;
	if(is_numeric($str)) if(array_key_exists($str,$wgLanguageProper)) return $wgLanguageProper[$str];
	if(is_array($wgLanguageProper)) if(in_array($str,$wgLanguageProper,1)) return $str;
	if($id=array_search($str,$wgLanguageIds,1)) if($wgLanguageProper && array_key_exists($id,$wgLanguageProper)) return $wgLanguageProper[$id];
	if(in_array($str,$wgLanguageWikimedia,1)) return $wgLanguageProper[wgLanguageId($str)];
}

/**
* Legacy to Language Tag
**/
function wgLanguageCodeLegacy($str) {
        global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageNames, $wgMainCacheType;
	if($str == 'und' || $str=='mul') return $GLOBALS['wgLanguageCode'];
	if(strlen($str) < 2) return $str;

        if(in_array($str,$wgLanguageIds,1)) {
		if(!array_key_exists($str,$wgLanguageWikimedia)) {
			$tmp=wgLanguageId($str); $tmp=wgLanguageCodeProper($str);
			if(!$tmp) return $GLOBALS['wgLanguageCode'];
			return $str;
		}
		return $wgLanguageWikimedia[$str];
	}

	if(!in_array($str,$wgLanguageWikimedia,1)) {
		$id=wgLanguageId($str);
		if(!$id && !is_int($id)) return $str;
		if(!$id) return wgLanguageCode($str);
		if(!in_array($str,$wgLanguageWikimedia)) return $str;	
	}

	return $str;
}

/**
* Unknown to Language Tag
**/
function wgLanguageCode3($str) {
	global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageNames, $wgMainCacheType;
	if(!strlen($str) || $str===null || $str=='   ') return 'und';
	if(in_array($str,$wgLanguageIds,1)) return $str;
	if(in_array($str,$wgLanguageWikimedia,1)) return $wgLanguageWikimedia[$str];
	else $str = wgLanguageWikimedia($str);	
	return wgLanguageCode(wgLanguageId($str));
}

function wgLanguageTagFromConds($conds) { 
	global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageProper, $wgMainCacheType;

	$dbr =& wfGetDB( DB_SLAVE );

	$result = $dbr->select('langtags',
		array('language_id','tag_name','wikimedia_key','rfc4646','preferred_id'),
		$conds,'wgLanguageTag::fromConds',
		array('ORDER BY'=>'preferred_id asc'));
	$found=false;
	while( $row = $dbr->fetchObject( $result ) ) {
		if(!$found) $found=true;
		$wgLanguageIds[$row->language_id]=$row->tag_name;
		if($row->rfc4646) {
			$wgLanguageProper[$row->language_id] = $row->rfc4646;
		}
		else if($row->wikimedia_key) {
			$wgLanguageProper[$row->language_id] = $row->wikimedia_key;
		}
		if($row->preferred_id) {
			$wgLanguageProper[$row->language_id] = wgLanguageCode($row->preferred_id);
		}
                if($row->wikimedia_key) {
                        $wgLanguageWikimedia[$row->wikimedia_key] = $row->tag_name; //WRONG WRONG WRONG
                        $wgLanguageWikimedia[$row->tag_name] = $row->wikimedia_key; //RIGHT RIGHT RIGHT
                }
        }
	$dbr->freeResult( $result );
	return $found;
	$vars=array('wgLanguageNames','wgLanguageIds','wgLanguageWikimedia','wgLanguageProper');
	$cache = wfGetMessageCacheStorage();
	if($wgMainCacheType!==CACHE_NONE) foreach($vars as $v) $cache->set($v,$$v);
}

/**
* Integer to Language Tag
**/
function wgLanguageCode($lid=null,$legacy=false) {
	global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageNames, $wgMainCacheType;
	$vars=array('wgLanguageNames','wgLanguageIds','wgLanguageWikimedia');
	$cache = wfGetMessageCacheStorage();
        
	if($lid === null) return 'und';
	if(!strlen(trim($lid))) return 'und';
	if($lid === 0 ) return 'mul';

	if( array_key_exists($lid,$wgLanguageIds) ) {
		return $wgLanguageIds[$lid];
	}
        
	$wgLanguageIds[$lid]=null;
	wgLanguageTagFromConds(array('language_id'=>$lid,'is_enabled'=>'1'));
	return $wgLanguageIds[$lid];
}

/**
* ISO 639-3+ to Integer
**/
function wgLanguageId($code='en',$strict=false) {
	global $wgLanguageIds, $wgLanguageWikimedia, $wgLanguageProper, $wgMainCacheType;
## First run of update.php
# $wgLanguageIds[1]='eng';

	if(!strlen(trim($code)) || $code===null) return null;
	if($code == 'mul') return 0;

	if($strict) {
		if(strlen($code)>42 || strlen($code)<2) return false;
		if(strpos($code,'-')>5) return false;
		if(strpos($code,'-')===false) {
			if(strlen($code)>3) return false;
		}
		else if(strpos($code,'-',2)) {
			$code=substr($code,0,strpos($code,'-',2));
		}
		if(strpos($code,'-')=='2' && strlen($code)==5) {
			$code=substr($code,3);
		}
	}

	if(false !== $lid=array_search($code,$wgLanguageIds,1)) return $lid;
	if(!$strict) {
		if(strlen($code) != 3 && $lid=array_key_exists($code,$wgLanguageWikimedia)) $code = $wgLanguageWikimedia[$code];
		if(false !== $lid=array_search($code,$wgLanguageIds,1)) return $lid;
	}

	wgLanguageTagFromConds(array('tag_name'=>$code,'is_enabled'=>'1'));

        $lid=array_search($code,$wgLanguageIds,1);
 	if(false === $lid) {
		$lid = null;	
#		$wgLanguageWikimedia[$code]=null;
	}
	return $lid;
}

if(!$wgLanguageWikimedia) {
### This fails when language tables are not yet defined
//	wgLanguageId('eng');
}

?>
