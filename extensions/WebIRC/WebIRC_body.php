<?php
/**
 * WebIRC
 *
 * make a web irc client in to a special page
 *
 * @link http://www.mediawiki.org/wiki/Extension:WebIRC
 *
 * @author Devunt <devunt@devunt.kr>
 * @authorlink http://www.mediawiki.org/wiki/User:Devunt
 * @copyright Copyright © 2010 Devunt (Bae June Hyeon).
 * basically source code from http://www.mediawiki.org/wiki/Extension:WebChat
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class WebIRC extends SpecialPage {
 
    function __construct() {
        SpecialPage::SpecialPage( 'WebIRC', 'webirc' );
    }
 
    function execute( $par ) {
        global $wgOut, $wgUser, $wgIRCServer, $wgIRCChannel, $wgIRCSettings;
        wfLoadExtensionMessages( 'WebIRC' );
        $this->setHeaders();
 
        if( !$this->userCanExecute( $wgUser ) ){
            $this->displayRestrictionError();
            return;
        }
 
        $wgIRCServer = urlencode( strstr($wgIRCServer, "irc://") ? substr($wgIRCServer, 6) : $wgIRCServer );
        $wgIRCChannel = urlencode( strstr($wgIRCChannel, "#") ? $wgIRCChannel : "#".$wgIRCChannel );
        $wgIRCSettings = urlencode( $wgIRCSettings );
 
        $username = strtolower($wgUser->mName);
        $webirc_url = "";
        $serverport = explode(':', $wgIRCServer);
        if (strstr($serverport[0], "freenode.org"))
            $webirc_url =
                "http://webchat.freenode.net/?".
                "channels=$wgIRCChannel&".
                "nick=$username";
        else
            $webirc_url =
                "http://widget.mibbit.com/?".
                "server=$wgIRCServer&".
                "channel=$wgIRCChannel&".
                "settings=$wgIRCSettings&".
                "showmotd=0&".
                "nick=$username";
        $wgOut->addHTML( Xml::openElement( 'iframe', array(
            'width'     => '100%',
            'height'    => '500px',
            'scrolling' => 'no',
            'style'    => 'border:solid black 1px',
            'src'       => $webirc_url,
        ) ) . Xml::closeElement( 'iframe' ) );
    }
}
