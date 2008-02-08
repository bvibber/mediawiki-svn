<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, version 2
of the License.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * A file for the WhiteList extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Paul Grinberg <gri6507@yahoo.com>
 * @author Mike Sullivan <ms-mediawiki@umich.edu>
 * @copyright Copyright © 2008, Paul Grinberg, Mike Sullivan
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

function WhiteListLoadMessages() {
    static $messagesLoaded = false;
    global $wgMessageCache;
    if ($messagesLoaded) return;
        $messagesLoaded = true;

    require(dirname(__FILE__) . '/SpecialWhitelistEdit.i18n.php' );
    foreach ( $allMessages as $lang => $langMessages ) {
        $wgMessageCache->addMessages( $langMessages, $lang );
    }
}

function wfMsgWhiteList($key) {
    WhiteListLoadMessages();
    return wfMsg($key);
}

class WhitelistEdit extends SpecialPage
{
    function WhitelistEdit() {
        global $wgWhiteListManagerRight;
        
        SpecialPage::SpecialPage("WhitelistEdit", $wgWhiteListManagerRight);
        self::loadMessages();
        return true;
    }

    function execute( $par ) {
        global $wgRequest, $wgOut, $wgUser;

        $this->setHeaders();
        $wgOut->setPagetitle(wfMsg('whitelistedit'));
        
        $contractorId = $wgRequest->getInt('contractor', 0);
        if (!$contractorId)
        {
            self::DisplayContractorSelectForm();
            return;
        }

        if ($wgRequest->getVal('submit', '') == 'Process')
            self::ProcessContractorEditChanges();
            
        self::DisplayContractorEditDetails($contractorId);
    }

    function loadMessages() {
        WhiteListLoadMessages();
        return true;
    }

    function ProcessContractorEditChanges()
    {
        global $wgOut, $wgUser, $wgRequest;
        $dbr = wfGetDB( DB_SLAVE );

        # first lets process the changes to the existing entries
        $contractorId = $wgRequest->getInt('contractor', 0);
        $modify_array = $wgRequest->getArray('cb_modify',array());
        $NewExpiryDate = $wgRequest->getVal('NewExpiryDate', '');
        $action = $wgRequest->getVal('action', '');

        if ($action == 'ChangeDate') {
            $date = ($NewExpiryDate == '') ? "" : date("Y-m-d H:i:s", strtotime($NewExpiryDate));
            foreach ($modify_array as $entry => $rowid)
                $dbr->update('whitelist',
                             array('wl_expires_on' => $date,
                                   'wl_updated_by_user_id' => $wgUser->getId()
                                  ),
                             array('wl_id' => $rowid),
                             __METHOD__
                            );
        } else if (($action == 'SetEdit') || ($action == 'SetView')) {
            foreach ($modify_array as $entry => $rowid)
                $dbr->update('whitelist',
                             array('wl_allow_edit' => ($action == 'SetEdit') ? 1 : 0,
                                   'wl_updated_by_user_id' => $wgUser->getId()
                                  ),
                             array('wl_id' => $rowid),
                             __METHOD__
                            );
        } else if ($action == 'Remove') {
            foreach ($modify_array as $entry => $rowid)
                $dbr->delete('whitelist',
                            array('wl_id' => $rowid),
                            __METHOD__
                            );
        }

        # now process the new additions, but make sure not to add duplicates
        $newPages = $wgRequest->getVal('newPages','');
        $expiryDate = $wgRequest->getVal('ExpiryDate','');
        $newAction = $wgRequest->getVal('newAction','');
        
        $pages = preg_split('/\n/', $newPages, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($pages as $entry => $pagename){
            # strip leading and trailing white spaces
            if (preg_match('/^\s*(.*?)\s*$/', $pagename, $match))
                $pagename = $match[1];

            # this is for some reason a case insensitive search, so be ware!!!
            if (!$dbr->selectRow('whitelist',
                                 array('wl_id'),
                                 array('wl_user_id'    => $contractorId,
                                       'wl_page_title' => $pagename
                                      ),
                                 __METHOD__
                                 )
                )
            $dbr->insert('whitelist',
                         array('wl_user_id'    => $contractorId,
                               'wl_page_title' => $pagename,
                               'wl_allow_edit' => ($newAction == 'SetEdit') ? 1 : 0,
                               'wl_expires_on' => ($expiryDate == '') ? "" : date("Y-m-d H:i:s", strtotime($expiryDate)),
                               'wl_updated_by_user_id' => $wgUser->getId()
                              ),
                         __METHOD__
                        );
        }
        return;
    }

    function DisplayContractorEditDetails($contractorId)
    {
        global $wgOut, $wgUser;
        $dbr = wfGetDB( DB_SLAVE );

        $wgOut->addScript(<<<END
<script language = "Javascript">
<!-- 

var form='mainform' //Give the form name here

function SetChecked(val,chkName) {
dml=document.forms[form];
len = dml.elements.length;
var i=0;
for( i=0 ; i<len ; i++) {
if (dml.elements[i].name==chkName) {
dml.elements[i].checked=val;
}
}
}
// -->
</script>
<script type='text/javascript'>document.write(getCalendarStyles());</SCRIPT>
END
);
        SpecialUserStats::AddCalendarJavascript();
        
        ob_start();
print  <<<END
<form name="mainform" method="get">
  <input type="hidden" name="contractor" value="$contractorId">
  <table cellpadding=0 cellspacing=10 border=0>
    <tr>
      <td>
        <table cellspacing=0 cellpadding=2 border=1>
          <tr>
            <td colspan=6>
END;
        $wgOut->addHTML(ob_get_contents());
        ob_clean();

        $ContractorUser = User::newFromID($contractorId);
        $wgOut->addHtml(wfMsg('whitelistfor', $ContractorUser->getRealName()));
        $wgOut->addHtml('</td></tr><tr><th><center>' .
                        wfMsg('whitelisttablemodify') .
                        "<br><a href=\"javascript:SetChecked(1,'cb_modify[]')\">" .
                        wfMsg('whitelisttablemodifyall') .
                        "</a> <a href=\"javascript:SetChecked(0,'cb_modify[]')\">" .
                        wfMsg('whitelisttablemodifynone') .
                        '</a></center></th><th>' .
                        wfMsg('whitelisttablepage') .
                        '</th><th>' .
                        wfMsg('whitelisttabletype') .
                        '</th><th>' .
                        wfMsg('whitelisttableexpires') .
                        '</th><th>' .
                        wfMsg('whitelisttablemodby') .
                        '</th><th>' .
                        wfMsg('whitelisttablemodon') .
                        '</th></tr>'
                        );
        $res = self::contractorWhitelistTable($dbr,$contractorId);
        for ($row = $dbr->fetchObject($res); $row; $row = $dbr->fetchObject($res)) {
            $wgOut->addHtml("<tr><td><center><input type='checkbox' name='cb_modify[]' value='$row->wl_id'></center></td><td>");
            $page_title = Title::newFromText($row->wl_page_title);
            $wgOut->addHtml('<a href="' . $page_title->getFullUrl() . '">' . $row->wl_page_title . "</a>");
            $wgOut->addHTML("</td><td><center>");
            if ($row->wl_allow_edit)
                $wgOut->addHtml(wfMsg('whitelisttableedit'));
            else
                $wgOut->addHtml(wfMsg('whitelisttableview'));
            $wgOut->addHtml("</center></td><td>&nbsp;$row->wl_expires_on</td><td>");
            $u = User::newFromId($row->wl_updated_by_user_id);
            $wgOut->addHtml($u->getRealName());
            $wgOut->addHtml("</td><td>$row->wl_updated_on</td></tr>");
        }
        $dbr->freeResult($res);

print  <<<END
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <script type='text/javascript'>
          var cal1 = new CalendarPopup('testdiv1');
          cal1.showNavigationDropdowns();
        </SCRIPT>
        <A HREF='#' onClick="cal1.select(document.forms[0].NewExpiryDate,'anchor1','MM/dd/yyyy'); return false;" NAME='anchor1' ID='anchor1'>
END;
        $wgOut->addHTML(ob_get_contents());
        ob_clean();

        $wgOut->addHtml(wfMsg('whitelisttablenewdate') .
                        "</A> <input type='text' size='10'  name='NewExpiryDate'/><input type='radio' name='action' value='ChangeDate'>" .
                        wfMsg('whitelisttablechangedate') .
                        " <input type='radio' name='action' value='SetEdit'>" .
                        wfMsg('whitelisttablesetedit') .
                        " <input type='radio' name='action' value='SetView'>" .
                        wfMsg('whitelisttablesetview') .
                        " <input type='radio' name='action' value='Remove' checked>" .
                        wfMsg('whitelisttableremove') .
                        "</td><td><DIV ID='testdiv1' STYLE=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\">" .
                        "</DIV></td></tr><tr><td><table border=1 cellspacing=0 cellpadding=2 width=100%><tr><td><center>"
                       );
        $wgOut->addHtml(wfMsg('whitelistnewpagesfor', $ContractorUser->getRealName()));
print  <<<END
              <textarea name="newPages" cols=60 rows=5></textarea></center>
            </td>
          </tr>
          <tr>
            <td>
                <script type='text/javascript'>
                var cal1 = new CalendarPopup('testdiv2');
                cal1.showNavigationDropdowns();
                </SCRIPT>
                <A HREF='#' onClick="cal1.select(document.forms[0].ExpiryDate,'anchor2','MM/dd/yyyy'); return false;" NAME='anchor2' ID='anchor2'>
END;
        $wgOut->addHTML(ob_get_contents());
        ob_clean();

        $wgOut->addHtml(wfMsg('whitelistnewtabledate') .
                        "</A><input type='text' size='10'  name='ExpiryDate'/> <input type='radio' name='newAction' value='SetEdit'>" .
                        wfMsg('whitelistnewtableedit') .
                        " <input type='radio' name='newAction' value='SetView' checked>" .
                        wfMsg('whitelistnewtableview') .
                        "</td><td><DIV ID='testdiv2' STYLE=\"position:absolute;visibility:hidden;background-color:white;layer-background-color:white;\">" .
                        "</DIV></td></tr></table></td></tr><tr><td><center><input type='submit' name='submit' value='" .
                        wfMsg('whitelistnewtableprocess') .
                        "' /></center></td></tr></table></form>"
                       );
    }
    
    function DisplayContractorSelectForm() {
        global $wgOut, $wgWhiteListRestrictedGroup;
        $dbr = wfGetDB( DB_SLAVE );
        
        $wgOut->addWikiText(wfMsg('whitelistselectrestricted'));

        $required = new SpecialUserStats();
        if (!method_exists($required, 'AddCalendarJavascript'))
            $wgOut->addWikiText(wfMsg('whitelistnocalendar'));
        
        $wgOut->addHTML("<form method=\"get\">");
        $wgOut->addHTML('<select name="contractor">');
        
        $users = array();
        $res = $dbr->select( 'user_groups', 'ug_user', array('ug_group'=>$wgWhiteListRestrictedGroup), __METHOD__);
        for ( $row = $dbr->fetchObject($res); $row; $row = $dbr->fetchObject($res)) {
            $u = User::newFromID($row->ug_user);
            $users[$u->getRealName()] = $row->ug_user;
        }
        $dbr->freeResult($res);
        ksort($users);
        foreach ($users as $name => $id)
            $wgOut->addHTML("<option value=\"$id\">".$name."</option>");
        $wgOut->addHTML('</select> ');
        $wgOut->addHTML("<input type=\"submit\" value=\"" . wfMsg('whitelistnewtableprocess') . "\" />");
        $wgOut->addHTML("</form>");
        return;
    }

    function contractorWhitelistTable($dbr, $contractorId)
    {
        $res = $dbr->select('whitelist',
                            array('wl_id',
                                'wl_page_title',
                                'wl_allow_edit',
                                'wl_expires_on',
                                'wl_updated_by_user_id',
                                'wl_updated_on'
                                ),
                            array('wl_user_id' => $contractorId
                                ),
                            __METHOD__
                            );
        return $res;
    }
}

function wfAddRestrictedPagesTab(&$personal_urls, $wgTitle)
{
    global $wgOut, $wgUser, $wgWhiteListRestrictedGroup;

    $userIsRestricted = in_array( $wgWhiteListRestrictedGroup, $wgUser->getGroups() );
    
    if ($wgUser->isLoggedIn() && $userIsRestricted) {
        $personal_urls['mypages'] = array(
            'text' => wfMsgWhiteList('mywhitelistpages'),
            'href' => Skin::makeSpecialUrl('WhiteList')
        );
    }
    return true;
}

require_once("$IP/includes/SpecialPage.php");

class WhiteList extends SpecialPage
{
    function WhiteList() {
        global $wgWhiteListRestrictedRight;
        
        SpecialPage::SpecialPage("WhiteList", $wgWhiteListRestrictedRight);
        self::loadMessages();
        return true;
    }

    function loadMessages() {
        WhiteListLoadMessages();
        return true;
    }

    function execute($user = '') {
        global $wgRequest, $wgOut, $wgUser, $wgWhitelistOverride;
        $dbr = wfGetDB( DB_SLAVE );

        $user = ($user == '') ? $wgUser : User::newFromId($user);

        $this->setHeaders();
        $wgOut->setPagetitle(wfMsgTL('whitelist'));

        $wgOut->addWikiText(wfMsg('whitelistpagelist', $user->getRealName()));

        $res = WhitelistEdit::contractorWhitelistTable($dbr, $user->getId());
        for ($row = $dbr->fetchObject($res); $row; $row = $dbr->fetchObject($res)) {
            $wgOut->addWikiText("* [[$row->wl_page_title]]");
        }
        $dbr->freeResult($res);

        foreach ($wgWhitelistOverride['always']['read'] as $page)
            $wgOut->addWikiText("* [[$page]]");
        foreach ($wgWhitelistOverride['always']['edit'] as $page)
            $wgOut->addWikiText("* [[$page]]");
    }
}

?>
