<?php
# Copyright (C) 2004 Thomas V. <thomasV1@gmx.de>
# http://www.mediawiki.org/
# http://wikisource.org/wiki/Proposed_extension
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
# http://www.gnu.org/copyleft/gpl.html

if (defined('MEDIAWIKI')) {

$wgExtensionFunctions[] = "wfRegister";

function wfRegister() {
    global $wgParser;
    $wgParser->setHook( "enumcat_fr", "enumCategoryFrench"     );
    $wgParser->setHook( "enumcat_en", "enumCategoryEnglish"    );
    $wgParser->setHook( "enumcat_de", "enumCategoryGerman"     );
    $wgParser->setHook( "enumcat_it", "enumCategoryItalian"    );
    $wgParser->setHook( "enumcat_es", "enumCategorySpanish"    );
    $wgParser->setHook( "enumcat_pt", "enumCategoryPortuguese" );
    $wgParser->setHook( "enumcat",    "enumCategory" );
}

function enumCategoryFrench($input)
{
    $r=enumCategory($input);
    $r = str_replace( 'french',     'français',$r);
    $r = str_replace( 'german',     'allemand',       $r);
    $r = str_replace( 'english',    'anglais',        $r);
    $r = str_replace( 'italian',    'italien',        $r);
    $r = str_replace( 'spanish',    'espagnol',       $r);
    $r = str_replace( 'portuguese', 'portuguais',     $r);
    return $r;
}

function enumCategoryEnglish($input)
{
    $r=enumCategory($input);
    $r = str_replace( 'french',     'French',    $r);
    $r = str_replace( 'german',     'German',    $r);
    $r = str_replace( 'english',    'English',   $r);
    $r = str_replace( 'italian',    'Italian',   $r);
    $r = str_replace( 'spanish',    'Spanish',   $r);
    $r = str_replace( 'portuguese', 'Portuguese',$r);
    return $r;
}

function enumCategoryGerman($input)
{
    $r=enumCategory($input);
    $r = str_replace( 'french',     'französisch', $r);
    $r = str_replace( 'german',     'deutsch',      $r);
    $r = str_replace( 'english',    'englisch',     $r);
    $r = str_replace( 'italian',    'italienisch',  $r);
    $r = str_replace( 'spanish',    'spanisch',     $r);
    $r = str_replace( 'portuguese', 'portugiesisch',$r);
    return $r;
}

function enumCategorySpanish($input)
{
    $r=enumCategory($input);
    $r = str_replace( 'french',     'francés',  $r);
    $r = str_replace( 'german',     'alemán',   $r);
    $r = str_replace( 'english',    'inglés',   $r);
    $r = str_replace( 'italian',    'italiano',        $r);
    $r = str_replace( 'spanish',    'español',  $r);
    $r = str_replace( 'portuguese', 'portugués',$r);
    return $r;
}

function enumCategoryItalian($input)
{
    $r=enumCategory($input);    
    $r = str_replace( 'french',     'francese',  $r);
    $r = str_replace( 'german',     'tedesco',   $r);
    $r = str_replace( 'english',    'inglese',   $r);
    $r = str_replace( 'italian',    'italiano',  $r);
    $r = str_replace( 'spanish',    'spagnolo',  $r);
    $r = str_replace( 'portuguese', 'portoghese',$r);
    return $r;
}

function enumCategoryPortuguese($input)
{
    $r=enumCategory($input);    
    $r = str_replace( 'french',     'francês',  $r);
    $r = str_replace( 'german',     'alemão',  $r);
    $r = str_replace( 'english',    'inglês',   $r);
    $r = str_replace( 'italian',    'italiano',       $r);
    $r = str_replace( 'spanish',    'espanhol',       $r);
    $r = str_replace( 'portuguese', 'português',$r);
    return $r;
}



function enumCategory($input) {

        global $wgContLang,$wgUser;
        $sk =& $wgUser->getSkin();
        $r = "";
        $articles = array() ;
        $dbr =& wfGetDB( DB_SLAVE );
        $cur = $dbr->tableName( 'cur' );
        $categorylinks = $dbr->tableName( 'categorylinks' );
        $t= $input;
        $sql = "SELECT DISTINCT cur_title,cur_namespace,cl_sortkey FROM " .
                "$cur,$categorylinks WHERE cl_to='$t' AND cl_from=cur_id AND cur_is_redirect=0 ORDER BY cl_sortkey" ;
        $res = $dbr->query ( $sql ) ;
        while ( $x = $dbr->fetchObject ( $res ) )
        {
                $t = $ns = $wgContLang->getNsText ( $x->cur_namespace ) ;
                if ( $t != '' ) $t .= ':' ;
                $t .= $x->cur_title ;
                $ctitle = str_replace( '_',' ',$x->cur_title );
                array_push ( $articles , $sk->makeKnownLink ( $t, $x->cl_sortkey ) ) ; 
        }
        $dbr->freeResult ( $res ) ;
        if ( count($articles) > 0) {
                $r .= ' '.$articles[0].' ';
                for ($index = 1; $index < count($articles); $index++ )
                        $r .= "| {$articles[$index]} ";
        }
        return $r;
}

} // end of if defined(MEDIAWIKI)
?>
