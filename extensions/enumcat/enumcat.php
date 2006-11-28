<?php

$wgExtensionFunctions[] = "wfRegister";

function wfRegister() {
    global $wgParser;
    $wgParser->setHook( "enumcat_fr", "enumCategoryFrench"     );
    $wgParser->setHook( "enumcat_en", "enumCategoryEnglish"    );
    $wgParser->setHook( "enumcat_de", "enumCategoryGerman"     );
    $wgParser->setHook( "enumcat_it", "enumCategoryItalian"    );
    $wgParser->setHook( "enumcat_es", "enumCategorySpanish"    );
    $wgParser->setHook( "enumcat_pt", "enumCategoryPortuguese" );
    $wgParser->setHook( "enumcat_da", "enumCategoryDanish"     );
    $wgParser->setHook( "enumcat_pl", "enumCategoryPolish"     );
    $wgParser->setHook( "enumcat",    "enumCategory" );
}

function enumCategoryFrench($input)
{
    $table = array(
           'french'    => 'fran&ccedil;ais',
           'german'    => 'allemand',
    	   'english'   => 'anglais',
           'italian'   => 'italien',
           'spanish'   => 'espagnol',
           'portuguese'=> 'portuguais',
           'danish'    => 'danois',
           'polish'    => 'polonais'
        );
    $r = enumCategory($input,$table);
    return $r;
}

function enumCategoryEnglish($input)
{
    $table = array(
           'french'    => 'French',
           'german'    => 'German',   
    	   'english'   => 'English',   
           'italian'   => 'Italian',  
           'spanish'   => 'Spanish',  
           'portuguese'=> 'Portuguese',
           'danish'    => 'Danish',
           'polish'    => 'Polish'
        );
    $r = enumCategory($input,$table);
    return $r;
}

function enumCategoryGerman($input)
{
    $table = array(
           'french'    => 'franz&ouml;sisch', 
           'german'    => 'deutsch',     
    	   'english'   => 'englisch',    
           'italian'   => 'italienisch', 
           'spanish'   => 'spanisch',    
           'portuguese'=> 'portugiesisch',
           'danish'    => 'd&auml;nisch',
           'polish'    => 'polnisch'
        );
    $r = enumCategory($input,$table);
    return $r;
}

function enumCategorySpanish($input)
{
    $table = array(
           'french'    => 'franc&eacute;s', 
           'german'    => 'alem&aacute;n',  
    	   'english'   => 'ingl&eacute;s',  
           'italian'   => 'italiano',       
           'spanish'   => 'espa&ntilde;ol', 
           'portuguese'=> 'portugu&eacute;s',
    	   'danish'    => 'dan&eacute;s',
           'polish'    => 'pulimento'
        );
    $r = enumCategory($input,$table);
    return $r;
}

function enumCategoryItalian($input)
{
    $table = array(
           'french'    => 'francese', 
           'german'    => 'tedesco',  
    	   'english'   => 'inglese',  
           'italian'   => 'italiano', 
           'spanish'   => 'spagnolo', 
           'portuguese'=> 'portoghese',
    	   'danish'    => 'danese',
           'polish'    => 'polacco'
        );
    $r = enumCategory($input,$table);
    return $r;
}

function enumCategoryPortuguese($input)
{
    $table = array(
           'french'    => 'franc&ecirc;s', 
           'german'    => 'alem&atilde;o', 
    	   'english'   => 'ingl&ecirc;s',  
           'italian'   => 'italiano',      
           'spanish'   => 'espanhol',      
           'portuguese'=> 'portugu&ecirc;s',
           'danish'    => 'dinamarqu&ecirc;s',
           'polish'    => 'polon&ecirc;s'
        );
    $r = enumCategory($input,$table);
    return $r;
}


function enumCategoryDanish($input)
{
    $table = array(
           'french'    => 'fransk',
           'german'    => 'tysk',
    	   'english'   => 'engelsk',
           'italian'   => 'italiensk',
           'spanish'   => 'spansk',
           'portuguese'=> 'portugisisk',
           'danish'    => 'dansk',
           'polish'    => 'polsk'
        );
    $r = enumCategory($input,$table);
    return $r;
}


function enumCategoryPolish($input)
{
    $table = array(
           'french'    => 'J&#281;zyku francuskim',
           'german'    => 'Niemcu',
    	   'english'   => 'J&#281;zyku angielskim',
           'italian'   => 'W&#322;ochu',
           'spanish'   => 'J&#281;zyku hiszpa&#324;skim ',
           'portuguese'=> 'Portugalczyku',
           'danish'    => 'J&#281;zyku du&#324;skim',
           'polish'    => 'J&#281;zyku polskim '
        );
    $r = enumCategory($input,$table);
    return $r;
}



function enumCategory($input, $table = null) {

	global $wgContLang,$wgUser;
	$sk =& $wgUser->getSkin();
	$articles = array() ;
	$dbr =& wfGetDB( DB_SLAVE );
	$cur = $dbr->tableName( 'cur' );
	$categorylinks = $dbr->tableName( 'categorylinks' );
	$sql = "SELECT DISTINCT cur_title,cur_namespace,cl_sortkey FROM $cur,$categorylinks "
		."WHERE cl_to=? AND cl_from=cur_id AND cur_is_redirect=0 ORDER BY cl_sortkey LIMIT 100" ;
	$res = $dbr->safeQuery ( $sql, $input ) ;
	while( $x = $dbr->fetchObject ( $res ) ){
		$t = Title::makeTitle ($x->cur_namespace, $x->cur_title);
		if ($table !=null)
			$linkname = $table[$x->cl_sortkey];
		else 
			$linkname = $x->cl_sortkey;
		array_push ( $articles , $sk->makeLinkObj(  $t, $linkname ) ) ; 
	}
	$dbr->freeResult ( $res ) ;
	return implode ( ' | ' , $articles ) ;
}

?>