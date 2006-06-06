<?php
	function addWikiDataBlock($title, $content) {
		global
			$wgOut;
		
	 	$wgOut->addHTML('<div class="wiki-data-block">
						<h4>'. $title . '</h4>'.
						$content .
						'</div>');
	}
?>
