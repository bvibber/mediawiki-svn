<?
/*
To activate, put something like this in your LocalSettings.php:
	$wgTasksNamespace = 200 ;
	$wgExtraNamespaces[$wgTasksNamespace] = "Task" ;
	$wgExtraNamespaces[$wgTasksNamespace+1] = "Task_Talk" ;
	include ( "extensions/Tasks.php" ) ;

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

if (!defined('MEDIAWIKI')) die();

# Integrating into the MediaWiki environment

$wgExtensionCredits['Tasks'][] = array(
        'name' => 'Tasks',
        'description' => 'An extension to manage tasks.',
        'author' => 'Magnus Manske'
);

$wgExtensionFunctions[] = 'wfTasksExtension' ;

# Misc hooks
$wgHooks['SkinTemplatePreventOtherActiveTabs'][] = 'wfTasksExtensionPreventOtherActiveTabs' ;
$wgHooks['SkinTemplateTabs'][] = 'wfTasksExtensionTab' ;
$wgHooks['UnknownAction'][] = 'wfTasksExtensionAction' ;
$wgHooks['ArticleSaveComplete'][] = 'wfTasksExtensionArticleSaveComplete' ;
$wgHooks['ArticleDeleteComplete'][] = 'wfTasksExtensionArticleDeleteComplete' ;
$wgHooks['SpecialMovepageAfterMove'][] = 'wfTasksExtensionAfterMove' ;
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfTasksExtensionAfterToolbox' ;
$wgHooks['ArticleViewHeader'][] = 'wfTaskExtensionHeaderHook' ;
$wgHooks['EditPage::showEditForm:initial'][] = 'wfTaskExtensionEditFormInitialHook' ;



# BEGIN logging functions
$wgHooks['LogPageValidTypes'][] = 'wfTasksAddLogType';
$wgHooks['LogPageLogName'][] = 'wfTasksAddLogName';
$wgHooks['LogPageLogHeader'][] = 'wfTasksAddLogHeader';
$wgHooks['LogPageActionText'][] = 'wfTasksAddActionText';

function wfTasksAddLogType( &$types ) {
	if ( !in_array( 'tasks', $types ) )
		$types[] = 'tasks';
	return true;
}

function wfTasksAddLogName( &$names ) {
	$names['tasks'] = 'tasks_logpage';
	return true;
}

function wfTasksAddLogHeader( &$headers ) {
	$headers['tasks'] = 'tasks_logpagetext';
	return true;
}

function wfTasksAddActionText( &$actions ) {
	$actions['tasks/tasks'] = 'tasks_logentry';
	return true;
}
# END logging functions


#_______________________________________________________________________________

/**
* Text adding function
*/
function wfTasksAddCache () {
	global $wgMessageCache , $wgTasksAddCache ;
	if ( $wgTasksAddCache ) return ;
	$wgTasksAddCache = true ;
	$wgMessageCache->addMessages(
		array(
			'tasks_tab' => 'Tasks',
			'tasks_title' => "Tasks for \"$1\"",
			'tasks_form_new' => "Create new task",
			'tasks_form_comment' => "Comment",
			'tasks_error1' => "Task was not created: there is already such a task!<br/>",
			'tasks_ok1' => "New task has been created!<br/>",
			'tasks_create_header' => "Create a new task",
			'tasks_existing_header' => "Existing tasks",
			'tasks_existing_table_header' => "<th>Task</th><th>Dates</th><th>Initial comment</th><th>Assignment/Actions/Page</th>",
			'tasks_noone' => "noone",
			'tasks_assign_me' => "Assign myself",
			'tasks_unassign_me' => "Remove my assignment",
			'tasks_close' => "<a href=\"$1\">Close task</a>",
			'tasks_wontfix' => "<a href=\"$1\">Won't fix</a>",
			'tasks_reopen' => "<a href=\"$1\">Reopen task</a>",
			'tasks_assignedto' => "Assigned to $1",
			'tasks_created_by' => "Created by $1",
			'tasks_discussion_page_link' => "Task discussion page",
			'tasks_closedby' => "Closed by $1",
			'tasks_assigned_myself_log' => "Self-assignment of task \"$1\"",
			'tasks_discussion_page_for' => "This task is for the page \"$1\". The list of all tasks for that page is $2.",
			'tasks_sidebar_title' => "Open tasks",
			'tasks_here' => "here",
			
			'tasks_link_your_assignments' => "your assignments",
			'tasks_see_your_assignments' => "See $1.",
			'tasks_my_assignments' => "Your current assignments",
			'tasks_table_header_page' => "<th>Page</th>",
			'tasks_you_have_no_assignments' => "You have currently no assignments",
			
			'tasks_creation_tasks' => "5,6",
			'tasks_task_types' => "1:cleanup:Cleanup|2:wikify:Wikify|3:rewrite:Rewrite|4:delete:Delete|5:create:Create|6:write:Write",
			'tasks_status_open' => "Open" ,
			'tasks_status_assigned' => "Assigned" ,
			'tasks_status_closed' => "Closed" ,
			'tasks_status_wontfix' => "Won't fix" ,
			'tasks_status_bgcol_open' => "#FF9999" ,
			'tasks_status_bgcol_assigned' => "#FFF380" ,
			'tasks_status_bgcol_closed' => "#99FF99" ,
			'tasks_status_bgcol_wontfix' => "#9999FF" ,
			'tasks_action_open' => "Task \"$1\" opened." ,
			'tasks_action_assigned' => "Task \"$1\" assigned." ,
			'tasks_action_closed' => "Task \"$1\" closed." ,
			'tasks_action_wontfix' => "Won't fix task \"$1\"." ,
			
			'tasks_logpage' => "Tasks log" ,
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
function wfTaskExtensionEditFormInitialHook ( &$editPage ) {
	global $wgArticle ;
	wfTaskExtensionHeaderHook ( $wgArticle ) ;
}

/**
* Display header on "Task:" pages
*/
function wfTaskExtensionHeaderHook ( &$article ) {
	global $wgTasksNamespace , $wgOut , $wgUser ;
	$title = $article->getTitle() ;
	$ns = $title->getNamespace() ;
	if ( $ns != $wgTasksNamespace AND $ns != $wgTasksNamespace+1 ) return ; # Doesn't concern us
	
	$subtitle = "" ;
	$taskid = $title->getText() ;
	$taskid = explode ( "(" , $taskid ) ;
	$taskid = explode ( ")" , array_pop ( $taskid ) ) ;
	$taskid = array_shift ( $taskid ) ;
	
	wfTasksAddCache() ;
	$st = new SpecialTasks ;
	$task = "" ;
	$page_title = $st->get_title_from_task ( $taskid , &$task ) ;
	if ( $task == "" ) return ; # No such task

	$sk =& $wgUser->getSkin() ;
	$link1 = $sk->makeLink ( $page_title->getPrefixedText() ) ;
	$link2 = $sk->makeLink ( $page_title->getPrefixedText() , wfMsg('tasks_here') , "action=tasks" ) ;
	$subtitle .= wfMsg ( 'tasks_discussion_page_for' , $link1 , $link2 ) ;
	$subtitle .= "<br/><table border='1' cellspacing='1' cellpadding='2'>" . 
				"<tr>" . wfMsg('tasks_existing_table_header') . "</tr>" ;
	$subtitle .= $st->get_task_table_row ( $task , $page_title ) ;
	$subtitle .= "</table>" ;
	
	$subtitle = $wgOut->getSubtitle() . "<br/>" . $subtitle ;
	$wgOut->setSubtitle ( $subtitle ) ;
}

/**
* Display in sidebar
*/
function wfTasksExtensionAfterToolbox (&$tpl) {
	global $wgTitle ;
	if ( $wgTitle->isTalkPage() ) return ; # No talk pages please
	if ( $wgTitle->getNamespace() < 0 ) return ; # No special pages please
	
	wfTasksAddCache() ;
	$st = new SpecialTasks ;
	$tasks = $st->get_open_task_list ( $wgTitle ) ;
	if ( count ( $tasks ) == 0 ) return ; # No tasks	

?>

			</ul>
		</div>
	</div>
	<div class="portlet" id="p-tb">
		<h5><?php $tpl->msg('tasks_sidebar_title') ?></h5>
		<div class="pBody">
			<ul>
<?php
	foreach ( $tasks AS $task ) {
		$ttype = $st->task_types[$task->task_type] ;
?>
			<li id="task_sidebar_<?php echo $ttype ?>">
			<a href="<?php
				$nt = Title::newFromText ( $st->get_task_discussion_page ( $task ) ) ;
				echo $nt->getLocalURL () ;
				?>"><?php
				echo $st->get_type_text ( $ttype ) ;
				?></a></li>
<?php
		
	}
}

/**
* Catch page movement, fix internal task_page_title values
*/
function wfTasksExtensionAfterMove ( &$special_page , &$old_title , &$new_title ) {
	if ( $new_title->isTalkPage() ) return false ; # No tasks for talk pages, no need to bother the database...

	wfTasksAddCache() ;

	$st = new SpecialTasks ;
	$st->rename_tasks_page ( $old_title , $new_title ) ;
	return false ;
}


/**
* Catch article deletion, remove all tasks
*/
function wfTasksExtensionArticleDeleteComplete ( &$article , &$user , $reason ) {
	# return false ; # Uncomment this line to prevent deletion of tasks upon deletion of article
	wfTasksAddCache() ;
	$t = $article->getTitle() ;
	if ( $t->isTalkPage() ) return false ; # No tasks for talk pages, no need to bother the database...

	$st = new SpecialTasks ;
	$st->delete_all_tasks ( $t ) ;
	return false ;
}

/**
* Catch article creation, to close "create" tasks
*/
function wfTasksExtensionArticleSaveComplete ( &$article , &$user , $text , $summary, $isminor, $watchthis, $something ) {
	wfTasksAddCache() ;
	$t = $article->getTitle() ;
	if ( $t->isTalkPage() ) return false ; # No tasks for talk pages, no need to bother the database...
	$new_id = $t->mArticleID ;
	$t->mArticleID = -1 ; # Fake non-existing page
	
	$st = new SpecialTasks ;
	$tasks = $st->get_tasks_for_page ( $t , true ) ;
	foreach ( $tasks AS $task ) {
		if ( !$st->is_creation_task ( $task->task_type ) ) continue ; # Not a "create" task
		if ( $st->is_closed ( $task->task_status ) ) continue ; # Not open
		$st->change_task_status ( $task->task_id , 3 ) ; # "Closed"
		$t->mArticleID = $new_id ;
		$st->set_new_article_id ( $t ) ;
		return false ; # Nothing more to do
	}
	
	return false ;
}

/**
* Prevents other tabs shown as active
*/
function wfTasksExtensionPreventOtherActiveTabs ( &$skin , &$prevent_active_tabs ) {
	global $action ;
	$prevent_active_tabs = $action == "tasks" ; ;
}

/**
* Show the tab
*/
function wfTasksExtensionTab ( &$skin , &$content_actions ) {
	global $wgTitle , $action ;
	if ( $wgTitle->isTalkPage() ) return false ; # No talk pages please
	if ( $wgTitle->getNamespace() < 0 ) return false ; # No special pages please

	wfTasksAddCache() ;
	$content_actions['tasks'] = array(
		'class' => ($action == 'tasks') ? 'selected' : false,
		'text' => wfMsg('tasks_tab'),
		'href' => $wgTitle->getLocalUrl( "action=tasks" )
	);
}

/**
* This is where the action is :-)
*/
function wfTasksExtensionAction ( $action , $article ) {
	if ( $action != 'tasks' ) return true ; # Not my kind of action!
	
	wfTasksAddCache() ;
	
	$t = new SpecialTasks ;
	$t->page_management ( $article->getTitle() ) ;
	
	return false ;
}

#_____________________________________________________________________________

/**
* The special page
*/
function wfTasksExtension() {
	global $IP, $wgMessageCache;
	wfTasksAddCache () ;

	$wgMessageCache->addMessage( 'tasks', 'Tasks' );

	require_once "$IP/includes/SpecialPage.php";

	class SpecialTasks extends SpecialPage {
	
		var $status_types = array (
			1 => 'open' ,
			2 => 'assigned' ,
			3 => 'closed' ,
			4 => 'wontfix'
		) ;
		var $task_types ; # e.g., 0 => 'cleanup'
		var $task_types_text ; # e.g., 'cleanup' => 'Clean up'
		var $creation_tasks ;
	
		/**
		* Constructor
		*/
		function SpecialTasks() {
			SpecialPage::SpecialPage( 'Tasks' );
			$this->includable( true );
			$this->update_types () ;
		}
		
		/**
		* Updates task_types and creation_tasks from wfMsg
		*/
		function update_types () {
			wfTasksAddCache () ;
			
			$this->task_types = array () ;
			$s = explode ( "|" , wfMsg ( 'tasks_task_types' ) ) ;
			foreach ( $s AS $l ) {
				$l = explode ( ":" , trim ( $l ) , 3 ) ;
				if ( count ( $l ) != 3 ) continue ; # Invalid line
				$this->task_types[trim($l[0])] = trim($l[1]) ;
				$this->task_types_text[trim($l[1])] = trim($l[2]) ;
			}
			
			$this->creation_tasks = array () ;
			$s = explode ( "," , wfMsg ( 'tasks_creation_tasks' ) ) ;
			foreach ( $s AS $l ) {
				$this->creation_tasks[] = trim ( $l ) ;
			}
			
		}
		
		function get_type_text ( $type_key ) {
			return $this->task_types_text[$type_key] ;
		}

		function is_creation_task ( &$task_type ) {
			return in_array ( $task_type , $this->creation_tasks ) ;
		}
		
		function is_open ( $status ) {
			if ( $status == 1 || $status == 2 )
				return true ;
			return false ;
		}

		function is_closed ( $status ) {
			return !$this->is_open ( $status ) ;
		}
		
		/**
		* Takes a title and a list of existing tasks, and decides which new tasks can be created.
		* There's no point in having a dozen "wikify" tasks for a single article, now is there? :-)
		*/
		function get_valid_new_tasks ( &$title , &$tasks ) {
			$exists = $title->exists() ;
			$tasks = $this->get_tasks_for_page ( $title ) ;
			$new_tasks = array () ;
			$tg = array () ;
			foreach ( $tasks AS $t )
				$tg[$t->task_type] = 1 ;
			for ( $a = min ( array_keys ( $this->task_types ) ) ; $a <= max ( array_keys ( $this->task_types ) ) ; $a++ ) {
				if ( $exists == $this->is_creation_task ( $a ) ) continue ; # Creation task and existance exclude each other
				if ( isset ( $tg[$a] ) AND ( $tg[$a]->task_status < 3 ) ) continue ; # Task exists and is not closed
				$tk = $this->task_types[$a] ;
				$new_tasks[$a] = $tk ;
			}
			return $new_tasks ;
		}

		/**
		* The form for creating a new task ("tasks" tab)
		*/
		function create_from_form ( $title ) {
			global $wgRequest , $wgUser ;
			if ( $wgRequest->getText('create_task', "") == "" ) return "" ; # No form
			
			$out = "" ;
			$tasks = array () ;
			$type = $wgRequest->getText('type', "") ;
			$comment = $wgRequest->getText('text', "") ;
			$new_tasks = $this->get_valid_new_tasks ( $title , $tasks ) ;
			if ( !isset ( $new_tasks[$type] ) ) # Trying to create a task that isn't available
				$out .= wfMsg('tasks_error1') ;
			else {
				$dbw =& wfGetDB( DB_MASTER );
				$dbw->insert ( 'tasks',
					array (
						'task_page_id' => $title->getArticleID() ,
						'task_page_title' => $title->getPrefixedDBkey() ,
						'task_user_id' => $wgUser->getID() ,
						'task_user_text' => $wgUser->getName() ,
						'task_user_assigned' =>  '0' , # default: No user assigned
						'task_status' => $this->get_status_number('open') ,
						'task_comment' => $comment ,
						'task_type' => $type ,
						'task_timestamp' => $dbw->timestamp()
						) ) ;
				$out .= wfMsg('tasks_ok1') ;
			}
			return $out ;
		}
		
		/**
		* For a list of tasks, get a single table row
		*/
		function get_task_table_row ( &$task , &$title , $show_page = false ) {
			global $wgContLang , $wgUser , $wgTasksNamespace , $wgExtraNamespaces ;
			$out = "" ;
			$sk = &$wgUser->getSkin() ;
			$ct = $wgContLang->timeanddate ( $task->task_timestamp ) ;
			$cu = Title::makeTitleSafe( NS_USER, $task->task_user_text ) ;
			$comment = htmlspecialchars ( $task->task_comment ) ;
			$comment = str_replace ( "\n" , "<br/>" , $comment ) ;
			$status = $task->task_status ;
			$tid = $task->task_id ;
			$ttype = $this->get_type_text ( $this->task_types[$task->task_type] ) ;

			$out .= "<tr>" ;
			if ( $show_page ) {
				$out .= "<td>" ;
				$out .= $sk->makeLink ( $title->getPrefixedText() ) ;
				$out .= "</td>" ;
			}
			$out .= "<td valign='top' align='left' nowrap bgcolor='" . wfMsg('tasks_status_bgcol_'.$this->status_types[$status]) . "'>" ;
			$out .= "<b>" . $ttype . "</b><br/><i>" ;
			$out .= wfMsg ( 'tasks_status_' . $this->status_types[$status] ) ;
			$out .= "</i></td>" ;
			$out .= "<td align='left' valign='top' nowrap>" ;
			$out .= wfMsg ( 'tasks_created_by' , $sk->makeLink ( $cu->getPrefixedText() , $task->task_user_text ) ) ;
			$out .= "<br/>{$ct}" ;

			# Closing information
			if ( $task->task_user_close != 0 && $this->is_closed ( $status ) ) {
				$user_close = new User ;
				$user_close->setID ( $task->task_user_close ) ;
				$uct = Title::makeTitleSafe( NS_USER, $user_close->getName() ) ; # Assigned user title
				$out .= "<br/>" . wfMsg ( 'tasks_closedby' , $sk->makeLink ( $uct->getPrefixedText() , $user_close->getName() ) ) ;				
				if ( $task->task_timestamp_closed != "" )
					$out .= "<br/>" . $wgContLang->timeanddate ( $task->task_timestamp_closed ) ;
			}
			$out .= "</td>" ;

			$out .= "<td align='left' valign='top'>" . $comment . "</td>" ;
			$out .= "<td align='left' valign='top'>" ;
			if ( $task->task_user_assigned == 0 ) { # Noone is assigned this task
				$out .= wfMsg('tasks_assignedto',wfMsg('tasks_noone')) ;
			} else { # Someone is assigned this task
				$au = new User ; # Assigned user
				$au->setID ( $task->task_user_assigned ) ;
				$aut = Title::makeTitleSafe( NS_USER, $au->getName() ) ; # Assigned user title
				$out .= wfMsg ( 'tasks_assignedto' , $sk->makeLink ( $aut->getPrefixedText() , $au->getName() ) ) ;
			}
			if ( $wgUser->isLoggedIn() ) {
				$txt = array() ;
				if ( $this->is_open ( $status ) ) { # Assignment
					if ( $wgUser->getID() != $task->task_user_assigned ) { # Assign myself
						$txt[] = $sk->makeLink ( $title->getPrefixedText() , wfMsg('tasks_assign_me') , "action=tasks&mode=assignme&taskid={$tid}" ) ;
					} else { # Unassign myself
						$txt[] = $sk->makeLink ( $title->getPrefixedText() , wfMsg('tasks_unassign_me') , "action=tasks&mode=unassignme&taskid={$tid}" ) ;
					}
				}
				if ( $this->is_open ( $status ) ) { # Open or assigned
					$url = $sk->makeUrl ( $title->getPrefixedText() , "action=tasks&mode=close&taskid={$tid}" ) ;
					$txt[] = wfMsg ( 'tasks_close' , $url ) ;
					$url = $sk->makeUrl ( $title->getPrefixedText() , "action=tasks&mode=wontfix&taskid={$tid}" ) ;
					$txt[] = wfMsg ( 'tasks_wontfix' , $url ) ;
				} else if ( $this->task_types[$task->task_type] != 'create' ) { # Closed or wontfix, can reopen (maybe)
					$url = $sk->makeUrl ( $title->getPrefixedText() , "action=tasks&mode=reopen&taskid={$tid}" ) ;
					$txt[] = wfMsg ( 'tasks_reopen' , $url ) ;
				}
				
				if ( count ( $txt ) > 0 )
					$out .= "<br/>" . implode ( " - " , $txt ) ;

				$tdp = $this->get_task_discussion_page ( $task ) ;
				$out .= "<br/>" . $sk->makeLink ( $tdp , wfMsg('tasks_discussion_page_link') ) ;
			}
			$out .="</td>" ;
			$out .= "</tr>" ;
			return $out ;
		}

		function get_task_discussion_page ( &$task ) {
			global $wgExtraNamespaces , $wgTasksNamespace ;
			$ttype = $this->get_type_text ( $this->task_types[$task->task_type]) ;
			$tdp = $wgExtraNamespaces[$wgTasksNamespace] . ":" . $ttype . ' (' . $task->task_id . ")" ;
			return $tdp ;
		}

		/**
		* On the "tasks" tab, show the list of existing tasks for that article
		*/
		function show_existing_tasks ( &$title , &$tasks ) {
			$out = "" ;
			foreach ( $tasks AS $task )
				$out .= $this->get_task_table_row ( $task , $title ) ;
			if ( $out == "" ) return "" ;

			$out = "<h2>" . wfMsg('tasks_existing_header') . "</h2>\n" .
				"<table border='1' cellspacing='1' cellpadding='2'>" . 
				"<tr>" . wfMsg('tasks_existing_table_header') . "</tr>" .
				$out . "</table>" ;
			return $out ;
		}
		
		/**
		* Checks if there's a "mode" set in the URL of the current page (performs changes on tasks, like assigning or closing them)
		*/
		function check_mode ( $title ) {
			global $wgUser , $wgRequest ;
			$mode = $wgRequest->getText('mode', "") ;
			$taskid = $wgRequest->getText('taskid', "") ;
			if ( $mode == "" || $taskid == "" ) return "" ; # Not correct
			if ( !$wgUser->isLoggedIn() ) return ; # Needs to be logged in
			
			$out = "" ;
			$fname = "Tasks:check_mode" ;
			$dbw =& wfGetDB( DB_MASTER );
			if ( $mode == 'assignme' ||  $mode == 'unassignme' ) {
				$conditions = array ( "task_id" => $taskid ) ;
				$user_id = $wgUser->getId() ; # Assign
				if ( $mode == 'unassignme' ) $user_id = 0 ; # Unassign me; this can be invoked for every user by editing the URL!
				$dbw->update( 'tasks',
					array( # SET
						'task_user_assigned' => $user_id,
						'task_status' => $this->get_status_number('assigned')
					),
					$conditions,
					$fname );

				$title = $this->get_title_from_task ( $taskid , $task ) ;
				$act = wfMsg ( 'tasks_assigned_myself_log' , $this->get_type_text ( $this->task_types[$task->task_type] ) ) ;
				$log = new LogPage( 'tasks' );
				$log->addEntry( 'tasks', $title , $act );
			} else if ( $mode == 'close' || $mode == 'wontfix' || $mode == 'reopen' ) {
				if ( $mode == 'reopen' ) $mode = "open" ;
				if ( $mode == 'close' ) $mode = "closed" ;
				$new_status = $this->get_status_number ( $mode ) ;
				$this->change_task_status ( $taskid , $new_status ) ;
			} else return "" ; # Unknown mode
			return $out ;
		}
		
		/**
		* Returns the number for the status
		*/
		function get_status_number ( $status ) {
			foreach ( $this->status_types AS $k => $v ) {
				if ( $v == $status )
					return $k ;
			}
			return 0 ; # Invalid status
		}
		
		/**
		* Changes the status of a task, performs some associated cleanup, and logs the action
		*/
		function change_task_status ( $taskid , $new_status ) {
			global $wgUser ;
			$fname = "Tasks:change_task_status" ;
			$dbw =& wfGetDB( DB_MASTER );
			
			$as = array ( 'task_status' => $new_status ) ; # What to chenge
			$aw = array ( 'task_id' => $taskid ) ; # Where to change it
			
			if ( $this->is_closed ( $new_status ) ) { # When closing, set closing user ID, and reset assignment
				$as['task_user_close'] = $wgUser->getID() ;
				$as['task_user_assigned'] = 0 ;
				$as['task_timestamp_closed'] = $dbw->timestamp() ;
			} else if ( $new_status == $this->get_status_number('open') ) { # Change to "open", no assigned user or closing user
				$as['task_user_assigned'] = 0 ;
				$as['task_user_close'] = 0 ;
				$as['task_timestamp_closed'] = "" ;
			}
			
			$dbw->update( 'tasks',
				$as , # SET
				$aw , # WHERE
				$fname );

			# Logging
			$title = $this->get_title_from_task ( $taskid , $task ) ;
			$act = wfMsg ( 'tasks_action_' . $this->status_types[$new_status] , $this->get_type_text ( $this->task_types[$task->task_type] ) ) ;
			$log = new LogPage( 'tasks' );
			$log->addEntry( 'tasks', $title , $act );
		}
		
		/**
		* Returns the list of active tasks for this page, for display in the sidebar
		*/
		function get_open_task_list ( &$title ) {
			$tasks = $this->get_tasks_for_page ( $title ) ;
			$ret = array () ;
			foreach ( $tasks AS $task ) {
				if ( $this->is_open ( $task->task_status ) )
					$ret[$this->get_type_text($this->task_types[$task->task_type])] = $task ;
			}
			ksort ( $ret ) ;
			return $ret ;
		}

		/**
		* Returns the title object for a task, and the task data through reference
		*/
		function get_title_from_task ( $task_id , &$task ) {
			$task = $this->get_task_from_id ( $task_id ) ;
			if ( $task->task_page_id == 0 ) { # Non-existing page
				$title = Title::newFromDBkey ( $task->task_page_title ) ;
			} else { # Existing page
				$title = Title::newFromID ( $task->task_page_id ) ;
			}
			return $title ;
		}
		
		/**
		* Returns a single task by its ID
		*/
		function get_task_from_id ( $task_id ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*',
					/* WHERE  */ array ( "task_id" => $task_id )
			);
			$task = $dbr->fetchObject( $res ) ;
			$dbr->freeResult($res);
			return $task ;
		}
		
		/**
		* Sets the article ID (on page creation)
		*/
		function set_new_article_id ( &$title ) {
			$fname = "Tasks:set_new_article_id" ;
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->update( 'tasks',
				array ( 'task_page_id' => $title->getArticleID() ) , # SET
				array ( "task_page_title" => $title->getPrefixedDBkey() ) , # WHERE
				$fname );
		}

		/**
		* Deletes all tasks associated with an article; done on article deletion
		*/
		function delete_all_tasks ( &$title ) {
			$fname = "Tasks:delete_all_tasks" ;
			if ( $title->getArticleID() == 0 )
				$conds = array ( 'task_page_title' => $title->getPrefixedDBkey() ) ;
			else
				$conds = array ( 'task_page_id' => $title->getArticleID() ) ;
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->delete ( 'tasks' ,
				$conds ,
				$fname ) ;
		}
		
		function rename_tasks_page ( $old_title , $new_title ) {
			$fname = "Tasks:rename_tasks_page" ;
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->update( 'tasks',
				array ( 'task_page_title' => $new_title->getPrefixedDBkey() ) , # SET
				array ( "task_page_title" => $old_title->getPrefixedDBkey() ) , # WHERE
				$fname );			
		}
		
		/**
		* THIS IS THE MAIN FUNCTION FOR THE TAB-BASED INTERFACE
		*/
		function page_management ( $title ) {
			if ( $title->isTalkPage() ) return ; # No tasks for talk pages, no need to bother the database...
			
			global $wgOut , $action ;
			$out = "" ;
			$tasks = array() ;
			$wgOut->setSubtitle ( wfMsg('tasks_title',$title->getPrefixedText()) ) ;
			
			# Create from form
			$out .= $this->create_from_form ( $title ) ;
			
			# Check for mode
			$out .= $this->check_mode ( $title ) ;
			
			# Get list of tasks that can be created
			$new_tasks = $this->get_valid_new_tasks ( $title , $tasks ) ;
			
			# Show task creation form, if tasks can be created
			$out .= $this->generate_form ( $new_tasks ) ;
			
			# Existing tasks
			$out .= $this->show_existing_tasks ( $title , $tasks ) ;

			# And ... out!
			$this->setHeaders();
			$wgOut->addHtml( $out );
		}

		/**
		* Generates a form for creating a new task
		*/
		function generate_form ( &$new_tasks ) {
			if ( count ( $new_tasks ) == 0 ) return "" ;
			$out = "<h2>" . wfMsg('tasks_create_header') . "</h2>\n" ; ;
			$out .= "<form method='post'>" ;
			$out .= "<table border='0' width='100%'><tr><th nowrap>" ;
			$out .= wfMsg ( 'tasks_form_new' ) ;
			$out .= "</th><td width='100%'>" ;
			$out .= "<select name='type'>" ;
			$o = array () ;
			foreach ( $new_tasks AS $k => $v ) {
				$o[$v] = "<option value='{$k}'>" . $this->get_type_text ( $v ) . "</option>" ;
			}
			ksort ( $o ) ;
			$out .= implode ( "" , $o ) ;
			$out .= "</select>" ;
			$out .= "<input type='submit' name='create_task' value='" . wfMsg ( 'ok' ) . "'/>" ;
			$out .= "</td></tr><tr><th nowrap>" ;
			$out .= wfMsg ( 'tasks_form_comment' ) ;
			$out .= "</th><td>" ;
			$out .= "<textarea name='text' rows=5 cols=20 style='width:100%'></textarea>" ;
			$out .= "</td></tr></table>" ;
			$out .= "</form>" ;
			return $out ;
		}
		
		/**
		* Returns the exisiting tasks for a single page
		*/
		function get_tasks_for_page ( &$title , $force_dbtitle = false ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$id = $title->getArticleID() ;

			if ( $id == 0 || $force_dbtitle )
				$conds = array ( 'task_page_title' => $title->getPrefixedDBkey() ) ;
			else
				$conds = array ( 'task_page_id' => $id ) ;

			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*',
					/* WHERE  */ $conds
			);
			
			$ret = array () ;
			while ( $line = $dbr->fetchObject( $res ) )
				$ret[$line->task_timestamp.":".$line->task_id] = $line ;
			$dbr->freeResult($res);
			krsort ( $ret ) ;
			return $ret ;
		}
		
		function get_assigned_tasks ( $userid ) {
			$dbr =& wfGetDB( DB_SLAVE );

			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*',
					/* WHERE  */ array ( 'task_user_assigned' => $userid )
			);
			
			$ret = array () ;
			while ( $line = $dbr->fetchObject( $res ) )
				$ret[$line->task_timestamp.":".$line->task_id] = $line ;
			$dbr->freeResult($res);
			krsort ( $ret ) ;
			return $ret ;
		}
	
		/**
		* Special page main function
		*/
		function execute( $par = null ) {
			global $wgOut , $wgRequest , $wgUser ;

			$out = "" ;
			$mode = $wgRequest->getText('mode', "") ;
			$skin =& $wgUser->getSkin() ;
			
			# Assignments
			if ( $wgUser->isLoggedIn() ) {
				if ( $mode == 'myassignments' ) {
					$tasks = $this->get_assigned_tasks ( $wgUser->getId() ) ;
					if ( count ( $tasks ) == 0 ) {
						$out .= "<p>" . wfMsg('tasks_you_have_no_assignments') . "</p>" ;
					} else {
						$out .= "<h2>" . wfMsg('tasks_my_assignments') . "</h2>\n" ;
						$out .= "<br/><table border='1' cellspacing='1' cellpadding='2'>" . 
							"<tr>" . wfMsg('tasks_table_header_page') . wfMsg('tasks_existing_table_header') . "</tr>" ;
						foreach ( $tasks AS $task ) {
							$page_title = $this->get_title_from_task ( $task->task_id , &$task ) ;
							$out .= $this->get_task_table_row ( $task , $page_title , true ) ;
						}
						$out.= "</table>" ;
					}
				} else { # default
					$link = $skin->makeLink ( "Special:Tasks" , wfMsg('tasks_link_your_assignments') , "mode=myassignments" ) ;
					$out .= "<p>" . wfMsg ( 'tasks_see_your_assignments' , $link ) . "</p>" ;
				}
			}
			
			# Search form
			$out .= "<form>" ;
			$out .= "</form>" ;

			# and ... out!
			$this->setHeaders();
			$wgOut->addHtml( $out );
		}
	} # end of class

	SpecialPage::addPage( new SpecialTasks );
}


?>
