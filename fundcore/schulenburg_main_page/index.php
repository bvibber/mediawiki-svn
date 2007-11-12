<?php
define( 'GUARD', 1 );
require( 'story.php' );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Language" content="en">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Contribute | Wikimedia Fundraising</title>
		<link rel="stylesheet" href="css/styles.css" type="text/css">
		<script type="text/javascript" src="fundraiser.js"></script>
		<style type="text/css">
			body {
				margin: 0px;
				padding: 0px;
				background-image:url(images/background.gif); background-repeat:repeat-y;
				background-position:center;
				background-color:#006699; 
				font-family: Verdana, Arial, Sans-Serif;
				font-size: 10pt;
			}
		</style>
	</head>

	<body>
		<center>

			<table border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
				<tr>
					<td width="886" height="123" valign="middle" style="background-image:url(images/header-logos.jpg);">
						<div style="margin-left:160px; font-size:22px; font-weight:bold; color:#484848; text-align:left;">

							You can help Wikipedia<br>change the world

						</div>
					</td>
				</tr>
				<tr>
					<td width="886" height="15" style="background-image:url(images/header-border.gif); background-repeat:repeat-x;">
					</td>
				</tr>
			</table>

			<br>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="463" valign="top">
						<!-- ++++++++++ VIDEOBOX begin ++++++++++ -->
						<table border="0" cellspacing="0" cellpadding="0" width="463">
							<!-- First line -->
							<tr>
								<td colspan="4"><img src="images/videobox-top.jpg" width="463" height="17" alt="border-top"></td>
							</tr>
							<!-- Second line -->
							<tr>
								<td colspan="4"><img src="images/imagine-a-world.gif" width="463" height="75" alt="Imagine a world..."></td>
							</tr>
							<!-- Third line -->
							<tr>
								<td colspan="4" height="18" style="border-top-width:0px; border-bottom-width:0px; border-left-width:1px; border-right-width:1px; border-style:solid; border-color:#c2c2c2; background-color:#ffffff">
									&nbsp;
								</td>
							</tr>
							<!-- Fourth line -->
							<tr>
								<td width="61" style="border-top-width:0px; border-bottom-width:0px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#c2c2c2; background-color:#ffffff">
									&nbsp;
								</td>

								<td valign="top" style="background-color:#ffffff;">
									<!-- INSERT YOUTUBE VIDEO (Take care to adjust height to 217px and width to 263px) -->
									<object width="263" height="217">
										<param name="movie" value="http://www.youtube.com/v/y6mCO5lXsSU&rel=1"></param>
										<param name="wmode" value="transparent"></param>
										<embed src="http://www.youtube.com/v/y6mCO5lXsSU&rel=1" type="application/x-shockwave-flash" wmode="transparent" width="263" height="217"></embed>
									</object>
									<!-- END INSERT -->
								</td>

								<td width="32" style="background-color:#ffffff"></td><td width="107" valign="bottom" align="right" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#c2c2c2; background-color:#ffffff"><img src="images/jimbo-top.jpg" width="106" height="94" alt="Jimbo portrait top"></td></tr>
							<!-- Fifth line -->
							<tr>
								<td style="border-top-width:0px; border-bottom-width:0px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#c2c2c2; background-color:#ffffff;">
									&nbsp;
								</td>
								<td align="right" style="background-color:#ffffff;">
									<div id="videooptions">
										<!-- INSERT REFERENCE to video options -->
										<a href="http://wikimediafoundation.org/donate/2007/psa/" target="_blank">Click here</a> for video playback options.
										<!-- END INSERT -->
									</div>
								</td>
								<td align="right" colspan="2" height="29" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:0px; border-style:solid; border-color:#c2c2c2; background-color:#ffffff">
									<img src="images/jimbo-middle.jpg" width="107" height="29" alt="Jimbo portrait middle">
								</td>
							</tr>
							<!-- Sixth line -->
							<tr>
								<td colspan="4">
									<img src="images/videobox-bottom.jpg" width="463" height="14" alt="border-bottom">
								</td>
							</tr>
						</table>
						<!-- ++++++++++ VIDEOBOX end ++++++++++++ -->

						<br><br>

						<!-- ++++++++++ STORYBOX begin ++++++++++ -->
						<table border="0" cellspacing="0" cellpadding="0" width="463" id="storybox">
						<?php story('patricio-lorente') ?>
						</table>
							
						<!-- ++++++++++ STORYBOX end ++++++++++++ -->

					</td>

					<td width="38"></td><!-- Spacer middle of the page -->

					<td width="321" valign="top">

						<!-- ++++++++++ DONATEBOX begin ++++++++++ -->
						<!-- ADJUST LINK to donate page here -->
						<form action="...">
							<table border="0" cellspacing="0" cellpadding="0" width="321">

								<!-- First line -->
								<tr>
									<td valign="top" colspan="4">
										<img src="images/donatebox-top.jpg" width="321" height="63" alt="donatebox-top">
									</td>
								</tr>

								<!-- Second line -->
								<tr>
									<td width="21" height="33" valign="top" style="border-top-width:0px; border-bottom-width:0px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
									<td valign="top" width="105" style="background-color:#f8e5bb">
										<img src="images/payment-first-line.gif" alt="Payment methods">
									</td>
									<td valign="top" align="left" style="background-color:#f8e5bb">
										<!-- ADJUST end -->
										<input name="amount" type="text" size="8">&nbsp;&nbsp;
										<select name="currency" size="1">
											<option selected="selected">USD</option>
											<option>EUR</option>
											<option>ZAR</option>
										</select>
									</td>
									<td width="21" valign="top" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
								</tr>

								<!-- Third line -->
								<tr>
									<td width="21" height="33" valign="top" style="border-top-width:0px; border-bottom-width:0px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
									<td colspan="2" valign="top" align="left" height="27" style="background-color:#f8e5bb">
										<img src="images/payment-second-line.gif" alt="Payment methods"></td>
									<td width="21" valign="top" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
								</tr>

								<!-- Fourth line -->
								<tr>
									<td width="21" valign="top" style="border-top-width:0px; border-bottom-width:0px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
									<td colspan="2" valign="top" align="left" style="background-color:#f8e5bb">
										<div class="tax-exemption">
											<br>
											<!-- INSERT TEXT -->
											The Wikimedia Foundation is a <b>501(c)(3) tax exempt charitable corporation</b>. You may deduct donations from your federally-taxable income.
											<!-- END INSERT -->
										</div>
									</td>
									<td width="21"  valign="top" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
								</tr>

								<!-- Fifth line -->
								<tr>
									<td width="21" height="64" valign="top" style="border-top-width:0px; border-bottom-width:1px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
									</td>
									<td colspan="2" valign="top" align="right" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:0px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										<br>
										<input type="image" src="images/button-donate-now.gif" alt="Donate now">
									</td>
									<td width="21"  valign="top" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#e9a95e; background-color:#f8e5bb">
										&nbsp;
								</td></tr>

							</table>
						</form>
						<!-- ++++++++++ DONATEBOX end ++++++++++ -->

						<br><br>

						<!-- ++++++++++ OTHERBOX begin ++++++++++ -->
						<table border="0" cellspacing="0" cellpadding="0" width="321">

							<tr>
								<td style="background-color:#ececec;">
									<ul class="tabmenu" style="display: none" id="deposit">
										<li><a id="deposit-a-0" class="active">Bank transfer</a></li>
										<li><a id="deposit-a-1" href="javascript:tab('deposit',1)">Send a cheque</a></li>
									</ul>
									<div class="tabmenu-pane" id="deposit-pane-0">
										<p><b>Deposit money into our bank account:</b></p>
										<p>Account holder: Wikimedia Foundation<br>
										Account: 068-9999995-01</p>
										<p>Bank: Dexia bank/Banque Dexia<br>
										IBAN: BE43 0689 9999 9501<br>
										BIC: GKCCBEBB</p>
									</div>
									<div class="tabmenu-pane" id="deposit-pane-1">
										TODO Cheque
									</div>
									<!-- Hide the second pane (semi-compatible with non-JS browsers -->
									<script type="text/javascript">tab('deposit', 0);</script>
								</td>
							</tr>

						</table>

						<!-- ++++++++++ OTHERBOX end ++++++++++ -->

						<br><br>

						<!-- ++++++++++ WHATWENEEDBOX begin ++++++++++ -->
						<table border="0" cellspacing="0" cellpadding="0" width="321">

							<!-- Box header -->
							<tr>
								<td>
									<img src="images/box-header-left.jpg" width="17" height="30">
								</td>
								<td align="left" width="287" style="background-image:url(images/box-header-background.gif); background-repeat:repeat-x;">
									<!-- INSERT BOX TITLE -->What we need the money for<!-- END INSERT -->
								</td>
								<td>
									<img src="images/box-header-right.jpg" width="17" height="30">
								</td>
							</tr>
							<!-- Box content -->
							<tr>
								<td height="200" style="border-top-width:0px; border-bottom-width:1px; border-left-width:1px; border-right-width:0px; border-style:solid; border-color:#d0d0d0; background-color:#ffffff">
									&nbsp;
								</td>
								<td valign="top" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:0px; border-style:solid; border-bottom-color:#d0d0d0; background-color:#ffffff">
									<br>
									<img src="images/planned-spending-distribution.jpg" height="157" width="287" alt="Planned spendign distribution">
									<br>
									<br>
									<div class="whatweneed">
										<!-- INSERT TEXT -->
										<p>Please read our latest <a href="http://upload.wikimedia.org/wikipedia/foundation/2/28/Wikimedia_2006_fs.pdf">financial report</a> (PDF) and have a look at our <a href="http://meta.wikimedia.org/wiki/Wikimedia_servers/hardware_orders">list of the hardware we ordered</a> after our last fundraising drive.</p>
										<p>Do you have more questions about our current fundraising drive? Please read our <a href="http://wikimediafoundation.org/wiki/Fundraising_FAQ">Fundraising FAQ</a>.
										</p>
										<!-- END INSERT -->
										<br>
									</div>
								</td>
								<td style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#d0d0d0; background-color:#ffffff">
									&nbsp;
								</td>
							</tr>
						</table>
						<!-- ++++++++++ WHATWENEEDBOX end ++++++++++++ -->

					</td>

				</tr>
			</table>
			<br><br>

			<div id="lastline">
				<a href="http://wikimediafoundation.org/wiki/Fundraising_FAQ">Fundraising FAQ</a>&nbsp;&nbsp;&nbsp;&nbsp;<b>&middot;</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://wikimediafoundation.org/wiki/Donor_Privacy_Policy">Donor privacy policy</a>&nbsp;&nbsp;&nbsp;&nbsp;<b>&middot;</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://wikimediafoundation.org/wiki/Deductibility_of_donations">Tax Deductability of Donations</a>&nbsp;&nbsp;&nbsp;&nbsp;<b>&middot;</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://wikimediafoundation.org/wiki/Planned_Spending_Distribution_2007-2008">Planned Spending Distribution</a>
			</div>
			<br><br>


		</center>
	</body>

</html>

