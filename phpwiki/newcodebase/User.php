<?
# See user.doc

/* private */ $wgDefaultOptions = array(
	"quickbar" => 2, "underline" => 1, "hover" => 1,
	"cols" => 60, "rows" => 20, "searchlimit" => 20,
	"skin" => 0, "rcdays" => 3, "rclimit" => 50
);

class User {
	/* private */ var $mId, $mName, $mPassword, $mEmail;
	/* private */ var $mRights, $mOptions;
	/* private */ var $mDataLoaded;
	/* private */ var $mSkin, $mWatchlist;
	/* private */ var $mBlockedby, $mBlockreason;

	function User()
	{
		$this->loadDefaults();
	}

	# Static factory method
	#
	function newFromName( $name )
	{
		$u = new User();

		# Clean up name according to title rules
		#
		$t = Title::newFromText( $name );
		$u->setName( $t->getText() );
		return $u;
	}

	/* static */ function whoIs( $id )
	{
		return wfGetSQL( "user", "user_name", "user_id=$id" );
	}

	/* static */ function randomPassword()
	{
		$pwchars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz";
		$l = strlen( $pwchars ) - 1;

		wfSeedRandom();
		$np = $pwchars{mt_rand( 0, $l )} . $pwchars{mt_rand( 0, $l )} .
		  $pwchars{mt_rand( 0, $l )} . chr( mt_rand(48, 57) ) .
		  $pwchars{mt_rand( 0, $l )} . $pwchars{mt_rand( 0, $l )} .
		  $pwchars{mt_rand( 0, $l )};
		return $np;
	}

	function loadDefaults()
	{
		global $wgDefaultOptions;

		$this->mId = 0;
		$this->mName = getenv( "REMOTE_ADDR" );
		$this->mEmail = "";
		$this->mPassword = "";
		$this->mRights = array();
		foreach ( $wgDefaultOptions as $oname => $val ) {
			$this->mOptions[$oname] = $val;
		}
		unset( $this->mSkin );
		unset( $this->mWatchlist );
		$this->mDataLoaded = false;
		$this->mBlockedby = -1; # Unset
	}

	/* private */ function getBlockedStatus()
	{
		if ( -1 != $this->mBlockedby ) { return; }

		$remaddr = getenv( "REMOTE_ADDR" );
		$conn = wfGetDB();
		if ( 0 == $this->mId ) {
			$sql = "SELECT ipb_by,ipb_reason FROM ipblocks WHERE " .
			  "ipb_address='$remaddr'";
		} else {
			$sql = "SELECT ipb_by,ipb_reason FROM ipblocks WHERE " .
			  "(ipb_address='$remaddr' OR ipb_user={$this->mId})";
		}
		wfDebug( "User: 5: $sql\n" );

		$res = mysql_query( $sql, $conn );
		if ( ( false === $res ) || ( 0 == mysql_num_rows( $res ) ) ) {
			$this->mBlockedby = 0;
			return;
		}
		$s = mysql_fetch_object( $res );
		$this->mBlockedby = $s->ipb_by;
		$this->mBlockreason = $s->ipb_reason;
	}

	function isBlocked()
	{
		$this->getBlockedStatus();
		if ( 0 == $this->mBlockedby ) { return false; }
		return true;
	}

	function blockedBy() {
		$this->getBlockedStatus();
		return $this->mBlockedby;
	}

	function blockedFor() {
		$this->getBlockedStatus();
		return $this->mBlockreason;
	}

	function loadFromSession()
	{
		global $HTTP_COOKIE_VARS, $wsUserID, $wsUserName, $wsUserPassword;

		if ( isset( $wsUserID ) ) {
			if ( 0 != $wsUserID ) {
				$sId = $wsUserID;
			} else {
				$this->mId = 0;
				return;
			}
		} else if ( isset( $HTTP_COOKIE_VARS["wcUserID"] ) ) {
			$sId = $HTTP_COOKIE_VARS["wcUserID"];
			$wsUserID = $sId;
		} else {
			$this->mId = 0;
			return;
		}
		if ( isset( $wsUserName ) ) {
			$sName = $wsUserName;
		} else if ( isset( $HTTP_COOKIE_VARS["wcUserName"] ) ) {
			$sName = $HTTP_COOKIE_VARS["wcUserName"];
			$wsUserName = $sName;
		} else {
			$this->mId = 0;
			return;
		}
		if ( isset( $wsUserPassword ) ) {
			$sPass = $wsUserPassword;
		} else if ( isset( $HTTP_COOKIE_VARS["wcUserPassword"] ) ) {
			$sPass = $HTTP_COOKIE_VARS["wcUserPassword"];
			$wsUserPassword = $sPass;
		} else {
			$this->mId = 0;
			return;
		}
		$this->mId = $sId;
		$this->loadFromDatabase();

		if ( ( $sName != $this->mName )
		  || ( $sPass != $this->mPassword ) ) {
			$this->mId = 0;
			return;
		}
	}

	function loadFromDatabase()
	{
		global $wgDefaultOptions;

		if ( 0 == $this->mId || $this->mDataLoaded ) { return; }
		$conn = wfGetDB();
		$sql = "SELECT user_name,user_password,user_email,user_options," .
		  "user_rights FROM user WHERE user_id={$this->mId}";

		wfDebug( "User: 1: $sql\n" );
		$res = mysql_query( $sql, $conn );

		if ( $res && ( mysql_num_rows( $res ) > 0 ) ) {
			$s = mysql_fetch_object( $res );
			$this->mName = $s->user_name;
			$this->mEmail = $s->user_email;
			$this->mPassword = $s->user_password;
			$this->decodeOptions( $s->user_options );
			$this->mRights = explode( ",", strtolower( $s->user_rights ) );
		}
		mysql_free_result( $res );
		$this->mDataLoaded = true;
	}

	function getID() { return $this->mId; }
	function setID( $v ) { $this->mId = $v; }

	function getName() {
		$this->loadFromDatabase();
		return $this->mName;
	}

	function setName( $str )
	{
		$this->loadFromDatabase();
		$this->mName = $str;
	}

	function getPassword()
	{
		$this->loadFromDatabase();
		return $this->mPassword;
	}

	/* static */ function encryptPassword( $p )
	{
		$np = md5( $p );
		wfDebug( "User: 9: {$p} = {$np}\n" );
		return $np;
	}

	function setPassword( $str )
	{
		$this->loadFromDatabase();
		$this->mPassword = User::encryptPassword( $str );
	}

	function getEmail()
	{
		$this->loadFromDatabase();
		return $this->mEmail;
	}

	function setEmail( $str )
	{
		$this->loadFromDatabase();
		$this->mEmail = $str;
	}

	function getOption( $oname )
	{
		$this->loadFromDatabase();
		return $this->mOptions[$oname];
	}

	function setOption( $oname, $val )
	{
		$this->loadFromDatabase();
		$this->mOptions[$oname] = $val;
	}

	function getRights()
	{
		$this->loadFromDatabase();
		return $this->mRights;
	}

	function isEditor()
	{
		$this->loadFromDatabase();
		return in_array( "is_editor", $this->mRights );
	}

	function isSysop()
	{
		$this->loadFromDatabase();
		return in_array( "is_sysop", $this->mRights );
	}

	function &getSkin()
	{
		if ( ! isset( $this->mSkin ) ) {
			$skinNames = Skin::getSkinNames();
			$s = $this->getOption( "skin" );
			if ( "" == $s ) { $s = 0; }

			if ( $s >= count( $skinNames ) ) { $sn = "SkinStandard"; }
			else $sn = "Skin" . $skinNames[$s];
			$this->mSkin = new $sn;
		}
		return $this->mSkin;
	}

	/* private */ function loadWatchlist()
	{
		if ( 0 == $this->mId ) {
			$this->mWatchlist = array();
			return;
		}
		if ( ! isset( $this->mWatchlist ) ) {
			$a = wfGetSQL( "user", "user_watch", "user_id={$this->mId}" );
			$this->mWatchlist = explode( "\n", $a );
		}
	}

	function isWatched( $title )
	{
		$this->loadWatchlist();
		return in_array( $title, $this->mWatchlist );
	}

	function addWatch( $title )
	{
		$this->loadWatchlist();
		$t = Title::newFromURL( $title );

		array_push( $this->mWatchlist, $t->getPrefixedURL() );
	}

	function removeWatch( $title )
	{
		$this->loadWatchlist();
		if ( in_array( $title, $this->mWatchlist ) ) {
			unset( $this->mWatchlist[$title] );
		}
	}

	/* private */ function encodeOptions()
	{
		$a = array();
		foreach ( $this->mOptions as $oname => $oval ) {
			array_push( $a, "{$oname}={$oval}" );
		}
		$s = implode( "\n", $a );
		return wfStrencode( $s );
	}

	/* private */ function decodeOptions( $str )
	{
		$a = explode( "\n", $str );
		foreach ( $a as $s ) {
			if ( preg_match( "/^(.[^=]*)=(.*)$/", $s, $m ) ) {
				$this->mOptions[$m[1]] = $m[2];
			}
		}
	}

	function setCookies()
	{
		global $wsUserID, $wsUserName, $wsUserPassword;
		global $wgCookieExpiration;
		if ( 0 == $this->mId ) return;
		$this->loadFromDatabase();
		$exp = time() + $wgCookieExpiration;

		$wsUserID = $this->mId;
		setcookie( "wcUserID", $this->mId, $exp, "/" );

		$wsUserName = $this->mName;
		setcookie( "wcUserName", $this->mName, $exp, "/" );

		$wsUserPassword = $this->mPassword;
		if ( 1 == $this->getOption( "rememberpassword" ) ) {
			setcookie( "wcUserPassword", $this->mPassword, $exp, "/" );
		} else {
			setcookie( "wcUserPassword", "", time() - 3600 );
		}
	}

	function logout()
	{
		global $wsUserID;
		$this->mId = 0;

		$wsUserID = 0;

		setcookie( "wcUserID", "", time() - 3600 );
		setcookie( "wcUserPassword", "", time() - 3600 );
	}

	function saveSettings()
	{
		if ( 0 == $this->mId ) { return; }

		$sql = "UPDATE user SET " .
		  "user_name= '" . wfStrencode( $this->mName ) . "', " .
		  "user_password= '" . wfStrencode( $this->mPassword ) . "', " .
		  "user_email= '" . wfStrencode( $this->mEmail ) . "', " .
		  "user_options= '" . $this->encodeOptions() . "', " .
		  "user_rights= '" . wfStrencode( implode( ",", $this->mRights ) ) .
		  "' WHERE user_id={$this->mId}";

		wfDebug( "User: 2: $sql\n" );

		$conn = wfGetDB();
		$res = mysql_query( $sql, $conn );

		if ( isset( $this->mWatchlist ) ) {
			wfSetSQL( "user", "user_watch", implode( "\n", $this->mWatchlist ),
			  "user_id={$this->mId}" );
		}
	}

    # Checks if a user with the given name exists
	#
	function idForName()
	{
		$gotid = 0;
		$s = trim( $this->mName );
		if ( 0 == strcmp( "", $s ) ) return 0;

		$conn = wfGetDB();
		$sql = "SELECT user_id FROM user WHERE user_name='" .
		  wfStrencode( $s ) . "'";

		wfDebug( "User: 3: $sql\n" );
		$res = mysql_query( $sql , $conn );
		if ( ( false === $res ) || ( 0 == mysql_num_rows( $res ) ) ) return 0;

		$s = mysql_fetch_object( $res );
		if ( "" == $s ) return 0;

		$gotid = $s->user_id;
		mysql_free_result( $res );
		return $gotid;
	}

	function addToDatabase()
	{
		$conn = wfGetDB();
		$sql = "INSERT INTO user (user_name, user_password, " .
		  "user_email, user_rights, user_options, user_watch) VALUES ('" .
		  wfStrencode( $this->mName ) . "', '" .
		  wfStrencode( $this->mPassword ) . "', '" .
		  wfStrencode( $this->mEmail ) . "', '" .
		  wfStrencode( implode( ",", $this->mRights ) ) . "', '" .
		  $this->encodeOptions() . "', '' )";

		wfDebug( "User: 4: $sql\n" );
		$res = mysql_query( $sql, $conn );
		$this->mId = $this->idForName();

		if ( isset( $this->mWatchlist ) ) {
			wfSetSQL( "user", "user_watch", implode( "\n", $this->mWatchlist ),
			  "user_id={$this->mId}" );
		}
	}
}
?>
