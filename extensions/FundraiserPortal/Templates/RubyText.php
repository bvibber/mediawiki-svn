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
	background-image: url(<?php echo $imageUrl ?>/rubytext-c.png);
	margin: 0;
	margin-left: 10px;
	margin-right: 10px;
}
div#fundraiserportal-button div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-t.png);
	background-position: top;
	background-repeat: repeat-x;
	margin: 0;
}
div#fundraiserportal-button div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
}
div#fundraiserportal-button div div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-tl.png);
	background-position: top left;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -10px;
	margin-right: -10px;
}
div#fundraiserportal-button div div div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button div div div div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-tr.png);
	background-position: top right;
	background-repeat: no-repeat;
	margin: 0;
}
div#fundraiserportal-button div div div div div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-br.png);
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
	background-image: url(<?php echo $imageUrl ?>/rubytext-b-b.png);
	background-position: bottom;
	background-repeat: repeat-x;
	margin: 0;
	margin-left: 10px;
	margin-right: 10px;
	padding: 0;
}
div#fundraiserportal-message div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-b-br.png);
	background-position: bottom right;
	background-repeat: no-repeat;
	margin: 0;
	margin-left: -10px;
	margin-right: -10px;
	padding: 0;
}
div#fundraiserportal-message div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-b-bl.png);
	background-position: bottom left;
	background-repeat: no-repeat;
	margin: 0;
	padding: 0;
	padding-bottom: 10px;
}
div#fundraiserportal-message div div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-b-l.png);
	background-position: left;
	background-repeat: repeat-y;
	margin: 0;
	padding: 0;
}
div#fundraiserportal-message div div div div {
	background-image: url(<?php echo $imageUrl ?>/rubytext-b-r.png);
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
div#fundraiserportal-close {
}
div#fundraiserportal-close a {
	float: right;
	margin-top: -11px;
	margin-right: -2px;
	display: block;
	width: 13px;
	height: 13px;
	background-image: url(<?php echo $imageUrl ?>/rubytext-b-close.png);
	background-position: top left;
	background-repeat: no-repeat;
}
</style>
<div id="fundraiserportal-button">
	<div>
		<div>
			<div>
				<div>
					<div>
						<div>
							<a href="<?php echo $wgFundraiserPortalURL ?>"><?php echo wfMsg( 'fundraiserportal-rubytext-button' ) ?></a>
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
					<p><?php echo wfMsg( 'fundraiserportal-rubytext-message' ) ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="fundraiserportal-close"><a href="#" title="<?php echo wfMsg( 'fundraiserportal-rubytext-close' ) ?>"></a></div>