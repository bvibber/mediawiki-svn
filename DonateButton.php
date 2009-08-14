<?php

class DonateButton extends UnlistedSpecialPage {
	/* Members */

	private $mSharedMaxAge = 600;
	private $mMaxAge = 600;

	/* Functions */ 

	function execute () {
		global $wgOut;
		$wgOUt->disable();
		$this->sendHeaders();
		$js = $this->getJsOutput();
	}

	public function sharedMaxAge() {
        	return $this->mSharedMaxAge();
    	}   

	public function maxAge() {
		return $this->mMaxAge();
    	}   

	// Set the caches 
	private function sendHeaders() {
        	$smaxage = $this->sharedMaxAge();
        	$maxage = $this->maxAge();
        	$public = ( session_id() == '' );

        	header( "Content-type: text/javascript; charset=utf-8" );
        	if ( $public ) { 
            		header( "Cache-Control: public, s-maxage=$smaxage, max-age=$maxage" );
        	} else {
        		header( "Cache-Control: private, s-maxage=0, max-age=$maxage" );
       		 }	   
    	}  	

	public function getJsOutput() {
		global $wgFundraiserPortalTemplates;
	
		foreach( $wgFundraiserPortalTemplates as $template => $weight ) {
			$buttons[$template] = $this->getButtonText( $template );
		}

        	return $this->getScriptFunctions() .
			'wgFundraiserPortalButtons=(' .
				Xml::encodeJsVar( $buttons ) .
				");\n" .
				"wgFundraiserPortal=wgFundraiserPortalButtons[0];\n";
    	}

	public function getScriptFunctions() {
		$script = "
function pickDonateButton() {
        var b = new Array();
        b['Ruby'] = 25;
        b['Tourmaline'] = 25;
        b['RubyText'] = 25;
        b['Sapphire'] = 25;

        var r = new Array();
        var total = 0;

        for (var button in b) {
                total += b[button];
                for(i=0; i < b[button]; i++) {
                        r[r.length] = button;
                }
        }

        if ( total == 0 )
                return '';

        var random = Math.floor(Math.random()*total);
        return r[random];
}

function setDonateButton( button ) {
        // Store cookie so portal is hidden for four weeks
        var e = new Date();
        e.setTime( e.getTime() + 28 * 24 * 60 * 60 * 10000 ) ;
        var work = 'donateButton=' + button + '; expires=' + e.toGMTString() + '; path=/';
        document.cookie = work;
}

function getDonateButton() {
        var t = 'donateButton';
        beg = document.cookie.indexOf( t );
        if ( beg != -1 ) {
                beg += t.length+1;
                end = document.cookie.indexOf(';', beg);
                if (end == -1)
                        end = document.cookie.length;
        return( document.cookie.substring(beg,end) );
        }
}

var wgDonateButton = getDonateButton();

if ( ! wgDonateButton ) {
        var wgDonateButton = pickDonateButton();
        setDonateButton( wgDonateButton );
}

document.write( wgDonateButton );
\n\n";
		return $script;
	}

	public function getButtonText( $template ) {
		global $wgImageUrl,$wgFundraiserPortalURL;

		wfLoadExtensionMessages( 'FundraiserPortal' );

		// Add our tracking identifiet
		$button_url = $wgFundraiserPortalURL . "&utm_source=$template";

		// Switch statement of horror
		switch( $template ) {
			case "Ruby":
			$template = <<<END
<style type="text/css">
/* Monobook Style */
body.skin-monobook div#p-DONATE h5 {
	display: none;
}
body.skin-monobook div#p-DONATE div.pBody {
	background: none;
	border: 0;
	padding: 0.5em;
	padding-left: 1em;
	padding-top: 0em;
	margin: 0;
}
/* Modern Style */
body.skin-modern div#p-DONATE h5 {
	display: none;
}
body.skin-modern div#p-DONATE div.pBody {
	padding: 0.5em;
	margin: 0;
}
/* Vector Style */
body.skin-vector div#p-DONATE {
	padding-top: 0;
}
body.skin-vector div#p-DONATE h5 {
	display: none;
}
body.skin-vector div#p-DONATE div.body {
	background: none;
	padding: 0;
	margin: 0;
	margin-left: 1em;
	margin-right: 1em;
}
/* General Style */
div#fundraiserportal-button {
	background-image: url( $wgImageUrl/ruby-c.png );
	margin-left: 10px;
	margin-right: 10px;
}
div#fundraiserportal-button div {
	background-image: url( $wgImageUrl/ruby-t.png );
	background-position: top;
	background-repeat: repeat-x;
	margin: 0;
}
div#fundraiserportal-button div div {
	background-image: url( $wgImageUrl/ruby-b.png );
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
}
div#fundraiserportal-button div div div {
	background-image: url( $wgImageUrl/ruby-tl.png );
	background-position: top left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -10px;
	margin-right: -10px;
}
div#fundraiserportal-button div div div div {
	background-image: url( $wgImageUrl/ruby-bl.png );
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button div div div div div {
	background-image: url( $wgImageUrl/ruby-tr.png );
	background-position: top right;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button div div div div div div {
	background-image: url( $wgImageUrl/ruby-br.png );
	background-position: bottom right;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button a {
	display: block;
	padding: 0.5em;
	color: white;
	font-weight: bold;
	text-align: center;
}
div#fundraiserportal-button a:hover {
	text-decoration: none;
}
</style>
<div id="fundraiserportal-button">
	<div>
		<div>
			<div>
				<div>
					<div>
						<div>
							<a href=" $button_url ">< wfMsg( 'fundraiserportal-ruby-button' ) ></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear:both"></div>
END;
	break;
	case "RubyText":
        	$template = <<<END
<style type="text/css">
/* Monobook Style */
body.skin-monobook div#p-DONATE h5 {
	display: none;
}
body.skin-monobook div#p-DONATE div.pBody {
	background: none;
	border: 0;
	padding: 0.5em;
	padding-left: 1em;
	padding-top: 0em;
	margin: 0;
}
/* Modern Style */
body.skin-modern div#p-DONATE h5 {
	display: none;
}
body.skin-modern div#p-DONATE div.pBody {
	padding: 0.5em;
	margin: 0;
}
/* Vector Style */
body.skin-vector div#p-DONATE {
	padding-top: 0;
}
body.skin-vector div#p-DONATE h5 {
	display: none;
}
body.skin-vector div#p-DONATE div.body {
	background: none;
	padding: 0;
	margin: 0;
	margin-left: 1em;
	margin-right: 1em;
}
/* General Style */
div#p-DONATE.collapsed {
	display: none;
}
div#fundraiserportal-button {
	background-image: url( $wgImageUrl/rubytext-c.png);
	margin: 0;
	margin-left: 10px;
	margin-right: 10px;
}
div#fundraiserportal-button div {
	background-image: url( $wgImageUrl/rubytext-t.png);
	background-position: top;
	background-repeat: repeat-x;
	margin: 0;
}
div#fundraiserportal-button div div {
	background-image: url( $wgImageUrl/rubytext-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
}
div#fundraiserportal-button div div div {
	background-image: url( $wgImageUrl/rubytext-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -10px;
	margin-right: -10px;
}
div#fundraiserportal-button div div div div {
	background-image: url( $wgImageUrl/rubytext-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button div div div div div {
	background-image: url( $wgImageUrl/rubytext-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button div div div div div div {
	background-image: url( $wgImageUrl/rubytext-br.png);
	background-position: bottom right;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button a {
	display: block;
	padding: 0.5em;
	color: white;
	font-weight: bold;
	text-align: center;
}
div#fundraiserportal-button a:hover {
	text-decoration: none;
}
div#fundraiserportal-message {
	background-color: white;
	background-image: url( $wgImageUrl/rubytext-b-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
	margin-left: 10px;
	margin-right: 10px;
	padding: 0;
}
div#fundraiserportal-message div {
	background-image: url( $wgImageUrl/rubytext-b-br.png);
	background-position: bottom right;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -10px;
	margin-right: -10px;
	padding: 0;
}
div#fundraiserportal-message div div {
	background-image: url( $wgImageUrl/rubytext-b-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
	padding: 0;
	padding-bottom: 10px;
}
div#fundraiserportal-message div div div {
	background-image: url( $wgImageUrl/rubytext-b-l.png);
	background-position: left;
	background-repeat: repeat-y;
	margin: 0;
	padding: 0;
}
div#fundraiserportal-message div div div div {
	background-image: url( $wgImageUrl/rubytext-b-r.png);
	background-position: right;
	background-repeat: repeat-y;
	margin: 0;
	padding: 0;
}
div#fundraiserportal-message div div div div p {
	font-size: 0.8em;
	color: #333333;
	margin: 0;
	padding: 0.5em 0.75em;
}
div#fundraiserportal-close a {
	float: right;
	margin-top: -11px;
	margin-right: -2px;
	display: block;
	width: 13px;
	height: 13px;
	background-image: url( $wgImageUrl/rubytext-b-close.png);
	background-position: top left;
	background-repeat: no-repeat;
}
</style>
<script language="javascript" type="text/javascript">
function toggleFundraiserPortal() {
	fundraiserPortalToggleState = !fundraiserPortalToggleState;
	setFundraiserPortalCookie( fundraiserPortalToggleState );
	updateFundraiserPortal();
}
function updateFundraiserPortal() {
	var portal = document.getElementById( 'p-DONATE' );
	if ( !fundraiserPortalToggleState ) {
		portal.className = portal.className.replace( 'collapsed', '' );
	} else {
		portal.className += ' collapsed';
	}
}
function setFundraiserPortalCookie( state ) {
	// Store cookie so portal is hidden for four weeks
	var e = new Date();
	e.setTime( e.getTime() + ( 21 * 24 * 60 * 60 * 1000 ) );
	var work = 'hidefrportal=' + ( state ? 1 : 0 ) + '; expires=' + e.toGMTString() + '; path=/';
	document.cookie = work;
}
function getFundraiserPortalCookie() {
	return ( document.cookie.indexOf( 'hidefrportal=1' ) !== -1 );
}
var fundraiserPortalToggleState = getFundraiserPortalCookie();
updateFundraiserPortal();
</script>
<div id="fundraiserportal-button">
	<div>
		<div>
			<div>
				<div>
					<div>
						<div>
							<a href=" $button_url ">< wfMsg( 'fundraiserportal-rubytext-button' ) ></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="fundraiserportal-message">
	<div>
		<div>
			<div>
				<div>
					<p>< wfMsg( 'fundraiserportal-rubytext-message' ) ></p>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="fundraiserportal-close"><a href="#" onclick="toggleFundraiserPortal();return false;" title=" wfMsg( 'fundraiserportal-rubytext-close' ) "></a></div>
END;
		break;
	case "Tourlamine":
        	$template = <<<END
<style type="text/css">
/* Monobook Style */
body.skin-monobook div#p-DONATE h5 {
	display: none;
}
body.skin-monobook div#p-DONATE div.pBody {
	background: none;
	border: 0;
	padding: 0;
	padding-left: 0.66em;
	margin: 0;
}
/* Modern Style */
body.skin-modern div#p-DONATE h5 {
	display: none;
}
body.skin-modern div#p-DONATE div.pBody {
	padding: 0.5em;
	margin: 0;
}
/* Vector Style */
body.skin-vector div#p-DONATE {
	padding-top: 0;
}
body.skin-vector div#p-DONATE h5 {
	display: none;
}
body.skin-vector div#p-DONATE div.body {
	background: none;
	padding: 0;
	margin: 0;
	margin-left: 1em;
	margin-right: 1em;
}
/* General Style */
div#p-DONATE.collapsed {
	display: none;
}
div#fundraiserportal-box {
	background-color: white;
	background-image: url( $wgImageUrl/tourmaline-b-l.png);
	background-position: left;
	background-repeat: repeat-y;
	margin: 0;
}
div#fundraiserportal-box div {
	background-image: url( $wgImageUrl/tourmaline-b-r.png);
	background-position: right;
	background-repeat: repeat-y;
	margin: 0;
	margin-left: 1px;
	padding: 0;
	padding-left: 12px;
	padding-right: 12px;
}
div#fundraiserportal-box div div {
	margin: 0;
	padding: 0;
}
div#fundraiserportal-box div#fundraiserportal-message {
	background: none;
	font-size: 0.9em;
	color: #333333;
	margin: 0;
	margin-bottom: 0.5em;
	padding: 0;
}
div#fundraiserportal-box div#fundraiserportal-button {
	background-image: url( $wgImageUrl/tourmaline-c.png);
	background-repeat: repeat;
}
div#fundraiserportal-button div {
	background-image: url( $wgImageUrl/tourmaline-t.png);
	background-position: top;
	background-repeat: repeat-x;
}
div#fundraiserportal-button div div {
	background-image: url( $wgImageUrl/tourmaline-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
}
div#fundraiserportal-button div div div {
	background-image: url( $wgImageUrl/tourmaline-l.png);
	background-position: left;
	background-repeat: repeat-y;
}
div#fundraiserportal-button div div div div {
	background-image: url( $wgImageUrl/tourmaline-r.png);
	background-position: right;
	background-repeat: repeat-y;
}
div#fundraiserportal-button div div div div div {
	background-image: url( $wgImageUrl/tourmaline-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div {
	background-image: url( $wgImageUrl/tourmaline-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div {
	background-image: url( $wgImageUrl/tourmaline-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div div {
	background-image: url( $wgImageUrl/tourmaline-br.png);
	background-position: bottom right;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div div a {
	display: block;
	padding: 0.4em;
	color: white;
	font-weight: bold;
	text-align: center;
}
div#fundraiserportal-button a:hover {
	text-decoration: none;
}
div#fundraiserportal-close {
	background: none;
}
div#fundraiserportal-close a {
	float: right;
	display: block;
	width: 15px;
	height: 15px;
	margin-right: -5px;
	margin-top: -5px;
	background-image: url( $wgImageUrl/tourmaline-b-close.png);
	background-position: top left;
	background-repeat: no-repeat;
}
div#fundraiserportal-box-top {
	background-image: url( $wgImageUrl/tourmaline-b-t.png);
	background-position: top;
	background-repeat: repeat-x;
	margin: 0;
	margin-left: 12px;
	margin-right: 12px;
}
div#fundraiserportal-box-top div {
	background-image: url( $wgImageUrl/tourmaline-b-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -12px;
	margin-right: -12px;
}
div#fundraiserportal-box-top div div {
	background-image: url( $wgImageUrl/tourmaline-b-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
	margin: 0;
	height: 12px;
}
div#fundraiserportal-box-bottom {
	background-image: url( $wgImageUrl/tourmaline-b-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
	margin-left: 12px;
	margin-right: 12px;
}
div#fundraiserportal-box-bottom div {
	background-image: url( $wgImageUrl/tourmaline-b-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -12px;
	margin-right: -12px;
}
div#fundraiserportal-box-bottom div div {
	background-image: url( $wgImageUrl/tourmaline-b-br.png);
	background-position: bottom right;
	background-repeat: no-repeat;
	margin: 0;
	height: 12px;
}
</style>
<script language="javascript" type="text/javascript">
function toggleFundraiserPortal() {
	fundraiserPortalToggleState = !fundraiserPortalToggleState;
	setFundraiserPortalCookie( fundraiserPortalToggleState );
	updateFundraiserPortal();
}
function updateFundraiserPortal() {
	var portal = document.getElementById( 'p-DONATE' );
	if ( !fundraiserPortalToggleState ) {
		portal.className = portal.className.replace( 'collapsed', '' );
	} else {
		portal.className += ' collapsed';
	}
}
var fundraiserPortalToggleState = getDonateButton();
updateFundraiserPortal();
</script>
<div id="fundraiserportal-box-top"><div><div></div></div></div>
<div id="fundraiserportal-box">
	<div>
		<div id="fundraiserportal-message">
			<div id="fundraiserportal-close"><a href="#" onclick="toggleFundraiserPortal();return false;" title="< wfMsg( 'fundraiserportal-tourmaline-close' ) >"></a></div>
			< wfMsg( 'fundraiserportal-tourmaline-message' ) >
		</div>
		<div id="fundraiserportal-button"><div><div><div><div><div><div><div><div><a href=" $button_url ">< wfMsg( 'fundraiserportal-tourmaline-button' ) ></a></div></div></div></div></div></div></div></div></div>
	</div>
</div>
<div id="fundraiserportal-box-bottom"><div><div></div></div></div>
END;
	break;
	case 'Sapphire':
		$template = <<<END
<style type="text/css">
/* Monobook Style */
body.skin-monobook div#p-DONATE h5 {
	display: none;
}
body.skin-monobook div#p-DONATE div.pBody {
	background: none;
	border: 0;
	padding: 0.5em;
	padding-left: 1em;
	padding-top: 0em;
	margin: 0;
}
/* Modern Style */
body.skin-modern div#p-DONATE h5 {
	display: none;
}
body.skin-modern div#p-DONATE div.pBody {
	padding: 0.5em;
	margin: 0;
}
/* Vector Style */
body.skin-vector div#p-DONATE {
	padding-top: 0;
}
body.skin-vector div#p-DONATE h5 {
	display: none;
}
body.skin-vector div#p-DONATE div.body {
	background: none;
	padding: 0;
	margin: 0;
	margin-left: 1em;
	margin-right: 1em;
}
/* General Style */
div#fundraiserportal-button {
	background-image: url( $wgImageUrl/sapphire-c.png);
}
div#fundraiserportal-button div {
	background-image: url( $wgImageUrl/sapphire-t.png);
	background-position: top;
	background-repeat: repeat-x;
}
div#fundraiserportal-button div div {
	background-image: url( $wgImageUrl/sapphire-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
}
div#fundraiserportal-button div div div {
	background-image: url( $wgImageUrl/sapphire-l.png);
	background-position: left;
	background-repeat: repeat-y;
}
div#fundraiserportal-button div div div div {
	background-image: url( $wgImageUrl/sapphire-r.png);
	background-position: right;
	background-repeat: repeat-y;
}
div#fundraiserportal-button div div div div div {
	background-image: url( $wgImageUrl/sapphire-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div {
	background-image: url( $wgImageUrl/sapphire-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div {
	background-image: url( $wgImageUrl/sapphire-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div div {
	background-image: url( $wgImageUrl/sapphire-br.png);
	background-position: bottom right;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div div a {
	display: block;
	padding: 0.4em;
	color: white;
	font-weight: bold;
	text-align: center;
}
div#fundraiserportal-button a:hover {
	text-decoration: none;
}
</style>
<div id="fundraiserportal-button"><div><div><div><div><div><div><div><div><a href=" $button_url ">< wfMsg( 'fundraiserportal-sapphire-button' ) ></a></div></div></div></div></div></div></div></div></div>
END;
		break;
		} 
	return $template;
	}
}
