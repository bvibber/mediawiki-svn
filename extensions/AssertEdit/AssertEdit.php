<?php

if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**#@+
 *
 * A bot interface extension that adds edit assertions, to help bots ensure
 * they stay logged in, and are working with the right wiki.
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:Assert_Edit
 *
 * @author Steve Sanbeg
 * @copyright Copyright © 2006, Steve Sanbeg
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

class AssertEdit 
{
  /**
   * methods for core assertions
   */
  static function assert_user () 
    {
      global $wgUser;
      return $wgUser->isLoggedIn();
    }
  static function assert_bot () 
    {
      global $wgUser;
      return $wgUser->isBot();
    }
  static function assert_exists()
    {
      global $wgTitle;
      return ($wgTitle->getArticleID() != 0);
    }

  /*
   * List of assertions; can be modified with setAssert
   */
  static private $msAssert=array
    (
     //simple constants, i.e. to test if the extension is installed.
     'true' => true,
     'false' => false,
     //useful variable tests, to ensure we stay logged in
     'user' => array('AssertEdit', 'assert_user'),
     'bot' => array('AssertEdit', 'assert_bot'),
     'exists' => array('AssertEdit', 'assert_exists'),
     //override these in LocalSetting.php
     'test' => false,      //Do we allow random tests?
     //'wikimedia' => false, //is this an offical wikimedia site?
     );

  static function setAssert($key,$val) 
    {
      
      //Don't confuse things by changing core assertions.
      switch ($key) {
      case 'true':
      case 'false':
      case 'user':
      case 'bot':
      case 'exists':
	return false;
      }
      //make sure it's useable.
      if (is_bool($value) or is_callable($value)) {
	self::$msAssert[$key] = $value;
	return true;
      }
      else
	return false;
    }
  //call the specified assertion
  static function callAssert($assertv, $negate) 
    {
      if (isset(self::$msAssert[$assertv])){
	if (is_bool(self::$msAssert[$assertv]))
	  $assertp = self::$msAssert[$assertv];
	elseif (is_callable(self::$msAssert[$assertv]))
	  $assertp = call_user_func(self::$msAssert[$assertv]);

	if ($negate and isset($assertp))
	  $assertp = !$assertp;
	
      } else {
	//unrecognized assert fails, regardless of negation.
	$assertp = false;
      }
      return $assertp;
    }
  
}


function wfAssertEditHook(&$editpage) {
  global $wgOut, $wgRequest;
  
  $assertv = $wgRequest->GetVal('assert');
  $assertp = true;
  
  if ($assertv != '')
    $assertp = AssertEdit::callAssert($assertv, false);

  //check for negative assert
  if ($assertp) {
    $assertv = $wgRequest->GetVal('nassert');
    if ($assertv != '')
      $assertp = AssertEdit::callAssert($assertv, true);
  }
  
  if ($assertp)
    return true;
  else {
    //slightly modified from showErrorPage(), to return back here.
    $wgOut->setPageTitle( wfMsg( 'assert_edit_title' ) );
    $wgOut->setHTMLTitle( wfMsg( 'errorpagetitle' ) );
    $wgOut->setRobotpolicy( 'noindex,nofollow' );
    $wgOut->setArticleRelated( false );
    $wgOut->enableClientCache( false );
    $wgOut->mRedirect = '';
    
    $wgOut->mBodytext = '';
    $wgOut->addWikiText( wfMsg( 'assert_edit_message', $assertv ) );

    $wgOut->returnToMain(false,$editpage->mTitle);
    return false;
  }
}

function wfAssertEditSetup()
{
  global $wgMessageCache;
  $wgMessageCache->addMessages
    (array(
	   'assert_edit_title' => 'Assert failed',
	   'assert_edit_message' => 'The specified assertion ($1) failed.'
	   ));
}

$wgHooks['AlternateEdit'][] = 'wfAssertEditHook';
$wgExtensionFunctions[] = 'wfAssertEditSetup';

$wgExtensionCredits['other'][] = array(
        'name' => 'AssertEdit',
        'author' => 'Steve Sanbeg',
        'description' => 'adds edit assertions for use by bots',
        'url' => 'http://www.mediawiki.org/wiki/Extension:Assert_Edit'
        );
