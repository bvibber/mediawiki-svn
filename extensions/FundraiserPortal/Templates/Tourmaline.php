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
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-l.png);
	background-position: left;
	background-repeat: repeat-y;
	margin: 0;
}
div#fundraiserportal-box div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-r.png);
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
	background-image: url(<?php echo $imageUrl ?>/tourmaline-c.png);
	background-repeat: repeat;
}
div#fundraiserportal-button div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-t.png);
	background-position: top;
	background-repeat: repeat-x;
}
div#fundraiserportal-button div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
}
div#fundraiserportal-button div div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-l.png);
	background-position: left;
	background-repeat: repeat-y;
}
div#fundraiserportal-button div div div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-r.png);
	background-position: right;
	background-repeat: repeat-y;
}
div#fundraiserportal-button div div div div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
}
div#fundraiserportal-button div div div div div div div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-br.png);
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
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-close.png);
	background-position: top left;
	background-repeat: no-repeat;
}
div#fundraiserportal-box-top {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-t.png);
	background-position: top;
	background-repeat: repeat-x;
	margin: 0;
	margin-left: 12px;
	margin-right: 12px;
}
div#fundraiserportal-box-top div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -12px;
	margin-right: -12px;
}
div#fundraiserportal-box-top div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
	margin: 0;
	height: 12px;
}
div#fundraiserportal-box-bottom {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
	margin-left: 12px;
	margin-right: 12px;
}
div#fundraiserportal-box-bottom div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -12px;
	margin-right: -12px;
}
div#fundraiserportal-box-bottom div div {
	background-image: url(<?php echo $imageUrl ?>/tourmaline-b-br.png);
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
function setFundraiserPortalCookie( state ) {
	// Store cookie so portal is hidden for one week
	var e = new Date();
	e.setTime( e.getTime() + ( 7 * 24 * 60 * 60 * 1000 ) );
	var work = 'hidefrportal=' + ( state ? 1 : 0 ) + '; expires=' + e.toGMTString() + '; path=/';
	document.cookie = work;
}
function getFundraiserPortalCookie() {
	return ( document.cookie.indexOf( 'hidefrportal=1' ) !== -1 );
}
var fundraiserPortalToggleState = getFundraiserPortalCookie();
updateFundraiserPortal();
</script>
<div id="fundraiserportal-box-top"><div><div></div></div></div>
<div id="fundraiserportal-box">
	<div>
		<div id="fundraiserportal-message">
			<div id="fundraiserportal-close"><a href="#" onclick="toggleFundraiserPortal();return false;" title="<?php echo wfMsg( 'fundraiserportal-tourmaline-close' ) ?>"></a></div>
			<?php echo wfMsg( 'fundraiserportal-tourmaline-message' ) ?>
		</div>
		<div id="fundraiserportal-button"><div><div><div><div><div><div><div><div><a href="<?php echo $wgFundraiserPortalURL ?>"><?php echo wfMsg( 'fundraiserportal-tourmaline-button' ) ?></a></div></div></div></div></div></div></div></div></div>
	</div>
</div>
<div id="fundraiserportal-box-bottom"><div><div></div></div></div>