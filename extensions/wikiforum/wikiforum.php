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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
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
	$wgExtensionFunctions[] = "wfForum";
	$wgExtensionFunctions[] = "wfNewthread";

	define('FORUM_PATH',         "extensions/wikiforum/" ); // extention path 
	define('FORUM_VERSION',      "1.0.4.0");
	define('FORUM_MAX_THREAD',   50);     // total number of last thread displayed on the forum page
	define('FORUM_INCLUDED_NUM', 20);     // number of thread directly included into the forum page
	define('FORUM_SUM_LENGHT',   32);     // maximum length of "last comment" field
	define('FORUM_CSS',          "$wgScriptPath/".FORUM_PATH."wikiforum.css" ); // forum styles
	define('FORUM_JS',           "$wgScriptPath/".FORUM_PATH."wikiforum.js" ); // forum styles
	define('FORUM_ALL_IN_TABLE', true );  // add included thread into the table (in a full width cell)
	define('FORUM_INC_ADD_SUM',  false ); // add link to the included thread into the summaries table
	define('FORUM_INC_TABLE',    true );  // create a table to put a link to the included thread
	define('FORUM_USE_JS',       false );  // use JS to toggle visibility of included thread

	require("language/default.php");
	
	$lang = "language/".$wgLanguageCode;
	if($wgUseLatin1)
		$lang .= "_latin1";
	else
		$lang .= "_utf8";
	$lang .= ".php";

	include($lang);

	/**
	 * Get language text value
	 *
	 * @package MediaWiki
	 * @subpackage Extensions
	 */
	function WF_Msg( $index )
	{
		global $wf_language, $wf_language_default;

		if(isset($wf_language))
		{
			if(isset($wf_language[$index]))
				return $wf_language[$index];
			else if(isset($wf_language_default[$index]))
				return $wf_language_default[$index];
		}
		else if(isset($wf_language_default[$index]))
			return $wf_language_default[$index];

		return "";
	}

	$wgExtraNamespaces[NS_THREAD]   = WF_Msg('Thread');
	$wgExtraNamespaces[NS_THREAD+1] = WF_Msg('ThreadTalk');

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

			$wgOut->setPagetitle( WF_Msg('ThreadNew') );
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
			                WF_Msg('ThreadTitle').": <input type='text' size='40' name='threadTitle' value='$title'/><br />\n".
			                "<textarea rows='$rows' cols='$cols' name='threadDescription'>$desc</textarea>\n".
			                "<input type='submit' value='".WF_Msg('ThreadOpen')."'/>\n".
			                "</form>\n");
		}

		function doSubmit() 
		{
			global $wgOut, $wgRequest, $wgParser, $wgUser, $wgContLang;
		
			$tt = $wgContLang->ucfirst(trim($wgRequest->getVal('threadTitle')));
			$title = Title::makeTitleSafe( NS_THREAD, $tt );

			if(!$tt or !$title) // invalid title
			{
				$wgOut->addHTML("<div id=\"threadexist\">".WF_Msg('ThreadInvalid')."</div>\n<br />\n");
				$this->showForm();
			}
			else if($title->getArticleID() == 0) // article don't exist
			{
				$article = new Article( $title );
				$article->insertNewArticle($wgRequest->getVal('threadDescription'), WF_Msg('ThreadNew'), false, false);
			}
			else // thread already exist
			{
				$wgOut->addHTML("<div id=\"threadexist\">".WF_Msg('ThreadExist')."</div>\n<br />\n");
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
			global $wgLang, $wgServer, $wgOut, $wgUserHtml;
		
			$fname = 'Forum::generate';

			$wgOut->setPagetitle('Forum');
			$wgOut->addLink(array(
				"rel"   => "stylesheet",
				"type"  => "text/css",
				"media" => "screen, projection",
				"href"  => FORUM_CSS
			));
			if(FORUM_USE_JS)
			{
				$wgOut->addHTML("<script language=\"JavaScript\">\n".
									 "function toggleElement(id, type)\n".
									 "{\n".
									 "   var elem = document.getElementById(id);\n".
									 "   if(elem.style.display == 'none')\n".
									 "      elem.style.display = type;\n".
									 "   else\n".
									 "      elem.style.display = 'none';\n".
									 "}\n".
									 "</script>\n");
			}
			$wgOut->addHTML("<!-- This page was generated by WikiForum v".FORUM_VERSION." -->\n");
			
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
			$num = $dbr->numRows( $res );

			
			// Generate forum's text
			$text = "";
			$text .= "__NOEDITSECTION____NOTOC__\n";
			$text .= "<div id=\"threadcreate\">[[Special:Newthread|".WF_Msg('ThreadCreate')."]]</div>\n\n";

			$text .= "> [{{SERVER}}{{localurl:Special:Allpages|from=&namespace=".NS_THREAD."}} ".WF_Msg('ThreadAll')."]\n\n";
			
			$tab = array();
			$cnt = 0;
			while( $x = $dbr->fetchRow( $res ) )
			{
				$cnt++;
				$tab[$num-$cnt] = new Thread;
				$tab[$num-$cnt]->title  = $x['cur_title'];
				$tab[$num-$cnt]->comment = $x['cur_comment'];
				$tab[$num-$cnt]->user  = $x['cur_user_text'];
				$tab[$num-$cnt]->timestamp = $x['cur_timestamp'];
				$tab[$num-$cnt]->count  = $x['cur_counter'];
				if(strlen($tab[$num-$cnt]->comment) > $this->mSumLength)
					$tab[$num-$cnt]->comment = substr($tab[$num-$cnt]->comment, 0, $this->mSumLength) . "  .";
			}
			$dbr->freeResult( $res );

			// secure include thread max
			if($this->mMaxFullText > $num)
				$this->mMaxFullText = $num;

			$summary = $num - $this->mMaxFullText;
			
			$wgOut->addWikiText( $text );
			$text = "";

			if(FORUM_ALL_IN_TABLE)
			{
				$t = WF_Msg('ThreadLastest');
				$t = str_replace("$1", $num, $t);
				$text .= "<h1>$t</h1>\n";
				$text .= "<table id=\"threadtable\" border=\"0\" cellspacing=\"0\" cellpadding=\"2px\" width=\"100%\">\n";
				$text .= "<tr class=\"threadrow\" id=\"threadtablehead\">\n";
				$text .= "<td>".WF_Msg('ThreadName')."</td>\n";
				$text .= "<td>".WF_Msg('ThreadView')."</td>\n";
				$text .= "<td>".WF_Msg('ThreadUser')."</td>\n";
				$text .= "<td>".WF_Msg('ThreadComment')."</td>\n";
				$text .= "<td>".WF_Msg('ThreadTime')."</td>\n";
				$text .= "</tr>\n";

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
							$text .= "<tr class=\"threadrow\" id=\"threadrowodd\">\n";
						else
							$text .= "<tr class=\"threadrow\" id=\"threadrowpeer\">\n";
			
						$text .= "<td>[[$t|". $title->getText() ."]]</td>".
						         "<td>". $tab[$cnt]->count."</td>".
						         "<td>[[". $wgLang->getNsText( NS_USER ) .":". $tab[$cnt]->user ."|" .$tab[$cnt]->user. "]]</td>".
						         "<td>". $tab[$cnt]->comment . "</td>".
						         "<td>". $wgLang->timeanddate($tab[$cnt]->timestamp) ."</td>\n";
						
						$text .= "</tr>\n";
					}
					else
					{
						if($cnt & 1)
							$text .= "<tr class=\"threadrow\" id=\"threadincodd\">\n";
						else
							$text .= "<tr class=\"threadrow\" id=\"threadincpeer\">\n";
			
						$text .= "<td>[[$t|". $title->getText() ."]]";
						if(FORUM_USE_JS)
							$text .= "<a href=\"\" onclick=\"toggleElement('thread_body_$cnt', 'table-row')\" >view</a></td>";
						$text .= "</td>";
						
						$text .= "<td>". $tab[$cnt]->count."</td>".
						         "<td>[[". $wgLang->getNsText( NS_USER ) .":". $tab[$cnt]->user ."|" .$tab[$cnt]->user. "]]</td>".
						         "<td>". $tab[$cnt]->comment . "</td>".
						         "<td>". $wgLang->timeanddate($tab[$cnt]->timestamp) ."</td>\n";
						
						$text .= "</tr>\n";
						
						if($cnt & 1)
							$text .= "<tr class=\"threadrow\" id=\"contentincodd, thread_body_$cnt\">\n";
						else
							$text .= "<tr class=\"threadrow\" id=\"contentincpeer, thread_body_$cnt\">\n";
			
						$text .= "<td colspan=\"5\"\>n";
						$text .= "<div id=\"threadedit\" style=\"float:right;\">[[$wgServer" . $title->getEditUrl() ." ".WF_Msg('ThreadEdit')."]]</div>\n";
						$text .= "{{{$t}}}\n";
						$text .= "</td>\n";
					
						$text .= "</tr>\n";
					}
				}
				
				$text .= "</table>\n\n\n";
			}
			else
			{
				// render summaries table
				if(($summary > 0) || FORUM_INC_ADD_SUM)
				{
					if(FORUM_INC_ADD_SUM)
						$max = $num;
					else
						$max = $summary;

					$t = WF_Msg('ThreadLastest');
					$t = str_replace("$1", $max, $t);
					$text .= "<h1>$t</h1>\n";
					$text .= "{| id=\"threadtable\" border=\"0\" cellspacing=\"0\" cellpadding=\"2px\" width=\"100%\"\n";
					$text .= "|- class=\"threadrow\" id=\"threadtablehead\"\n";
					$text .= "! ".WF_Msg('ThreadName')." !! ".WF_Msg('ThreadView')." !! ".WF_Msg('ThreadUser')." !! ".WF_Msg('ThreadComment')." !! ".WF_Msg('ThreadTime')."\n";

					for( $cnt=0; $cnt<$max; $cnt++ )
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
							if($cnt & 1)
								$text .= "|- class=\"threadrow\" id=\"threadincodd\"\n";
							else
								$text .= "|- class=\"threadrow\" id=\"threadincpeer\"\n";
				
							$text .= "| [[#".$title->getText()."|".$title->getText()."]] ".
							         "|| ". $tab[$cnt]->count." ".
							         "|| [[". $wgLang->getNsText( NS_USER ) .":". $tab[$cnt]->user ."|" .$tab[$cnt]->user. "]] ".
							         "|| ". $tab[$cnt]->comment . " " .
							         "|| ". $wgLang->timeanddate($tab[$cnt]->timestamp) ."\n";
						}
					}
				
					$text .= "|}\n\n";
				}

				// render includes thread
				if($this->mMaxFullText > 0)
				{
					if(FORUM_INC_TABLE)
					{
						$t = WF_Msg('ThreadIncluded');
						$t = str_replace("$1", $this->mMaxFullText, $t);
						$text .= "<h1>$t</h1>\n";
						$text .= "{| id=\"threadtable\" border=\"0\" cellspacing=\"0\" cellpadding=\"2px\" width=\"100%\"\n";
						$text .= "|- class=\"threadrow\" id=\"threadtablehead\"\n";
						$text .= "! ".WF_Msg('ThreadName')." !! ".WF_Msg('ThreadView')." !! ".WF_Msg('ThreadUser')." !! ".WF_Msg('ThreadComment')." !! ".WF_Msg('ThreadTime')."\n";

						for( $cnt=$summary; $cnt<$num; $cnt++ )
						{
							$t = $wgLang->getNsText( NS_THREAD );
							if ( $t != '' ) 
								$t .= ':' ;
							$t .= $tab[$cnt]->title;

							$title = Title::newFromText( $t );

							if($cnt & 1)
								$text .= "|- class=\"threadrow\" id=\"threadincodd\"\n";
							else
								$text .= "|- class=\"threadrow\" id=\"threadincpeer\"\n";
			
							$text .= "| [[#".$title->getText()."|".$title->getText()."]] ".
							         "|| ". $tab[$cnt]->count." ".
							         "|| [[". $wgLang->getNsText( NS_USER ) .":". $tab[$cnt]->user ."|" .$tab[$cnt]->user. "]] ".
							         "|| ". $tab[$cnt]->comment . " " .
							         "|| ". $wgLang->timeanddate($tab[$cnt]->timestamp) ."\n";
						}
				
						$text .= "|}\n\n";
					}

					for( $cnt=$summary; $cnt<$num; $cnt++ )
					{
						$t = $wgLang->getNsText( NS_THREAD );
						if ( $t != '' ) 
							$t .= ':' ;
						$t .= $tab[$cnt]->title;

						$title = Title::newFromText( $t );

						$text .= "<div id=\"threadcontent\">\n";
						$text .= "<div id=\"threadedit\" style=\"float:right;\">[[$wgServer" . $title->getEditUrl() ." ".WF_Msg('ThreadEdit')."]]</div>\n";
						$text .= "==".$title->getText()."==\n";
						$text .= "{{{$t}}}\n";
						$text .= "</div>\n";
					}
				}
			}

			$text .= "<div id=\"threadcreate\">[[Special:Newthread|".WF_Msg('ThreadCreate')."]]</div>";
			
			wfDebug("FORUM - END GENERATE\n");

			$wgOut->addWikiText( $text );
			//return $text;
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
				global $wgForum;

				$wgForum->Generate();
			}
		}

		global $wgMessageCache;
		SpecialPage::addPage( new SpecialForum );
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
	}

} // end if(defined('MEDIAWIKI'))

?>
