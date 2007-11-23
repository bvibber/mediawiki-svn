<?php
function _fundcore_frontpage_block_story( $storyName ) {
	$stories = array(
		'patricio-lorente',
		'some-other-story',
		'a-third-story',
	);
	$imgAlts = array(
		'Patricio Lorente',
		'2',
		'3',
	);

	$storyId = array_search( $storyName, $stories );
	if ( $storyId === false ) {
		$storyId = 0;
	}
	
	$storyName = $stories[$storyId];
	$imgAlt = htmlspecialchars( $imgAlts[$storyId] );
	$prev = $stories[( $storyId + count( $stories ) - 1 ) % count( $stories )];
	$next = $stories[($storyId + 1) % count( $stories )];

  $story = '
			<!-- Box header -->
			<tr>
				<td>
					<img src="images/box-header-left.jpg" width="17" height="30">
				</td>
				<td align="left" width="429" colspan="3" style="background-image:url(images/box-header-background.gif); background-repeat:repeat-x;">
					<!-- INSERT BOX TITLE -->What you don\'t know about Wikipedia<!-- END INSERT -->
				</td>
				<td>
					<img src="images/box-header-right.jpg" width="17" height="30">
				</td>
			</tr>
			<!-- Box content -->
			<tr>
				<td style="border-top-width:0px; border-bottom-width:1px; border-left-width:1px; border-right-width:0px; border-style:solid; border-left-color:#c2c2c2; border-bottom-color:#d0d0d0; background-color:#ffffff">
					&nbsp;
				</td>
				<td width="150" valign="top" align="middle" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:0px; border-style:solid; border-color:#d0d0d0; background-color:#ffffff">
					<br>
					<!-- INSERT IMAGE (Take care to adjust height to 200px and width to 140px) -->
					<img src="images/story-' . $storyName . '.jpg" width="140" height="200" alt="' . $imgAlt . '">
					<!-- END INSERT --><br><br><br><br>
					<div class="morestories">
						<!-- INSERT MORE STORIES -->More stories<!-- END INSERT -->
					</div>
					<br>
					<center>
						<a href="javascript:story(\'' . $prev . '\')">
							<img src="images/button-morestories-left.jpg" width="34" height="34" alt="Left button" border="0">
						</a>
						<img src="images/1-pixel-transparent.gif" width="15">
						<a href="javascript:story(\'' . $next . '\')">
							<img src="images/button-morestories-right.jpg" width="34" height="34" alt="Right button" border="0">
						</a>
					</center>
				</td>

				<td width="20" height="200" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:0px; border-style:solid; border-color:#d0d0d0; background-color:#ffffff">
					&nbsp;
				</td>
				<td valign="top" text-align="left" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:0px; border-style:solid; border-color:#d0d0d0; background-color:#ffffff">
					<br>
					<div class="story" id="storycontent">
						<?php require( "stories/$storyName.html" ); ?>
						<p><a href="..."><b>Donate now.</b></a></p>
					</div>
					<br>
				</td>
				<td style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:1px; border-style:solid; border-color:#d0d0d0; background-color:#ffffff">
					&nbsp;
				</td>
			</tr>';
  return $story;
}
?>

<!-- ++++++++++ STORYBOX begin ++++++++++ -->
<table border="0" cellspacing="0" cellpadding="0" width="463" id="storybox">
<?php echo _fundcore_frontpage_block_story('patricio-lorente'); ?>
</table>
  
<!-- ++++++++++ STORYBOX end ++++++++++++ -->
