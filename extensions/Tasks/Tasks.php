<?
/*
To activate, put something like this in your LocalSettings.php:
	$wgTasksNamespace = 200 ;
	$wgExtraNamespaces[$wgTasksNamespace] = "Task" ;
	$wgExtraNamespaces[$wgTasksNamespace+1] = "Task_Talk" ;
	include ( "extensions/Tasks.php" ) ;

Also, you need to run the following SQL statement:
CREATE TABLE `tasks` (
	`task_id` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
	`task_page_id` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
	`task_page_revision` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
	`task_user_id` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
	`task_user_text` VARCHAR( 255 ) NOT NULL ,
	`task_user_assigned` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0',
	`task_status` INT( 4 ) UNSIGNED NOT NULL DEFAULT '0',
	`task_comment` MEDIUMTEXT NOT NULL ,
	`task_type` INT( 4 ) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY ( `task_id` ) ,
	INDEX ( `task_article_id` , `task_status` )
	) TYPE = innodb;
*/

if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['Tasks'][] = array(
        'name' => 'Tasks',
        'description' => 'An extension to manage tasks.',
        'author' => 'Magnus Manske'
);

$wgExtensionFunctions[] = 'wfTasksExtension' ;

$wgHooks['SkinTemplateTabs'][] = 'wfTasksExtensionTab' ;
$wgHooks['UnknownAction'][] = 'wfTasksExtensionAction' ;

# Show the tab
function wfTasksExtensionTab ( &$skin , &$content_actions ) {
	global $wgTitle , $action ;
	wfTasksAddCache() ;
	$content_actions['validate'] = array(
		'class' => ($action == 'tasks') ? 'selected' : false,
		'text' => wfMsg('tasks_tab'),
		'href' => $wgTitle->getLocalUrl( "action=tasks" )
	);
}

# This is where the action is :-)
function wfTasksExtensionAction ( $action , $article ) {
	if ( $action != 'tasks' ) return true ;
	
#	global $wgOut ;
	wfTasksAddCache() ;
	
	$t = new SpecialTasks ;
	$t->page_management ( $article->getTitle() ) ;
	
#	$wgOut->addHTML ( "YES!" ) ;
	return false ;
}

# Text adding function
function wfTasksAddCache () {
	global $wgMessageCache , $wgTasksAddCache ;
	if ( $wgTasksAddCache ) return ;
	$wgTasksAddCache = true ;
	$wgMessageCache->addMessages(
		array(
			'tasks_tab' => 'Tasks',
			'tasks_form_new' => "Create new task",
			'tasks_form_comment' => "Comment",
			
			'tasks_status_open' => "Open" ,
			'tasks_status_assigned' => "Assigned" ,
			'tasks_status_closed' => "Closed" ,
			'tasks_status_wontfix' => "Won't fix" ,
			'tasks_type_cleanup' => "Cleanup" ,
			'tasks_type_wikify' => "Wikify" ,
			'tasks_type_rewrite' => "Rewrite" ,
			'tasks_type_delete' => "Delete" ,
			'tasks_type_create' => "Create" ,
/*			'stableversion_reset_log' => 'Stable version has been removed.',
			'stableversion_logpage' => 'Stable version log',
			'stableversion_logpagetext' => 'This is a log of changes to stable versions',
			'stableversion_logentry' => '',
			'stableversion_log' => 'Revision #$1 is now the stable version.',
			'stableversion_before_no' => 'There was no stable revision before.',
			'stableversion_before_yes' => 'The last stable revision was #$1.',*/
		)
	);
}

# The special page
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
		var $task_types = array (
			1 => 'cleanup',
			2 => 'wikify',
			3 => 'rewrite',
			4 => 'delete',
			5 => 'create',
		) ;
	
		/**
		* Constructor
		*/
		function SpecialTasks() {
			SpecialPage::SpecialPage( 'Tasks' );
			$this->includable( true );
		}

		function get_valid_new_tasks ( &$title , &$tasks , $exists ) {
			$new_tasks = array () ;
			if ( $exists ) {
				$tg = array () ;
				foreach ( $tasks AS $t )
					$tg[$t->task_type] = 1 ;
				for ( $a = min ( array_keys ( $this->task_types ) ) ; $a <= max ( array_keys ( $this->task_types ) ) ; $a++ ) {
					if ( isset ( $tg[$a] ) ) continue ;
					$tk = $this->task_types[$a] ;
					if ( $tk == 'create' && $exists ) continue ; # Can't create an existing article...
					$new_tasks[$a] = $tk ;
				}
			} else $new_tasks[5] = 'create' ; # Hack, should look up number for 'create' instead of using '5'
			return $new_tasks ;
		}

		
		function page_management ( $title ) {
			global $wgOut ;
			$out = "" ;
			
			if ( $title->getArticleID() == 0 ) $exists = false ;
			else $exists = true ;
			
			if ( $exists )
				$tasks = $this->get_tasks_for_page ( $title->getArticleID() ) ;
			$new_tasks = $this->get_valid_new_tasks ( $title , $tasks , $exists ) ;
			
			if ( count ( $new_tasks ) > 0 )
				$out .= $this->generate_form ( $new_tasks ) ;

			$this->setHeaders();
			$wgOut->addHtml( $out );
		}

		# Generates a form for creating a new task
		function generate_form ( &$new_tasks ) {
			$out = "" ;
			$out .= "<form method='post'>" ;
			$out .= "<table border='0' width='100%'><tr><th nowrap>" ;
			$out .= wfMsg ( 'tasks_form_new' ) ;
			$out .= "</th><td width='100%'>" ;
			$out .= "<select name='type'>" ;
			foreach ( $new_tasks AS $k => $v ) {
				$out .= "<option value='{$k}'>" . wfMsg ( 'tasks_type_' . $v ) . "</option>" ;
			}
			$out .= "</select>" ;
			$out .= "<input type=submit name='doit' value='" . wfMsg ( 'ok' ) . "'/>" ;
			$out .= "</td></tr><tr><th nowrap>" ;
			$out .= wfMsg ( 'tasks_form_comment' ) ;
			$out .= "</th><td>" ;
			$out .= "<textarea name='text' rows=5 cols=20 style='width:100%'></textarea>" ;
			$out .= "</td></tr></table>" ;
			$out .= "</form>" ;
			return $out ;
		}
		
		function get_tasks_for_page ( $id ) {
			$dbr =& wfGetDB( DB_SLAVE );

			$res = $dbr->select(
					/* FROM   */ 'tasks',
					/* SELECT */ '*', #array('tb_id', 'tb_title', 'tb_url', 'tb_ex', 'tb_name'),
					/* WHERE  */ array('task_page_id' => $id)
			);
			
			$ret = array () ;
			while ( $line = $dbr->fetchObject( $res ) )
				$ret[] = line ;
			$dbr->freeResult($res);
			return $ret ;
		}
	
		/**
		* main()
		*/
		function execute( $par = null ) {
			global $wgOut , $wgRequest ;

			$out = "OK!" ;

			$this->setHeaders();
			$wgOut->addHtml( $out );
			
/*			# Sanity checks
			$mode = $wgRequest->getText('mode', "") ;
			if ( $mode != 'set' && $mode != 'reset' ) return ; # Should be error (wrong call)
			$id = $wgRequest->getText ( 'id', "0" ) ;
			if ( $id == "0" ) return ; # Should be error (wrong call)
			if ( !wfStableVersionCanChange() ) return ; # Should be error (not allowed)

			# OK, now do business
			$t = Title::newFromID ( $id ) ;

			if ( $mode == 'set' ) { # Set new version as stable
				$newstable = $wgRequest->getText ( 'revision', "0" ) ;
				$out = wfMsg ( 'stableversion_set_ok' ) ;
				$url = $t->getFullURL ( "oldid=" . $newstable ) ;
				$act = wfMsg ( 'stableversion_log' , $newstable ) ;
			} else { # Reset stable version
				$newstable = "0" ;
				$out = wfMsg ( 'stableversion_reset_ok' ) ;
				$url = $t->getFullURL () ;
				$act = wfMsg ( 'stableversion_reset_log' ) ;
			}
			
			# Get old stable version
			$dbr =& wfGetDB( DB_SLAVE );
			$row = $dbr->selectRow( 'page', array( 'page_stable' ),
				array( 'page_id' => $id ), $fname );
			$oldstable = $row->page_stable ;
			if ( $oldstable == 0 ) $before = wfMsg ( 'stableversion_before_no' ) ;
			else $before = wfMsg ( 'stableversion_before_yes' , $oldstable ) ;
			$act .= " " . $before ;

			$conditions = array( 'page_id' => $id );
			$fname = "SpecialStableVersion:execute" ;
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->update( 'page',
				array( # SET
					'page_stable'      => $newstable,
				),
				$conditions,
				$fname );

			$out = "<p>{$out}</p><p>" . wfMsg ( 'stableversion_return' , $url , $t->getFullText() ) . "</p>" ;
			$act = "[[" . $t->getText() . "]] : " . $act ;

			# Logging
			$log = new LogPage( 'stablevers' );
			$log->addEntry( 'stablevers', $t , $act );

*/
		}
	} # end of class

	SpecialPage::addPage( new SpecialTasks );
}


?>
