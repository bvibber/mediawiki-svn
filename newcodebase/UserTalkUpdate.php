<?
# See deferred.doc

class UserTalkUpdate {

	/* private */ var $mAction, $mNamespace, $mTitle;

	function UserTalkUpdate( $action, $ns, $title )
	{
		$this->mAction = $action;
		$this->mNamespace = $ns;
		$this->mTitle = str_replace( "_", " ", $title );
	}

	function doUpdate()
	{
		global $wgUser, $wgLang;
		$fname = "UserTalkUpdate::doUpdate";

		# If namespace isn't User_talk:, do nothing.

		if ( $this->mNamespace != Namespace::getTalk(
		  Namespace::getUser() ) ) {
			return;
		}
		# If the user talk page is our own, clear the flag
		# whether we are reading it or writing it.

		if ( 0 == strcmp( $this->mTitle, $wgUser->getName() ) ) {
			$wgUser->setNewtalk( 0 );
			$wgUser->saveSettings();
		} else {
			# Not ours.  If writing, mark it as modified.

			if ( 1 == $this->mAction ) {
				$id = User::idFromName( $this->mTitle );
				if ( 0 == $id ) { return; }

				$sql = "UPDATE LOW_PRIORITY user SET user_newtalk=1 WHERE user_id={$id}";
				wfQuery( $sql, $fname );
			}
		}
	}
}

?>
