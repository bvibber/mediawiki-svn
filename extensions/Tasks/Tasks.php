<?php
/*
To activate, put something like this in your LocalSettings.php:
	$wgTasksNamespace = 200;
	$wgExtraNamespaces[$wgTasksNamespace] = "Task";
	$wgExtraNamespaces[$wgTasksNamespace+1] = "Task_Talk";
	include ( "extensions/Tasks.php" );

Also, you need to run the following SQL statement (with respect to your table prefix!):
CREATE TABLE tasks (
  task_id int(8) unsigned NOT NULL auto_increment,
  task_page_id int(8) unsigned NOT NULL default '0',
  task_page_title varchar(255) NOT NULL default '',
  task_user_id int(8) unsigned NOT NULL default '0',
  task_user_text varchar(255) NOT NULL default '',
  task_user_assigned int(8) unsigned NOT NULL default '0',
  task_status int(4) unsigned NOT NULL default '0',
  task_comment mediumtext NOT NULL,
  task_type int(4) unsigned NOT NULL default '0',
  task_timestamp varchar(14) binary NOT NULL default '',
  task_user_close int(8) unsigned NOT NULL default '0',
  task_timestamp_closed varchar(14) NOT NULL default '',
  PRIMARY KEY  (task_id),
  KEY task_page_id (task_page_id,task_status,task_type),
  KEY task_page_title (task_page_title)
) TYPE=InnoDB;

Known bugs:
* Both the "article" and "tasks" tabs are displayed as active when viewing the "tasks" tab
* sidebar task list for Monobook only?

*/

if( !defined( 'MEDIAWIKI' ) ) die();

# Integrating into the MediaWiki environment

$wgExtensionCredits['Tasks'][] = array(
        'name' => 'Tasks',
        'description' => 'An extension to manage tasks.',
        'author' => 'Magnus Manske'
);

$wgExtensionFunctions[] = 'wfTasksExtension';

# Misc hooks
$wgHooks['SkinTemplatePreventOtherActiveTabs'][] = 'wfTasksExtensionPreventOtherActiveTabs';
$wgHooks['SkinTemplateTabs'][] = 'wfTasksExtensionTab';
$wgHooks['UnknownAction'][] = 'wfTasksExtensionAction';
$wgHooks['ArticleSaveComplete'][] = 'wfTasksExtensionArticleSaveComplete';
$wgHooks['ArticleDeleteComplete'][] = 'wfTasksExtensionArticleDeleteComplete';
$wgHooks['SpecialMovepageAfterMove'][] = 'wfTasksExtensionAfterMove';
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfTasksExtensionAfterToolbox';
$wgHooks['ArticleViewHeader'][] = 'wfTaskExtensionHeaderHook';
$wgHooks['EditPage::showEditForm:initial'][] = 'wfTaskExtensionEditFormInitialHook';



# BEGIN logging functions
$wgHooks['LogPageValidTypes'][] = 'wfTasksAddLogType';
$wgHooks['LogPageLogName'][] = 'wfTasksAddLogName';
$wgHooks['LogPageLogHeader'][] = 'wfTasksAddLogHeader';
$wgHooks['LogPageActionText'][] = 'wfTasksAddActionText';

function wfTasksAddLogType( &$types ) { # Checked for HTML and MySQL insertion attacks
	if( !in_array( 'tasks', $types ) ) {
		$types[] = 'tasks';
	}
	return true;
}

function wfTasksAddLogName( &$names ) { # Checked for HTML and MySQL insertion attacks
	$names['tasks'] = 'tasks_logpage';
	return true;
}

function wfTasksAddLogHeader( &$headers ) { # Checked for HTML and MySQL insertion attacks
	$headers['tasks'] = 'tasks_logpagetext';
	return true;
}

function wfTasksAddActionText( &$actions ) { # Checked for HTML and MySQL insertion attacks
	$actions['tasks/tasks'] = 'tasks_logentry';
	return true;
}
# END logging functions


#_______________________________________________________________________________

/**
* Text adding function
*/
function wfTasksAddCache() { # Checked for HTML and MySQL insertion attacks
	global $wgMessageCache, $wgTasksAddCache;
	if( $wgTasksAddCache ) {
		return;
	}
	$wgTasksAddCache = true;
	$wgMessageCache->addMessages(
		array(
			'tasks_tab' => 'Tasks',
			'tasks_title' => "Tasks for \"$1\"",
			'tasks_form_new' => "Create new task",
			'tasks_form_comment' => "Comment",
			'tasks_error1' => "Task was not created: there is already such a task!",
			'tasks_ok1' => "New task has been created!",
			'tasks_create_header' => "Create a new task",
			'tasks_existing_header' => "Existing tasks",
			'tasks_existing_table_header' => "Task|Dates|Initial comment|Assignment/Actions/Page",
			'tasks_noone' => "noone",
			'tasks_assign_me' => "Assign myself",
			'tasks_unassign_me' => "Remove my assignment",
			'tasks_close' => "Close task",
			'tasks_wontfix' => "Won't fix",
			'tasks_reopen' => "Reopen task",
			'tasks_assignedto' => "Assigned to $1",
			'tasks_created_by' => "Created by $1",
			'tasks_discussion_page_link' => "Task discussion page",
			'tasks_closedby' => "Closed by $1",
			'tasks_assigned_myself_log' => "Self-assignment of task \"$1\"",
			'tasks_discussion_page_for' => "This task is for the page \"$1\". The list of all tasks for that page is $2.",
			'tasks_sidebar_title' => "Open tasks",
			'tasks_here' => "here",
			'tasks_returnto' => "You will be redirected now. If you have not been redirected in a few seconds, click $1.",
			'tasks_see_page_tasks' => "(tasks of this page)",
			'tasks_task_is_assigned' => "(assigned)",
			'tasks_plain_text_only' => "(plain text only)",
			
			'tasks_link_your_assignments' => "open assignments",
			'tasks_see_your_assignments' => "You currently have $1 open assignments. See your $2.",
			'tasks_my_assignments' => "Your current assignments",
			'tasks_table_header_page' => "Page",
			'tasks_you_have_no_assignments' => "You have no open assignments",
			'tasks_search_form_title' => "Search",
			'tasks_search_tasks' => "Tasks",
			'tasks_search_status' => "Status",
			'tasks_search_no_tasks_chosen_note' => "(No selection here will search all task types.)",
			'tasks_search_results' => "Search results",
			'tasks_previous' => "Previous",
			'tasks_next' => "Next",
			'tasks_sort' => "Sort",
			'tasks_ascending' => "Oldest first",
			'tasks_search_limit' => "10",
			
			'tasks_creation_tasks' => "5,6",
			'tasks_task_types' => "1:cleanup:Cleanup|2:wikify:Wikify|3:rewrite:Rewrite|4:delete:Delete|5:create:Create|6:write:Write",
			'tasks_status_open' => "Open",
			'tasks_status_assigned' => "Assigned",
			'tasks_status_closed' => "Closed",
			'tasks_status_wontfix' => "Won't fix",
			'tasks_status_bgcol_open' => "#FF9999",
			'tasks_status_bgcol_assigned' => "#FFF380",
			'tasks_status_bgcol_closed' => "#99FF99",
			'tasks_status_bgcol_wontfix' => "#9999FF",
			'tasks_action_open' => "Task \"$1\" opened.",
			'tasks_action_assigned' => "Task \"$1\" assigned.",
			'tasks_action_closed' => "Task \"$1\" closed.",
			'tasks_action_wontfix' => "Won't fix task \"$1\".",
			
			'tasks_logpage' => "Tasks log",
			'tasks_logpagetext' => 'This is a log of changes to tasks',
			'tasks_logentry' => 'For "[[$1]]"',
		)
	);
}


#___________________________________________________________________
# Hook functions

/**
* Display header on "Task:" pages (dummy hook for edit pages)
*/
function wfTaskExtensionEditFormInitialHook( &$editPage ) { # Checked for HTML and MySQL insertion attacks
	global $wgArticle;
	return wfTaskExtensionHeaderHook( $wgArticle );
}

/**
* Display header on "Task:" pages
*/
function wfTaskExtensionHeaderHook( &$article ) { # Checked for HTML and MySQL insertion attacks
	global $wgTasksNamespace, $wgOut, $wgUser, $wgTitle;
	$title = $article->getTitle();
	$ns = $title->getNamespace();
	if( $ns != $wgTasksNamespace && $ns != $wgTasksNamespace+1 ) {
		return true; # Doesn't concern us
	}
	
	$subtitle = "";
	$taskid = $title->getText();
	$taskid = explode( "(", $taskid );
	$taskid = explode( ")", array_pop( $taskid ) );
	$taskid = array_shift( $taskid );
	if( !is_numeric( $taskid ) ) {
		return true ; # Paranoia
	}
	$taskid = (int) $taskid;
	
	wfTasksAddCache();
	$st = new SpecialTasks;
	$task = "";
	$page_title = $st->get_title_from_task( $taskid, &$task );
	if( $task == "" ) {
		# No such task
		return true;
	}

	$sk =& $wgUser->getSkin();
	$returnto = urlencode( $wgTitle->getFullURL() );
	$link1 = $sk->makeLinkObj( $page_title );
	$link2 = $sk->makeLinkObj( $page_title, wfMsgHTML( 'tasks_here' ), "action=tasks" );
	$subtitle .= wfMsgHTML( 'tasks_discussion_page_for', $link1, $link2 );
	$subtitle .= "<br/>\n<table border='1' cellspacing='1' cellpadding='2'>\n" . 
				"<tr>" . wfTaskExtensionGetTableHeader() . "</tr>\n";
	$subtitle .= $st->get_task_table_row( $task, $page_title, false, $returnto );
	$subtitle .= "</table>\n";
	
	$subtitle = $wgOut->getSubtitle() . "<br/>" . $subtitle;
	$wgOut->setSubtitle( $subtitle );
	return true;
}

/**
* Return the header
*/
function wfTaskExtensionGetTableHeader( $with_title = false ) {
	$s = wfMsgHTML( 'tasks_existing_table_header' );
	if( $with_title ) {
		$s = wfMsgHTML( 'tasks_table_header_page' ) . "|" . $s;
	}
	$s = "<th>" . str_replace( "|", "</th><th>", $s ) . "</th>";
	return $s;
}

/**
* Display in sidebar
*/
function wfTasksExtensionAfterToolbox( &$tpl ) { # Checked for HTML and MySQL insertion attacks
	global $wgTitle;
	if( $wgTitle->isTalkPage() ) {
		# No talk pages please
		return;
	}
	if( $wgTitle->getNamespace() < 0 ) {
		# No special pages please
		return;
	}
	
	wfTasksAddCache();
	$st = new SpecialTasks;
	$tasks = $st->get_open_task_list( $wgTitle );
	if( count( $tasks ) == 0 ) {
		# No tasks	
		return;
	}

?>

			</ul>
		</div>
	</div>
	<div class="portlet" id="p-tasks">
		<h5><?php $tpl->msg('tasks_sidebar_title') ?></h5>
		<div class="pBody">
			<ul>
<?php
	foreach( $tasks as $task ) {
		$ttype = $st->get_task_type( $task->task_type );
?>
			<li id="task_sidebar_<?php echo $ttype ?>">
			<a href="<?php
				$nt = $st->get_task_discussion_page( $task );
				echo $nt->getLocalURL();
				?>"><?php
				echo $st->get_type_text( $ttype );
				?></a><?php
				if( $task->task_user_assigned != 0 ) {
					echo " " . wfMsgHTML( 'tasks_task_is_assigned' );
				}
				?></li>
<?php
		
	}
}

/**
* Catch page movement, fix internal task_page_title values
*/
function wfTasksExtensionAfterMove( &$special_page, &$old_title, &$new_title ) { # Checked for HTML and MySQL insertion attacks
	if( $new_title->isTalkPage() ) {
		# No tasks for talk pages, no need to bother the database...
		return false;
	}

	wfTasksAddCache();

	$st = new SpecialTasks;
	$st->rename_tasks_page( $old_title, $new_title );
	return false;
}


/**
* Catch article deletion, remove all tasks
*/
function wfTasksExtensionArticleDeleteComplete( &$article, &$user, $reason ) { # Checked for HTML and MySQL insertion attacks
	# return false ; # Uncomment this line to prevent deletion of tasks upon deletion of article
	wfTasksAddCache();
	$t = $article->getTitle();
	if( $t->isTalkPage() ) {
		# No tasks for talk pages, no need to bother the database...
		return false;
	}

	$st = new SpecialTasks;
	$st->delete_all_tasks( $t );
	return false;
}

/**
* Catch article creation, to close "create" tasks
*/
function wfTasksExtensionArticleSaveComplete( &$article, &$user, $text, $summary, $isminor, $watchthis, $something ) { # Checked for HTML and MySQL insertion attacks
	wfTasksAddCache();
	$t = $article->getTitle();
	if( $t->isTalkPage() ) {
		# No tasks for talk pages, no need to bother the database...
		return false;
	}
	
	$st = new SpecialTasks;
	$tasks = $st->get_tasks_for_page( $t, true );
	foreach( $tasks as $task ) {
		if( !$st->is_creation_task( $task->task_type ) ) {
			# Not a "create" task
			continue;
		}
		if( $st->is_closed( $task->task_status ) ) {
			# Not open
			continue;
		}
		$st->change_task_status( $task->task_id, 3 ); # "Closed"
		$st->set_new_article_id( $t );
		# Nothing more to do
		return false;
	}	
	return false;
}

/**
* Prevents other tabs shown as active
*/
function wfTasksExtensionPreventOtherActiveTabs( &$skin, &$prevent_active_tabs ) { # Checked for HTML and MySQL insertion attacks
	global $action;
	$prevent_active_tabs = ( $action == "tasks" );
}

/**
* Show the tab
*/
function wfTasksExtensionTab( &$skin, &$content_actions ) { # Checked for HTML and MySQL insertion attacks
	global $wgTitle, $action;
	if( $wgTitle->isTalkPage() ) {
		# No talk pages please
		return false;
	}
	if( $wgTitle->getNamespace() < 0 ) {
		# No special pages please
		return false;
	}

	wfTasksAddCache();
	$content_actions['tasks'] = array(
		'class' => ($action == 'tasks') ? 'selected' : false,
		'text' => wfMsgHTML('tasks_tab'),
		'href' => $wgTitle->getLocalUrl( "action=tasks" )
	);
}

/**
* This is where the action is :-)
*/
function wfTasksExtensionAction( $action, $article ) { # Checked for HTML and MySQL insertion attacks
	if( $action != 'tasks' ) {
		# Not my kind of action!
		return true;
	}
	
	wfTasksAddCache();
	
	$t = new SpecialTasks;
	$t->page_management( $article->getTitle() );
	
	return false;
}

#_____________________________________________________________________________

/**
* The special page
*/
function wfTasksExtension() { # Checked for HTML and MySQL insertion attacks
	global $IP, $wgMessageCache;
	wfTasksAddCache();

	$wgMessageCache->addMessage( 'tasks', 'Tasks' );

	require_once "$IP/includes/SpecialPage.php";

	class SpecialTasks extends SpecialPage {
	
		var $status_types = array(
			1 => 'open',
			2 => 'assigned',
			3 => 'closed',
			4 => 'wontfix'
		);
		var $task_types ; # e.g., 0 => 'cleanup'
		var $task_types_text ; # e.g., 'cleanup' => 'Clean up'
		var $creation_tasks ; # e.g., ( 1, 2, 3 )
	
		/**
		* Constructor
		*/
		function SpecialTasks() { # Checked for HTML and MySQL insertion attacks
			SpecialPage::SpecialPage( 'Tasks' );
			$this->includable( true );
			$this->update_types();
		}
		
		function get_task_type( $num ) { # Checked for HTML and MySQL insertion attacks
			if( !isset( $this->task_types[$num] ) ) {
				wfDebug( "Tasks: get_task_type was passed illegal num : " . $type_key . " (out of range)\n" );
				return 0;
			}
			return $this->task_types[$num];
		}
		
		/**
		* Updates task_types and creation_tasks from wfMsg
		*/
		function update_types() { # Checked for HTML and MySQL insertion attacks
			wfTasksAddCache();
			
			$this->task_types = array();
			$s = wfMsgHTML( 'tasks_task_types' ); # HTML safe
			$s = explode( "|", $s );
			foreach( $s as $l ) {
				$l = explode( ":", trim( $l ), 3 );
				if( count( $l ) != 3 ) {
					# Invalid line
					continue;
				}
				if( !is_numeric( $l[0] ) ) {
					# First value needs to be a number
					continue;
				}
				if( $l[0] < 1 ) {
					# First value needs to be larger than zero
					continue;
				}
				$this->task_types[trim($l[0])] = trim($l[1]);
				$this->task_types_text[trim($l[1])] = trim($l[2]);
			}
			
			$this->creation_tasks = array();
			$s = wfMsgHTML( 'tasks_creation_tasks' );
			$s = explode( ",", $s );
			foreach( $s as $l ) {
				$l = trim( $l );
				if( $l == "" ) {
					continue;
				}
				if( !is_numeric( $l ) ) {
					continue;
				}
				$this->creation_tasks[] = trim( $l );
			}
			
		}
		
		function get_type_text( $type_key ) { # Checked for HTML and MySQL insertion attacks
			if( !isset( $this->task_types_text[$type_key] ) ) {
				wfDebug( "Tasks: get_type_text was passed illegal type_key : " . $type_key . " (out of range)\n" );
				return "";
			}
			return $this->task_types_text[$type_key];
		}

		function is_creation_task( &$task_type ) { # Checked for HTML and MySQL insertion attacks
			return in_array( $task_type, $this->creation_tasks );
		}
		
		function is_open( $status ) { # Checked for HTML and MySQL insertion attacks
			if( $status == 1 || $status == 2 )
				return true;
			return false;
		}

		function is_closed( $status ) { # Checked for HTML and MySQL insertion attacks
			return !$this->is_open( $status );
		}
		
		/**
		* Takes a title and a list of existing tasks, and decides which new tasks can be created.
		* There's no point in having a dozen "wikify" tasks for a single article, now is there? :-)
		*/
		function get_valid_new_tasks( &$title, &$tasks ) { # Checked for HTML and MySQL insertion attacks
			$exists = $title->exists();
			$tasks = $this->get_tasks_for_page( $title );
			$new_tasks = array();
			$tg = array();
			
			foreach( $tasks as $t ) {
				# Assemble types; if multiple of one type, assemble open ones
				if( !isset( $tg[$t->task_type] ) || $this->is_open( $t->task_status ) ) {
					$tg[$t->task_type] = $t->task_status;
				}
			}
			
			for( $a = min( array_keys( $this->task_types ) ); $a <= max( array_keys( $this->task_types ) ); $a++ ) {
				if( $exists == $this->is_creation_task( $a ) ) {
					# Creation task and existence exclude each other
					continue;
				}
				if( isset( $tg[$a] ) && $this->is_open( $tg[$a] ) ) {
					# Task exists and is not closed
					continue;
				}
				$new_tasks[$a] = $this->get_task_type( $a );
			}
			return $new_tasks;
		}

		/**
		* The form for creating a new task from a form ("tasks" tab)
		*/
		function create_from_form( $title ) { # Checked for HTML and MySQL insertion attacks
			global $wgRequest, $wgUser;
			if( $wgRequest->getText( 'create_task', "" ) == "" ) {
				# No form
				return "";
			}
			
			$out = "";
			$tasks = array();
			$type = (int) $wgRequest->getText( 'type', 0 );
			if( $type == 0 ) {
				# Invalid type
				return "";
			}
			$comment = $wgRequest->getText( 'text', "" ); # Not evaluated here; stored in database through safe database function
			$new_tasks = $this->get_valid_new_tasks( $title, $tasks );
			if( !isset( $new_tasks[$type] ) ) {
				# Trying to create a task that isn't available
				$out .= "<p>" . wfMsgHTML('tasks_error1') . "</p>";
			} else {
				$dbw =& wfGetDB( DB_MASTER );
				$dbw->insert( 'tasks',
					array(
						'task_page_id'       => $title->getArticleID(),
						'task_page_title'    => $title->getPrefixedDBkey(),
						'task_user_id'       => $wgUser->getID(),
						'task_user_text'     => $wgUser->getName(),
						'task_user_assigned' => '0', # default: No user assigned
						'task_status'        => $this->get_status_number( 'open' ),
						'task_comment'       => $comment,
						'task_type'          => $type,
						'task_timestamp'     => $dbw->timestamp()
						) );
				$out .= "<p>" . wfMsgHTML( 'tasks_ok1' ) . "</p>";
			}
			return $out;
		}
		
		/**
		* For a list of tasks, get a single table row
		* This function is heavy on output!
		*/
		function get_task_table_row( &$task, &$title, $show_page = false, $returnto = "" ) { # Checked for HTML and MySQL insertion attacks
			global $wgContLang, $wgUser, $wgTasksNamespace, $wgExtraNamespaces;
			$out = "";
			$sk =& $wgUser->getSkin();
			$ct = $wgContLang->timeanddate( $task->task_timestamp ); # Time object from string of digits
			$cu = Title::makeTitleSafe( NS_USER, $task->task_user_text ); # Safe user name
			$comment = htmlspecialchars( $task->task_comment ); # Safe user comment, no HTML allowed
			$comment = str_replace( "\n", "<br/>", $comment ); # display newlines as they were in the edit box
			$status = $task->task_status; # Integer
			$tid = $task->task_id; # Integer
			$ttype = $this->get_type_text( $this->get_task_type( $task->task_type ) ); # Will catch illegal types and wfDebug them
			if( $returnto != "" ) {
				$returnto = "&returnto=" . urlencode( $returnto );
			}

			$out .= "<tr>";
			if( $show_page ) {
				$out .= "<td align='left' valign='top'>";
				$out .= $sk->makeLinkObj( $title );
				$out .= "<br/>";
				$out .= $sk->makeLinkObj( $title, wfMsgHTML('tasks_see_page_tasks'), "action=tasks" );
				$out .= "</td>";
			}
			$out .= "<td valign='top' align='left' nowrap bgcolor='" . wfMsgHTML('tasks_status_bgcol_'.$this->status_types[$status]) . "'>";
			$out .= "<b>" . $ttype . "</b><br/><i>";
			$out .= wfMsgHTML( 'tasks_status_' . $this->status_types[$status] );
			$out .= "</i></td>";
			$out .= "<td align='left' valign='top' nowrap>";
			$out .= wfMsgHTML( 'tasks_created_by', $sk->makeLinkObj( $cu, htmlentities( $task->task_user_text ) ) );
			$out .= "<br/>{$ct}";

			# Closing information
			if( $task->task_user_close != 0 && $this->is_closed( $status ) ) {
				$user_close = new User;
				$user_close->setID( $task->task_user_close );
				$uct = Title::makeTitleSafe( NS_USER, $user_close->getName() ); # Assigned user title
				$out .= "<br/>" . wfMsgHTML( 'tasks_closedby', $sk->makeLinkObj( $uct, htmlentities( $user_close->getName() ) ) );
				if( $task->task_timestamp_closed != "" ) {
					$out .= "<br/>" . $wgContLang->timeanddate( $task->task_timestamp_closed ); # Time object from string of digits
				}
			}
			$out .= "</td>";

			$out .= "<td align='left' valign='top'>" . $comment . "</td>" ; # Comment is HTML-stripped
			$out .= "<td align='left' valign='top'>";
			if( $task->task_user_assigned == 0 ) {
				# Noone is assigned this task
				$out .= wfMsgHTML( 'tasks_assignedto', wfMsgHTML( 'tasks_noone' ) );
			} else {
				# Someone is assigned this task
				$au = new User(); # Assigned user
				$au->setID( $task->task_user_assigned );
				$aut = Title::makeTitleSafe( NS_USER, $au->getName() ); # Assigned user title
				$out .= wfMsgHTML( 'tasks_assignedto', $sk->makeLinkObj( $aut, htmlentities( $au->getName() ) ) );
			}
			if( $wgUser->isLoggedIn() ) {
				$txt = array();
				if( $this->is_open( $status ) ) {
					# Assignment
					if( $wgUser->getID() != $task->task_user_assigned ) {
						# Assign myself
						$txt[] = $sk->makeLinkObj( $title,
							wfMsgHTML( 'tasks_assign_me' ),
							"action=tasks&mode=assignme&taskid={$tid}{$returnto}" ); # tid is integer, returnto is safe
					} else {
						# Unassign myself
						$txt[] = $sk->makeLinkObj( $title,
							wfMsgHTML( 'tasks_unassign_me' ),
							"action=tasks&mode=unassignme&taskid={$tid}{$returnto}" ); # tid is integer, returnto is safe
					}
				}
				if( $this->is_open( $status ) ) {
					# Open or assigned
					$txt[] = $sk->makeLinkObj( $title, wfMsgHTML( 'tasks_close' ), "action=tasks&mode=close&taskid={$tid}{$returnto}" );
					$txt[] = $sk->makeLinkObj( $title, wfMsgHTML( 'tasks_wontfix' ), "action=tasks&mode=wontfix&taskid={$tid}{$returnto}" );
				} else if( $this->get_task_type( $task->task_type ) != 'create' ) {
					# Closed or wontfix, can reopen (maybe)
					$txt[] = $sk->makeLinkObj( $title, wfMsgHTML( 'tasks_reopen' ), "action=tasks&mode=reopen&taskid={$tid}{$returnto}" );
				}
				
				if( count( $txt ) > 0 )
					$out .= "<br/>" . implode( " - ", $txt );

			}
			$tdp = $this->get_task_discussion_page( $task );
			$out .= "<br/>" . $sk->makeLinkObj( $tdp, wfMsgHTML('tasks_discussion_page_link') );
			$out .="</td>";
			$out .= "</tr>";
			return $out;
		}

		/**
		 * @param Task $task
		 * @return Title
		 */
		function get_task_discussion_page( &$task ) { # Checked for HTML and MySQL insertion attacks
			global $wgTasksNamespace;
			$ttype = $this->get_type_text( $this->get_task_type( $task->task_type ) ); # Illegal values will be caught on the way
			return Title::makeTitle( $wgTasksNamespace, $ttype . ' (' . $task->task_id . ")" );
		}

		/**
		* On the "tasks" tab, show the list of existing tasks for that article
		*/
		function show_existing_tasks( &$title, &$tasks ) { # Checked for HTML and MySQL insertion attacks
			$out = "";
			foreach( $tasks as $task ) {
				$out .= $this->get_task_table_row( $task, $title ); # Assumed safe
			}
			if( $out == "" ) {
				return "";
			}

			$out = "<h2>" . wfMsgHTML( 'tasks_existing_header' ) . "</h2>\n" .
				"<table border='1' cellspacing='1' cellpadding='2'>" . 
				"<tr>" . wfTaskExtensionGetTableHeader() . "</tr>" .
				$out . "</table>";
			return $out;
		}
		
		/**
		* Checks if there's a "mode" set in the URL of the current page (performs changes on tasks, like assigning or closing them)
		*/
		function check_mode( $title ) { # Checked for HTML and MySQL insertion attacks
			global $wgUser, $wgRequest;
			$mode = trim( $wgRequest->getText( 'mode', "" ) );
			$taskid = (int) $wgRequest->getText( 'taskid', "" );
			if( $mode == "" || $taskid == "" ) {
				# Not correct
				return "";
			}
			if( !is_numeric( $taskid ) ) {
				# Paranoia
				return "";
			}
			if( !$wgUser->isLoggedIn() ) {
				# Needs to be logged in
				return;
			}
			
			$out = "";
			$fname = "Tasks:check_mode";
			$dbw =& wfGetDB( DB_MASTER );
			if( $mode == 'assignme' ||  $mode == 'unassignme' ) {
				$conditions = array( "task_id" => $taskid );
				$user_id = $wgUser->getId() ; # Assign
				if( $mode == 'unassignme' ) {
					# Unassign me; this can be invoked for every user by editing the URL!
					$user_id = 0;
				}
				$do_set = array( # SET
					'task_user_assigned' => $user_id, # Coming from $wgUser, so assumed safe
					'task_status' => ( $mode == "assignme" )
						? $this->get_status_number( 'assigned' )
						: $this->get_status_number( 'open' ), # Integer
					);
				$dbw->update( 'tasks',
					$do_set,
					$conditions,
					$fname );

				$title = $this->get_title_from_task( $taskid, $task );
				$act = wfMsgHTML( 'tasks_assigned_myself_log',
					$this->get_type_text( $this->get_task_type( $task->task_type ) ) );
				$log = new LogPage( 'tasks' );
				$log->addEntry( 'tasks', $title, $act );
			} elseif( $mode == 'close' || $mode == 'wontfix' || $mode == 'reopen' ) {
				if( $mode == 'reopen' ) {
					$mode = "open";
				}
				if( $mode == 'close' ) {
					$mode = "closed";
				}
				$new_status = $this->get_status_number( $mode );
				$this->change_task_status( $taskid, $new_status );
			} else {
				# Unknown mode
				return "";
			}
			return $out;
		}
		
		/**
		* Returns the number for the status
		*/
		function get_status_number( $status ) { # Checked for HTML and MySQL insertion attacks
			foreach( $this->status_types as $k => $v ) {
				if( $v == $status ) {
					return $k;
				}
			}
			# Invalid status
			return 0;
		}
		
		/**
		* Changes the status of a task, performs some associated cleanup, and logs the action
		*/
		function change_task_status( $taskid, $new_status ) { # Checked for HTML and MySQL insertion attacks
			global $wgUser;
			$fname = "Tasks:change_task_status";
			$dbw =& wfGetDB( DB_MASTER );
			
			if( !is_numeric( $new_status ) ) {
				# Paranoia
				return;
			}
			if( !is_numeric( $taskid ) ) {
				# Paranoia
				return;
			}
			
			$as = array( 'task_status' => $new_status ); # What to chenge
			$aw = array( 'task_id' => $taskid ); # Where to change it
			
			if( $this->is_closed( $new_status ) ) {
				# When closing, set closing user ID, and reset assignment
				$as['task_user_close'] = $wgUser->getID();
				$as['task_user_assigned'] = 0;
				$as['task_timestamp_closed'] = $dbw->timestamp(); # Assumed safe
			} elseif( $new_status == $this->get_status_number( 'open' ) ) {
				# Change to "open", no assigned user or closing user
				$as['task_user_assigned'] = 0;
				$as['task_user_close'] = 0;
				$as['task_timestamp_closed'] = "";
			}
			
			$dbw->update( 'tasks',
				$as, # SET
				$aw, # WHERE
				$fname );

			# Logging
			$title = $this->get_title_from_task( $taskid, $task );
			$act = wfMsgHTML( 'tasks_action_' . $this->status_types[$new_status],
				$this->get_type_text( $this->get_task_type( $task->task_type ) ) );
			$log = new LogPage( 'tasks' );
			$log->addEntry( 'tasks', $title, $act );
		}
		
		/**
		* Returns the list of active tasks for this page, for display in the sidebar
		*/
		function get_open_task_list( &$title ) { # Checked for HTML and MySQL insertion attacks
			$tasks = $this->get_tasks_for_page( $title );
			$ret = array();
			foreach( $tasks as $task ) {
				if( $this->is_open( $task->task_status ) ) {
					$ret[$this->get_type_text( $this->get_task_type( $task->task_type ) )] = $task;
				}
			}
			ksort( $ret );
			return $ret;
		}

		/**
		* Returns the title object for a task, and the task data through reference
		*/
		function get_title_from_task( $task_id, &$task ) { # Checked for HTML and MySQL insertion attacks
			$task = $this->get_task_from_id( $task_id );
			if( $task->task_page_id == 0 ) { # Non-existing page
				$title = Title::newFromDBkey( $task->task_page_title );
			} else { # Existing page
				$title = Title::newFromID( $task->task_page_id );
			}
			return $title;
		}
		
		/**
		* Returns a single task by its ID
		*/
		function get_task_from_id( $task_id ) { # Checked for HTML and MySQL insertion attacks
			if( !is_numeric( $task_id ) ) {
				# Paranoia
				return null;
			}
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*',
					/* WHERE  */ array( "task_id" => $task_id )
			);
			$task = $dbr->fetchObject( $res );
			$dbr->freeResult( $res );
			return $task;
		}
		
		/**
		* Sets the article ID (on page creation)
		*/
		function set_new_article_id( &$title ) { # Checked for HTML and MySQL insertion attacks
			$fname = "Tasks:set_new_article_id";
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->update( 'tasks',
				array( 'task_page_id' => $title->getArticleID() ), # SET
				array( "task_page_title" => $title->getPrefixedDBkey() ), # WHERE
				$fname );
		}

		/**
		* Deletes all tasks associated with an article; done on article deletion
		*/
		function delete_all_tasks( &$title ) { # Checked for HTML and MySQL insertion attacks
			$fname = "Tasks:delete_all_tasks";
			if( $title->getArticleID() == 0 ) {
				$conds = array( 'task_page_title' => $title->getPrefixedDBkey() );
			} else {
				$conds = array( 'task_page_id' => $title->getArticleID() );
			}
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->delete( 'tasks',
				$conds,
				$fname );
		}
		
		/**
		* Called for page moves
		*/
		function rename_tasks_page( $old_title, $new_title ) { # Checked for HTML and MySQL insertion attacks
			$fname = "Tasks:rename_tasks_page";
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->update( 'tasks',
				array( 'task_page_title' => $new_title->getPrefixedDBkey() ), # SET
				array( "task_page_title" => $old_title->getPrefixedDBkey() ), # WHERE
				$fname );			
		}
		
		/**
		* THIS IS THE MAIN FUNCTION FOR THE TAB-BASED INTERFACE
		*/
		function page_management( $title ) { # Checked for HTML and MySQL insertion attacks
			if( $title->isTalkPage() ) {
				# No tasks for talk pages, no need to bother the database...
				return;
			}
			
			global $wgOut, $action, $wgRequest, $wgUser, $wgTitle;
			$out = "";
			$tasks = array();
			$wgOut->setSubtitle( wfMsgHTML( 'tasks_title', $title->getPrefixedText() ) );
			
			# Create from form
			$out .= $this->create_from_form( $title );
			
			# Check for mode
			$out .= $this->check_mode( $title );
			
			# Get list of tasks that can be created
			$new_tasks = $this->get_valid_new_tasks( $title, $tasks );
			
			# Show task creation form, if tasks can be created
			$out .= $this->generate_form( $new_tasks );
			
			# Existing tasks
			$out .= $this->show_existing_tasks( $title, $tasks );

			# And ... out!
			$returnto = urldecode( $wgRequest->getText( 'returnto', "" ) );
			if( $returnto != "" ) {
				# Forward to other page
				$skin =& $wgUser->getSkin();
				$link = $skin->makeExternalLink( $returnto, wfMsgHTML( 'tasks_here' ) );
				
				# Paranoia
				$url1 = $wgTitle->getFullURL();
				$url1 = explode( "/", $url1 );
				$url1 = $url1[0] . "/" . $url1[1] ."/" . $url1[2];
				$url2 = explode( "/", $returnto );
				$url2 = $url2[0] . "/" . $url2[1] ."/" . $url2[2];

				if( $url1 != $url2 ) {
					# In the domain of this wiki; otherwise, no redirect
					return;
				}

				$wgOut->addMeta( 'http:Refresh', '0;url=' . $returnto );
				$msg = wfMsgHTML( 'tasks_returnto', $link );
				$wgOut->addHTML( $msg );
			} else {
				$this->setHeaders();
				$wgOut->addHtml( $out );
			}
		}

		/**
		* Generates a form for creating a new task
		*/
		function generate_form( &$new_tasks ) { # Checked for HTML and MySQL insertion attacks
			if( count( $new_tasks ) == 0 ) {
				return "";
			}
			$out = "<h2>" . wfMsgHTML( 'tasks_create_header' ) . "</h2>\n" ;
			$out .= "<form method='post'>";
			$out .= "<table border='0' width='100%'><tr><td valign='top' nowrap><b>";
			$out .= wfMsgHTML( 'tasks_form_new' );
			$out .= "</b></td><td width='100%'>";
			$out .= "<select name='type'>";
			$o = array();
			foreach( $new_tasks as $k => $v ) {
				$o[$v] = "<option value='{$k}'>" . $this->get_type_text( $v ) . "</option>";
			}
			ksort( $o );
			$out .= implode( "", $o );
			$out .= "</select>";
			$out .= "<input type='submit' name='create_task' value='" . wfMsgHTML( 'ok' ) . "'/>";
			$out .= "</td></tr><tr><td valign='top' nowrap>";
			$out .= "<b>" . wfMsgHTML( 'tasks_form_comment' ) . "</b>";
			$out .= "<br/>" . wfMsgHTML( 'tasks_plain_text_only' );
			$out .= "</td><td>";
			$out .= "<textarea name='text' rows=5 cols=20 style='width:100%'></textarea>";
			$out .= "</td></tr></table>";
			$out .= "</form>";
			return $out;
		}
		
		/**
		* Returns the exisiting tasks for a single page
		*/
		function get_tasks_for_page( &$title, $force_dbtitle = false ) { # Checked for HTML and MySQL insertion attacks
			$dbr =& wfGetDB( DB_SLAVE );
			$id = $title->getArticleID();

			if( $id == 0 || $force_dbtitle ) {
				$conds = array( 'task_page_title' => $title->getPrefixedDBkey() );
			} else {
				$conds = array( 'task_page_id' => $id );
			}

			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*',
					/* WHERE  */ $conds
			);
			
			$ret = array();
			while( $line = $dbr->fetchObject( $res ) ) {
				$ret[$line->task_timestamp.":".$line->task_id] = $line;
			}
			$dbr->freeResult($res);
			krsort( $ret );
			return $ret;
		}
		
		function get_assigned_tasks( $userid ) { # Checked for HTML and MySQL insertion attacks
			if( !is_numeric( $userid ) ) {
				# Paranoia
				return null;
			}
		
			$dbr =& wfGetDB( DB_SLAVE );

			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*',
					/* WHERE  */ array( 'task_user_assigned' => $userid )
			);
			
			$ret = array();
			while( $line = $dbr->fetchObject( $res ) ) {
				$ret[$line->task_timestamp.":".$line->task_id] = $line;
			}
			$dbr->freeResult( $res );
			krsort( $ret );
			return $ret;
		}
	
		/**
		* Special page main function
		*/
		function execute( $par = null ) { # Checked for HTML and MySQL insertion attacks
			global $wgOut, $wgRequest, $wgUser, $wgTitle;
			$fname = "Special::Tasks:execute";

			$out = "";
			$mode = trim( $wgRequest->getText( 'mode', "" ) );
			$skin =& $wgUser->getSkin();
			$dbr =& wfGetDB( DB_SLAVE );
			
			# Assignments
			if( $wgUser->isLoggedIn() ) {
				if( $mode == 'myassignments' ) {
					# Show my assignments
					$tasks = $this->get_assigned_tasks( $wgUser->getId() );
					if( count( $tasks ) == 0 ) {
						$out .= "<p>" . wfMsgHTML( 'tasks_you_have_no_assignments' ) . "</p>";
					} else {
						$out .= "<h2>" . wfMsgHTML( 'tasks_my_assignments' ) . "</h2>\n";
						$out .= "<br/><table border='1' cellspacing='1' cellpadding='2'>" . 
							"<tr>" . wfTaskExtensionGetTableHeader( true ) . "</tr>";
						foreach( $tasks as $task ) {
							$page_title = $this->get_title_from_task( $task->task_id, $task );
							$returnto = $wgTitle->getFullURL( "mode=myassignments" );
							$out .= $this->get_task_table_row( $task, $page_title, true, $returnto );
						}
						$out.= "</table>";
					}
				} else { # default
					$res = $dbr->select(
							/* FROM   */ 'tasks',
							/* SELECT */ ' COUNT(task_id) AS num',
							/* WHERE  */ array( "task_user_assigned" => $wgUser->getId() ),
							/* FNAME */ $fname
					);
					$tasks = array();
					$data = $dbr->fetchObject( $res );
					$dbr->freeResult( $res );
					if( !isset ( $data ) || !isset ( $data->num ) ) {
						# Paranoia dummy
						$data->num = 0;
					}

					$specialTasks = Title::makeTitle( NS_SPECIAL, 'Tasks' );
					$link = $skin->makeLinkObj( $specialTasks,
						wfMsgHTML( 'tasks_link_your_assignments' ), "mode=myassignments" );
					$out .= "<p>";
					if( $data->num == 0 ) {
						$out .= wfMsgHTML( 'tasks_you_have_no_assignments' ) . ".";
					} else {
						$out .= wfMsgHTML( 'tasks_see_your_assignments', $data->num, $link ) . "</p>";
					}
				}
			}
			
			# Read former form
			$task_type = array();
			$status_type = array( 1 => 1 ) ; # Default : open tasks
			if( isset( $_POST['task_type'] ) ) {
				$task_type = $_POST['task_type'];
			}
			if( isset( $_POST['status_type'] ) ) {
				$status_type = $_POST['status_type'];
			}
			$ascending = $wgRequest->getText( 'ascending', "" );
			
			if( !is_array( $status_type ) ) {
				return;
			}
			if( !is_array( $task_type ) ) {
				return;
			}
			if( $ascending != "" && $ascending != "1" ) {
				return "";
			}

			$out .= "<form method='post' action='" . $wgTitle->getLocalURL() . "'>";

			# Search results
			if( $wgRequest->getText( 'doit', "" ) . $wgRequest->getText( 'prev', "" ) . $wgRequest->getText( 'next', "" ) != "" ) {
				# Did we search?
				$search_tasks = array_keys( $task_type );
				if( count( $search_tasks ) == 0 ) {
					# No choice => search all
					$search_tasks = array_keys( $this->task_types );
				}
				$search_status = array_keys( $status_type );
				if( count( $search_status ) == 0 ) {
					# No choice => search all
					$search_status = array_keys( $this->status_types );
				}
					
				$limit = wfMsgHTML( 'tasks_search_limit' );
				if( !is_numeric( $limit ) ) {
					return "";
				}
				$offset = $wgRequest->getText( 'offset', "0" );
				if( !is_numeric( $offset ) ) {
					return "";
				}
				if( $wgRequest->getText( 'next', "" ) != "" ) {
					$offset += $limit;
				}
				if( $wgRequest->getText('prev', "") != "" && $offset >= $limit ) {
					$offset -= $limit;
				}

				# Search
				$conds = array(
					"task_type" => $search_tasks,
					"task_status" => $search_status,
				);
				$options = array(
					"LIMIT" => $limit,
					"OFFSET" => $offset,
					"ORDER BY" => "task_timestamp" . ( $ascending == "1" ? " DESC" : "" ),
				);
				$res = $dbr->select(
						/* FROM   */ 'tasks',
						/* SELECT */ '*',
						/* WHERE  */ $conds,
						/* FNAME */ $fname,
						/* OPTIONS */$options
				);
				$tasks = array();
				while( $line = $dbr->fetchObject( $res ) ) {
					$tasks[] = $line;
				}
				$dbr->freeResult( $res );
				
				if( count( $tasks ) > 0 ) {
					$out .= "<h2>" . wfMsgHTML( 'tasks_search_results' ) . "</h2>\n";
					# Last/next form
					if( $offset >= $limit ) {
						$out .= "<input type='submit' name='prev' value='" .
							wfMsgHTML('tasks_previous') . "' /> ";
					}
					$out .= ($offset + 1) . " .. " . ($offset + count( $tasks )) . " ";
					if( count( $tasks ) >= $limit ) {
						$out .= "<input type='submit' name='next' value='" .
							wfMsgHTML( 'tasks_next' ) . "' />";
					}
					$out .= "<input type='hidden' name='offset' value='{$offset}' />";
					$out .= "<br/><table border='1' cellspacing='1' cellpadding='2'>" . 
						"<tr>" . wfTaskExtensionGetTableHeader( true ) . "</tr>";
					$returnto = $wgTitle->getFullURL(); # Return to this page
					foreach( $tasks as $task ) {
						$page_title = $this->get_title_from_task( $task->task_id, $task );
						$out .= $this->get_task_table_row( $task, $page_title, true, $returnto );
					}
					$out .= "</table>";
				}
			}
			
			# Search form
			$out .= "<h2>" . wfMsgHTML( 'tasks_search_form_title' ) . "</h2>";
			$out .= "<table border=0>";
			$out .= "<tr><th align='left'>" . wfMsgHTML( 'tasks_search_tasks' ) . "</th>";
			$out .= "<td>";
			foreach( $this->task_types as $k => $v ) {
				$checked = isset( $task_type[$k] ) ? "checked " : "";
				$out .= "<input type='checkbox' name='task_type[{$k}]' value='1' {$checked}/>" .
					$this->get_type_text( $v ) . " ";
			}
			$out .= wfMsgHTML( 'tasks_search_no_tasks_chosen_note' );
			$out .= "</td>";
			$out .= "</tr><tr><th align='left'>" . wfMsgHTML( 'tasks_search_status' ) . "</th>";
			$out .= "<td>";
			foreach( $this->status_types as $k => $v ) {
				$checked = isset( $status_type[$k] ) ? "checked " : "";
				$out .= "<input type='checkbox' name='status_type[{$k}]' value='1' {$checked}/>" .
					wfMsgHTML( 'tasks_status_' . $v ) . " ";
			}
			$out .= "</td></tr>\n<tr><th>";
			$out .= wfMsgHTML('tasks_sort') . "</th><td>";
			$out .= "<input type='checkbox' name='ascending' value='1'";
			if( $ascending == "1" ) {
				$out .= "checked";
			}
			$out .= " />" . wfMsgHTML( 'tasks_ascending' );
			$out .= "</td></tr></table>";
			$out .= "<input type='submit' name='doit' value='" . wfMsgHTML( 'search' ) . "' />";
			$out .= "</form>";

			# and ... out!
			$this->setHeaders();
			$wgOut->addHtml( $out );
		}
	} # end of class

	SpecialPage::addPage( new SpecialTasks );
}


?>
