<?php
/**
 * AjaxLogin extension - allows users to log in through an AJAX pop-up box
 *
 * @file
 * @ingroup Extensions
 * @version 2.0.0
 * @author Inez Korczyński <korczynski(at)gmail(dot)com>
 * @author Jack Phoenix <jack@countervandalism.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This is not a valid entry point.\n" );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'AjaxLogin',
	'version' => '2.0.0',
	'author' => array( 'Inez Korczyński', 'Jack Phoenix' ),
	'description' => 'Dynamic box which allow users to login and remind password',
	'url' => 'http://www.mediawiki.org/wiki/Extension:AjaxLogin',
	'descmsg' => 'ajaxlogin-desc',
);

// Array of skins for which AjaxLogin is enabled.
// Key is: 'skinname' => (true or false)
$wgEnableAjaxLogin = array(
	'monobook' => true,
	'vector' => true
);

// Autoload AjaxLogin API interface
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ApiAjaxLogin'] = $dir . 'ApiAjaxLogin.php';
$wgAPIModules['ajaxlogin'] = 'ApiAjaxLogin';

// Internationalization file
$wgExtensionMessagesFiles['AjaxLogin'] = $dir . 'AjaxLogin.i18n.php';

// Hook things up
$wgHooks['BeforePageDisplay'][] = 'AjaxLoginJS';
$wgHooks['SkinAfterContent'][] = 'GetAjaxLoginForm';
$wgHooks['MakeGlobalVariablesScript'][] = 'efAddAjaxLoginVariables';

/**
 * Adds required JavaScript & CSS files to the HTML output of a page if AjaxLogin is enabled
 *
 * @param $out OutputPage object
 * @return true
 */
function AjaxLoginJS( OutputPage $out ) {
	global $wgEnableAjaxLogin, $wgScriptPath;

	# Don't load anything if AjaxLogin isn't enabled
	if ( !isset( $wgEnableAjaxLogin ) ) {
		return true;
	}

	// Our custom CSS
	$out->addExtensionStyle( $wgScriptPath . '/extensions/AjaxLogin/AjaxLogin.css' );
	// JQuery and JQModal scripts
	$out->addScriptFile( $wgScriptPath . '/extensions/AjaxLogin/jquery-1.3.2.js' );
	$out->addScriptFile( $wgScriptPath . '/extensions/AjaxLogin/jqModal.js' );
	$out->addScriptFile( $wgScriptPath . '/extensions/AjaxLogin/AjaxLogin.js' );

	return true;
}

/**
 * Adds the required JavaScript variables inside the <head> tags of the page
 * if AjaxLogin is enabled and the current page is not an article page.
 *
 * @param $vars Variables to be added
 * @return true
 */
function efAddAjaxLoginVariables( $vars ) {
	global $wgEnableAjaxLogin;

	$vars['wgEnableAjaxLogin'] = ( is_array( $wgEnableAjaxLogin ) ) ? in_array( $vars['skin'], $wgEnableAjaxLogin ) : false;
	if ( $vars['wgIsArticle'] == false && $vars['wgEnableAjaxLogin'] ) {
		wfLoadExtensionMessages( 'AjaxLogin' );
		$vars['ajaxLogin1'] = wfMsg( 'ajaxLogin1' );
		$vars['ajaxLogin2'] = wfMsg( 'ajaxLogin2' );
	}

	return true;
}

/**
 * Gets the AjaxLogin form
 *
 * @param $data The data, AjaxLogin form in this case, to be added to the HTML output of a page
 * @return true
 */
function GetAjaxLoginForm( &$data ) {
	global $wgAuth, $wgEnableEmail, $wgOut, $wgTitle, $wgUser;
	if ( $wgUser->isAnon() && $wgTitle->getNamespace() != 8 && $wgTitle->getDBkey() != 'Userlogin' ) {
		$titleObj = SpecialPage::getTitleFor( 'Userlogin' );
		$link = $titleObj->getLocalUrl( 'type=signup' );
		$wgOut->addHTML( '<!--[if lt IE 9]><style type="text/css">#userloginRound { width: 350px !important; }</style><![endif]-->
	<div id="userloginRound" class="roundedDiv jqmWindow">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="r_boxContent">
		<div>
			<div name="wpClose" id="wpClose" value="' . wfMsg( 'close' ) . '" style ="float:right;cursor:pointer;"><a href="#" tabindex="108"><font size="4" color="white"><b>X</b></font></a>
			</div>
			<div class="boxHeader color1">
		' . wfMsg( 'login' ) . '
		</div>
	</div>
		<form action="" method="post" name="userajaxloginform" id="userajaxloginform" style="margin:5px">
			<div id="wpError" style="width: 250px; line-height: 1.4em;"></div>
			<label>' . wfMsg( 'loginprompt' ) . '</label><br /><br />
			<label for="wpName1">' . wfMsg( 'yourname' ) . '</label><br />
			<input type="text" class="loginText" name="wpName" id="wpName1" tabindex="101" size="20" /><br />
			<label for="wpPassword1">' . wfMsg( 'yourpassword' ) . '</label><br />
			<input type="password" class="loginPassword" name="wpPassword" id="wpPassword1" tabindex="102" size="20" /><br />
			<div style="padding-bottom:3px">
				<input type="checkbox" name="wpRemember" tabindex="104" value="1" id="wpRemember1"' . ( $wgUser->getOption( 'rememberpassword' ) ? ' checked="checked"' : '' ) . ' />
				<label for="wpRemember1">' . wfMsg( 'remembermypassword' ) . '</label><br />
			</div>
			<input style="margin:0;padding:0 .25em;width:auto;overflow:visible;" type="submit" name="wpLoginattempt" id="wpLoginattempt" tabindex="105" value="' . wfMsg( 'login' ) . '" />'
		);
		if ( $wgEnableEmail && $wgAuth->allowPasswordChange() ) {
			$wgOut->addHTML( '<br /><input style="margin:3px 0;padding:0 .25em;width:auto;overflow:visible;font-size:0.9em" type="submit" name="wpMailmypassword" id="wpMailmypassword" tabindex="106" value="' . wfMsg( 'mailmypassword' ) . '" />' );
		}
		// Originally this used core message 'nologinlink' but it wouldn't work too well for Finnish, so I changed it. --Jack Phoenix
		wfLoadExtensionMessages( 'AjaxLogin' );
		$wgOut->addHTML( '<br /><a id="wpAjaxRegister" tabindex="107" href="' . htmlspecialchars( $link ) . '">' . wfMsg( 'ajaxlogin-create' ) . '</a>
		</form>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>' );
	}
	return true;
}