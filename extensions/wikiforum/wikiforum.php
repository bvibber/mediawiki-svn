<?php
/* Wikiforum.php -- a basic forum extension for Mediawiki
 * Copyright 2004 Guillaume Blanchard <aoineko@free.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Guillaume Blanchard <aoineko@free.fr>
 * @package MediaWiki
 * @subpackage Extensions
 */

/**
 * This is not a valid entry point, perform no further processing unless MEDIAWIKI is defined
 */
if(defined('MEDIAWIKI')) 
{
   $wgExtraNamespaces[NS_THREAD] = "Thread";

   $wgExtensionFunctions[] = "wfForum";
   $wgExtensionFunctions[] = "wfNewthread";

   define('FORUM_VERSION',      "1.0.1.0");
   define('FORUM_MAX_THREAD',   50); // total number of last thread displayed on the forum page
   define('FORUM_INCLUDED_NUM', 10); // number of thread directly included into the forum page
   define('FORUM_SUM_LENGHT',   32); // maximum length of "last comment" field
   define('FORUM_CSS',          "$IP/extensions/wikiforum.css" ); // forum styles

   /**
    * New thread class
    *
    * @package MediaWiki
    * @subpackage Extensions
    */
   class NewThread 
   {
	   function showForm() 
      {
         global $wgOut, $wgUser, $wgRequest;

		   $wgOut->setPagetitle( wfMsg('ThreadNew') );
         $wgOut->addLink(array(
            "rel"   => "stylesheet",
            "type"  => "text/css",
            "media" => "screen,projection",
            "href"  => FORUM_CSS
         ));
		   
		   $titleObj = Title::makeTitle( NS_SPECIAL, "Newthread" );
		   $action = $titleObj->escapeLocalURL( "action=submit" );
		   
         $title = $wgRequest->getVal('threadTitle');
         $desc  = $wgRequest->getVal('threadDescription');

         $rows = $wgUser->getOption( "rows" );
		   $cols = $wgUser->getOption( "cols" );
		   $wgOut->addHTML("<form id='newthread' method='post' action='{$action}'>\n".
                         wfMsg('ThreadTitle').": <input type='text' size='40' name='threadTitle' value='$title'/><br />\n".
                         "<textarea rows='$rows' cols='$cols' name='threadDescription'>$desc</textarea>\n".
                         "<input type='submit' value='".wfMsg('ThreadOpen')."'/>\n".
                         "</form>\n");
	   }

	   function doSubmit() 
      {
		   global $wgOut, $wgRequest, $wgParser, $wgUser;
      
         $title = Title::makeTitle( NS_THREAD, ucfirst($wgRequest->getVal('threadTitle')) );

         if($title->getArticleID() == 0) // article don't exist
         {
            $article = new Article( $title );
            $article->insertNewArticle($wgRequest->getVal('threadDescription'), wfMsg('ThreadNew'), false, false);
         }
         else
         {
            $wgOut->addHTML("<div id=\"threadexist\">".wfMsg('ThreadExist')."</div>\n<br />\n");
            $this->showForm();
         }
	   }
   }

   /**
    * Thread class
    *
    * @package MediaWiki
    * @subpackage Extensions
   */
   class Thread
   {
      var $title;
      var $comment;
      var $user;
      var $timestamp;
      var $count;
   }
    
   /**
    * Forum class
    *
    * @package MediaWiki
    * @subpackage Extensions
    */
   class Forum
   {
      var $mMaxThread;
      var $mMaxFullText;
      var $mSumLength;

      function Forum($thread=FORUM_MAX_THREAD, $included=FORUM_INCLUDED_NUM, $sum=FORUM_SUM_LENGHT)
      {
         $this->mMaxThread   = $thread;
         $this->mMaxFullText = $included;
         $this->mSumLength   = $sum;
      }

      function SetThreadNumber($thread=FORUM_MAX_THREAD)
      {
         $this->mMaxThread   = $thread;
      }

      function SetIncludeNumber($included=FORUM_INCLUDED_NUM)
      {
         $this->mMaxFullText = $included;
      }

      function SetSummaryMaxLength($sum=FORUM_SUM_LENGHT)
      {
         $this->mSumLength   = $sum;
      }

      function Generate()
      {
         global $wgLang, $wgServer;

         $fname = 'Forum::generate';

         // Get last X modified thread
         wfDebug("FORUM - START GENERATE\n");
         $dbr =& wfGetDB( DB_SLAVE );
         $cur = $dbr->tableName( 'cur' );
         $sql = "SELECT cur_title, cur_comment, cur_user_text, cur_timestamp, cur_counter FROM $cur".
                " WHERE cur_namespace = ".NS_THREAD.
                " AND cur_is_redirect = 0".
                " ORDER BY cur_timestamp DESC".
                " LIMIT $this->mMaxThread;";
         $res = $dbr->query( $sql, $fname ) ;
         $num = mysql_num_rows( $res );

         // Generate forum's text
         $text = "";
         $text .= "__NOEDITSECTION____NOTOC__\n";
         $text .= "<!-- This page was generated by forum.php -->\n";
         $text .= "<div id=\"threadcreate\">[[Special:Newthread|".wfMsg('ThreadCreate')."]]</div>\n\n";

         $text .= "> [{{SERVER}}{{localurl:Special:Allpages|from=&namespace=".NS_THREAD."}} ".wfMsg('ThreadAll')."]\n\n";
         
         $tab = array();
         $cnt = 0;
         while( $x = mysql_fetch_array( $res ) )
         {
            $cnt++;
            $tab[$num-$cnt] = new Thread;
            $tab[$num-$cnt]->title     = $x['cur_title'];
            $tab[$num-$cnt]->comment   = $x['cur_comment'];
            $tab[$num-$cnt]->user      = $x['cur_user_text'];
            $tab[$num-$cnt]->timestamp = $x['cur_timestamp'];
            $tab[$num-$cnt]->count     = $x['cur_counter'];
            if(strlen($tab[$num-$cnt]->comment) > $this->mSumLength)
               $tab[$num-$cnt]->comment = substr($tab[$num-$cnt]->comment, 0, $this->mSumLength) . "...";
         }
         mysql_free_result( $res );

         $summary = $num - $this->mMaxFullText;

         if($summary > 0)
         {
            $text .= "<h1>$summary ".wfMsg('ThreadLastest')."</h1>\n";
            $text .= "{| id=\"threadtable\" border=\"0\" cellspacing=\"0\" cellpadding=\"4px\" width=\"100%\"\n";
            $text .= "|- id=\"threadtablehead\"\n";
            $text .= "! ".wfMsg('ThreadName')." !! ".wfMsg('ThreadView')." !! ".wfMsg('ThreadUser')." !! ".wfMsg('ThreadComment')." !! ".wfMsg('ThreadTime')."\n";
         }
                                    
         for( $cnt=0; $cnt<$num; $cnt++ )
         {
            $t = $wgLang->getNsText( NS_THREAD );
            if ( $t != '' ) 
               $t .= ':' ;
            $t .= $tab[$cnt]->title;

            $title = Title::newFromText( $t );

            if($cnt < $summary)
            {
               if($cnt & 1)
                  $text .= "|- class=\"threadrow\" id=\"threadrowodd\"\n";
               else
                  $text .= "|- class=\"threadrow\" id=\"threadrowpeer\"\n";
               $text .= "| [[$t|". $title->getText() ."]] ".
                        "|| ". $tab[$cnt]->count." ".
                        "|| [[". $wgLang->getNsText( NS_USER ) .":". $tab[$cnt]->user ."|" .$tab[$cnt]->user. "]] ".
                        "|| ". $tab[$cnt]->comment . " " .
                        "|| ". $wgLang->timeanddate($tab[$cnt]->timestamp) ."\n";
            }
            else
            {
               $text .= "<div id=\"threadcontent\">\n";
               $text .= "<div id=\"threadedit\" style=\"float:right;\">[[$wgServer" . $title->getEditUrl() ." ".wfMsg('ThreadEdit')."]]</div>\n";
               $text .= "==".$title->getText()."==\n";
               $text .= "{{{$t}}}\n";
               $text .= "</div>\n<br />\n";
            }

            if($cnt == $summary-1)
            {
               if($summary > 0)
                  $text .= "|}\n\n";
            }
         }

         $text .= "<div id=\"threadcreate\">[[Special:Newthread|".wfMsg('ThreadCreate')."]]</div>";

         wfDebug("FORUM - END GENERATE\n");

         return $text;
      }
   }

   $wgForum = new Forum();

   /**
    * Forum special page
    *
    * @package MediaWiki
    * @subpackage Extensions
    */
   function wfForum() 
   {
      global $IP;
      require_once( "$IP/includes/SpecialPage.php" );

      class SpecialForum extends SpecialPage
      {
	      function SpecialForum() 
         {
		      SpecialPage::SpecialPage("Forum");
            SpecialPage::setListed(true);
	      }

	      function execute() 
         {
            global $wgOut, $wgForum;

	         $wgOut->setPagetitle('Forum');
            $wgOut->addLink(array(
               "rel"   => "stylesheet",
               "type"  => "text/css",
               "media" => "screen,projection",
               "href"  => FORUM_CSS
            ));
            $wgOut->addWikiText( $wgForum->Generate() );
         }
      }

      global $wgMessageCache;
      SpecialPage::addPage( new SpecialForum );
      $wgMessageCache->addMessage( "ThreadName",      "Name" );
      $wgMessageCache->addMessage( "ThreadView",      "View" );
      $wgMessageCache->addMessage( "ThreadUser",      "Last user" );
      $wgMessageCache->addMessage( "ThreadComment",   "Last comment" );
      $wgMessageCache->addMessage( "ThreadTime",      "Time" );
      $wgMessageCache->addMessage( "ThreadEdit",      "Edit" );
      $wgMessageCache->addMessage( "ThreadCreate",    "Create a new thread" );
      $wgMessageCache->addMessage( "ThreadAll",       "View all threads" );
      $wgMessageCache->addMessage( "ThreadLastest",   "lastest threads" );
   }

   
   /**
    * New thread special page
    *
    * @package MediaWiki
    * @subpackage Extensions
    */
   function wfNewthread() 
   {
      global $IP;
      require_once( "$IP/includes/SpecialPage.php" );

      class SpecialNewthread extends SpecialPage
      {
	      function SpecialNewthread() 
         {
		      SpecialPage::SpecialPage("Newthread");
            SpecialPage::setListed(false);
	      }

	      function execute() 
         {
	         global $wgRequest, $action;

	         $nt = new NewThread();
	         if ( $action == "submit" && $wgRequest->wasPosted() ) 
		         $nt->doSubmit();
	         else 
		         $nt->showForm();
         }
      }

      global $wgMessageCache;
      SpecialPage::addPage( new SpecialNewthread );
      $wgMessageCache->addMessage( "ThreadNew",    "New thread" );
      $wgMessageCache->addMessage( "ThreadTitle",  "Thread title" );
      $wgMessageCache->addMessage( "ThreadOpen",   "Open thread" );
      $wgMessageCache->addMessage( "ThreadExist",  "This thread already exist, please chose an other name!" );

   }

} // end if(defined('MEDIAWIKI'))

?>
