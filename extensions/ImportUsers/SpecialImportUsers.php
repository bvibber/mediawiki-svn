<?php
/**
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author RouslanZenetl
 * @author YuriyIlkiv
 * @license You are free to use this extension for any reason and mutilate it to your heart's liking.
 */

if (!defined('MEDIAWIKI')) die();
require_once "$IP/includes/SpecialPage.php";

$wgExtensionFunctions[] = 'wfSpecialImportUsers';
$wgExtensionCredits['specialpage'][] = array(
  'name' => 'Import Users',
  'author' => 'Yuriy Ilkiv, Rouslan Zenetl',
  'url' => 'http://www.mediawiki.org/wiki/Extension:ImportUsers',
  'description' => 'Imports users in bulk from CSV-file; encoding: UTF-8',
);

$wgAvailableRights[] = 'import_users';
$wgGroupPermissions['bureaucrat']['import_users'] = true;

function wfSpecialImportUsers() {
  global $IP, $wgMessageCache;

  $wgMessageCache->addMessages(
    array(
      'importusers' => 'Import Users' ,
      'importusers_form_caption' => 'Input CSV-file (UTF-8)' ,
      'importusers_form_replace_present' => 'Replace existing users' ,
      'importusers_form_button' => 'Import' ,
      'importusers_user_added' => 'User <b>%s</b> has been added.' ,
      'importusers_user_present_update' => 'User <b>%s</b> already exists. Updated.' ,
      'importusers_user_present_not_update' => 'User <b>%s</b> already exists. Did not update.' ,
      'importusers_user_invalid_format' => 'User data in the line #%s has invalid format or is blank. Skipped.' ,
      'importusers_log' => 'Import log' ,
      'importusers_log_summary' => 'Summary' ,
      'importusers_log_summary_all' => 'All' ,
      'importusers_log_summary_added' => 'Added' ,
      'importusers_log_summary_updated' => 'Updated' ));

  class SpecialImportUsers extends SpecialPage {

    function SpecialImportUsers() {
      SpecialPage::SpecialPage('ImportUsers' , 'import_users' );
    }

    function execute( $par ) {
      global $wgOut, $wgUser;
      $wgOut->setArticleRelated( false );
      if( !$wgUser->isAllowed( 'import_users' ) ) {
        $wgOut->permissionRequired( 'import_users' );
        return;
      }
      $wgOut->setPagetitle( wfMsg( 'importusers' ) );
      if (IsSet($_FILES['users_file'])) {
        $wgOut->addHTML( $this->AnalizeUsers($_FILES['users_file'],IsSet($_POST['replace_present'])) );
      } else {
        $wgOut->addHTML( $this->MakeForm() );
      }
    }

    function MakeForm() {
      $titleObj = Title::makeTitle( NS_SPECIAL, 'ImportUsers' );
      $action = $titleObj->escapeLocalURL();
      $output ='<form enctype="multipart/form-data" method="post"  action="'.$action.'">';
      $output.='<dl><dt>User file format (csv): </dt><dd>&amp;lt;login-name&amp;gt;,&amp;lt;password&amp;gt;,&amp;lt;email&amp;gt;,&amp;lt;real-name&amp;gt;</dd></dl>';
      $output.='<fieldset><legend>Upload file</legend>';
      $output.='<table border=0 a-valign=center width=100%>';
      $output.='<tr><td align=right width=160>'.wfMsg( 'importusers_form_caption' ).': </td><td><input name="users_file" type="file" size=40 /></td></tr>';
      $output.='<tr><td align=right></td><td><input name="replace_present" type="checkbox" />'.wfMsg( 'importusers_form_replace_present' ).'</td></tr>';
      $output.='<tr><td align=right></td><td><input type="submit" value="'.wfMsg( 'importusers_form_button' ).'" /></td></tr>';
      $output.='</table>';
      $output.='</fieldset>';
      $output.='</form>';
      return $output;
    }

    function AnalizeUsers($fileinfo,$replace_present) {
      global $IP, $wgOut;
      require_once "$IP/includes/User.php";
      $summary=array('all'=>0,'added'=>0,'updated'=>0);
      $filedata=explode("\n",rtrim(file_get_contents($fileinfo['tmp_name'])));
      $output='<h2>'.wfMsg( 'importusers_log' ).'</h2>';
      foreach ($filedata as $line=>$newuserstr) {
        $newuserarray=explode(',', trim( $newuserstr ) );
        if (count($newuserarray)<2) {
          $output.=sprintf(wfMsg( 'importusers_user_invalid_format' ) ,$line+1 ).'<br />';
          continue;
        }
        if (!IsSet($newuserarray[2])) $newuserarray[2]='';
        if (!IsSet($newuserarray[3])) $newuserarray[3]='';
        $NextUser=User::newFromName( $newuserarray[0] );
        $NextUser->setEmail( $newuserarray[2] );
        $NextUser->setRealName( $newuserarray[3] );
        $uid=$NextUser->idForName();
        if ($uid===0) {
          $NextUser->addToDatabase();
          $NextUser->setPassword( $newuserarray[1] );
          $NextUser->saveSettings();
          $output.=sprintf(wfMsg( 'importusers_user_added' ) ,$newuserarray[0] ).'<br />';
          $summary['added']++;
        }
        else {
          if ($replace_present) {
            $NextUser->setPassword( $newuserarray[1] );
            $NextUser->saveSettings();
            $output.=sprintf( wfMsg( 'importusers_user_present_update' ) ,$newuserarray[0] ).'<br />';
            $summary['updated']++;
          }
          else $output.=sprintf(wfMsg( 'importusers_user_present_not_update' ) ,$newuserarray[0] ).'<br />';
        }
        $summary['all']++;
      }
      $output.='<b>'.wfMsg( 'importusers_log_summary' ).'</b><br />';
      $output.=wfMsg( 'importusers_log_summary_all' ).': '.$summary['all'].'<br />';
      $output.=wfMsg( 'importusers_log_summary_added' ).': '.$summary['added'].'<br />';
      $output.=wfMsg( 'importusers_log_summary_updated' ).': '.$summary['updated'];
      return $output;
    }

  }

  SpecialPage::addPage (new SpecialImportUsers());
}
