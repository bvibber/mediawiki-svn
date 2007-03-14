<?
#(c) Joerg Baach 2007 GPL
/*


-- 
-- Table structure for table `revisiontags`
-- 

CREATE TABLE `flaggedrevs` (
  `fr_id` int(10) NOT NULL auto_increment,
  `fr_rev_id` int(10) NOT NULL,
  `fr_dimension` varchar(255) NOT NULL,
  `fr_flag` int(2) NOT NULL,
  `fr_user` int(5) NOT NULL,
  `fr_timestamp` char(14) NOT NULL,
  `fr_comment` varchar(255) default NULL,
  PRIMARY KEY  (`fr_id`),
  KEY `fr_rev_id` (`fr_rev_id`,`fr_dimension`,`fr_tag`,`fr_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COMMENT='Revision Tags Extension' AUTO_INCREMENT=0;



Possible Hooks
--------------

'BeforePageDisplay': Called just before outputting a page (all kinds of,
		     articles, special, history, preview, diff, edit, ...)
		     Can be used to set custom CSS/JS
$out: OutputPage object


'OutputPageBeforeHTML': a page has been processed by the parser and
the resulting HTML is about to be displayed.  
$parserOutput: the parserOutput (object) that corresponds to the page 
$text: the text that will be displayed, in HTML (string)

*/

class FlaggedRevs {
    
    var $dimensions = array('quality'=>array('flags'=>array(0=>'none',
                                                            1=>'unvandalised',
                                                            2=>'superb'),
                                             'comments'=>True,
                                             'default'=>0),
                            'funny'=>array('flags'=>array(0=>'none',
                                                          1=>'funny',
                                                          2=>'hillarious'),
                                             'comments'=>True,
                                             'default'=>0));


    function writeTag($rev_id,$dimension,$tag,$user,$comment) {
        
        
    }

    function getFlagsForRevision($rev_id) {
        #XXX dirty, dirty, dirty
        $limit = sizeof($this->dimensions);
        $sql = "select * from flaggedrevs where fr_rev_id=$rev_id order by fr_id desc limit $limit";
        $db =& wfGetDB(DB_MASTER);
        $result = $db->query($sql);
        $flags = array();
        for ($i=0;$i < $db->numRows($result);$i++) {
            $row=$db->fetchObject($result);
            $flags[$row->fr_dimension] = $row->fr_flag;
        }
        return $flags;
    }

    function addFlaggs(&$out) {
        global $wgArticle;
        if ($out->isArticle())
            $type = 'article';
        else
            $type = 'something';
        if ($type != 'article')
            return;
        #find out revision id
        if ($wgArticle->mRevision)
            $revid = $wgArticle->mRevision->mId;
        else
            $revid = $wgArticle->mLatest;
        $flags = Null;
        if ($revid) 
            $flags = $this->getFlagsForRevision($revid);
        $flaghtml = '';
        if (sizeof($flags)) { 
            foreach ($this->dimensions as $dimension=>$content) {
                $value = $content['flags'][$flags[$dimension]];
                $flaghtml.="<li>$dimension: $value</li>\n";    
            }
        } else {
            $flaghtml.='<li>No Flags yet</li>';    
        }
        #print_r($wgArticle);
        $out->mBodytext="<p>Flags for revision $revid:<ul>$flaghtml</ul></p>".$out->mBodytext;
        #$out->addHTML("<p><blink>$text</blink></p>");
        
    }

    function addToDiff(&$diff,&$oldrev,&$newrev) {
        global $wgOut;
        $id = $newrev->getId();
        $self = $_SERVER['PHP_SELF'];
        $title = $wgOut->mPagetitle;
        $flags = $this->getFlagsForRevision($id);
        $form = "
        <form action='$self/Special:FlaggedRevsPage/$title'>
        <input type='hidden' name='fr_rev_id' value='$id' />
        Flag the newer revision $id:<br>\n";
        foreach ($this->dimensions as $dimension=>$content) {
            $form.="$dimension <select name='dimensions[$dimension]'>\n";
            foreach ($content['flags'] as $idx=>$label) {
                if ($flags[$dimension] == $idx)
                    $selected = 'selected';
                else
                    $selected = '';
                $form.="<option value='$idx' $selected>$label</option>\n";    
            }
            $form.="</select>\n";
            
        }
        $form.="<input type='submit' value='flag'> (do a shift reload after being redirected)</form>";
        $wgOut->addHTML($form);
        
    }

    
}


$wgAutoloadClasses['FlaggedRevsPage'] = dirname(__FILE__) . '/FlaggedRevsPage.body.php';
$wgSpecialPages['FlaggedRevsPage'] = 'FlaggedRevsPage';
$wgHooks['LoadAllMessages'][] = 'FlaggedRevsPage::loadMessages';

$flaggedrevs = new FlaggedRevs();
$wgHooks['BeforePageDisplay'][] = array($flaggedrevs, 'addFlaggs');
$wgHooks['DiffViewHeader'][] = array($flaggedrevs, 'addToDiff');
?>
