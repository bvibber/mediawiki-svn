<?php

/** Each namespace corresponds to a subclass of LqtView. */
$lqtViews = array(
	LQT_NS_CHANNEL=>'ChannelView',
	LQT_NS_THREAD=>'ThreadView',
	LQT_NS_ARCHIVE=>'ArchiveBrowser'
	);

/** Each value for the lqtMethod query variable corresponds to a  */
$lqtMethods = array();



/** 
 * Returns true if the caller is to go ahead with default behavior, false if it is to skip.
 * The method is passed a reference to $caller, plus any additional arguments supplied.
 */
function lqtDelegate( $caller, $delegate, $methodName ) {
	if( !$delegate ) return true;
	
	// Build an array from the extra arguments.
	// In any case, include $caller in the arguments.
	$extra_args = array( &$caller );
	for ( $i = 3; $i < func_num_args(); $i++ ) {
		$extra_args[] = &func_get_arg($i);
	}
	
	$refl = new ReflectionObject( $delegate );
	if ( $refl->hasMethod( $methodName ) ) {
		return call_user_func_array( array( $delegate, $methodName ), $extra_args );
	} else {
		return true;
	}
}

/**
 * Examine the request to figure out:
 * (1) Whether we're drawing a thread, a channel, etc.
 * (2) Whether a post has been saved, deleted, etc.
 * And does the appropriate thing.
 */
class LqtDispatch {
	function __construct( $delegate=null ) {
		$this->delegate = $delegate;
	}
	
	/** Return an instance of the appropriate view class for the namespace we're in. */
	function viewForTitle( $title ) {
		global $lqtViews;

		$ns = $title->getNamespace();
		if( !isset( $lqtViews[$ns] ) ) {
			return null;
		} else {
			$class = new ReflectionClass( $lqtViews[$ns] );
			if( $class->isInstantiable() ) {
				return $class->newInstance($title);
			} else {
				echo "something really, really bad happened.";
			}
		}
	}
	
	protected function handleSubmittedSave($title, $pp) {
		global $wgOut;
				
		$status = LqtEditController::tryOperation($pp);

		if ( $status == LqtEditController::SAVE_OK ) {
			$wgOut->addRedirect( $title->getFullURL() );
		}
		else {
			echo "something bad happened.";
		}
	}
	
	function execute($title) {
		global $wgRequest, $wgOut;
		
		if( lqtDelegate( $this, $this->delegate, 'lqtDispatchShouldExecute', $title ) ) {

			$pp = new PostProxy(null, $wgRequest);

			if( $pp->submittedSave() ) {
				$this->handleSubmittedSave($title, $pp);
				// exit point? XXX
			}

			$view = $this->viewForTitle( $title );
			if ($view) {
				$view->show();
				return false;
			} else {
				return true;
			}

		}
		return lqtDelegate( $this, $this->delegate, 'lqtDispatchDidExecute' );
	}
}

class LqtEditController
{
	/** returned by saveNewRevision if the revision saved alright. */
	const SAVE_OK = 1;
	const SAVE_ERROR = 2;

	
	static function tryOperation($pp) {
		switch( $pp->editType() ) {
			case "edit":
				$article = $pp->editAppliesTo();
//				$allowed_status = EditController::canEditArticle( $article );
//				if( $allowed_status == EditControlled::OK )
				
					return $article->doEdit($pp->content(), $pp->summary) ? LqtEditController::SAVE_OK : LqtEditController::SAVE_ERROR;
//				else
//					return $allowed_status; /* weird: two possible sets of return values. but, orthogonal sets.*/
			break;
/*			case "reply":
				$parent = $pp->editAppliesTo();
				$allowed_status = LqtEditController::canReplyTo($parent);
				if ( $allowed_status == EditController::OK ) { // edtcontroller?
					$new_article = null; /// xxx
					// need to also make new thread.
				}
			break;
			case "new":
			break;*/
		}
	}
	
	
	
	static function canEdit( $pp ) {
		return false;
	}
	
	static function saveNewRevision( $pp ) {
		return false;
	}
	
	static function saveExisting ( $title, $post_proxy ) {
		echo "article id = {$post_proxy->editAppliesTo()}";
		echo $post_proxy->content();
		
		
	}
}

?>