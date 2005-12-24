<?php

if( !defined( 'MEDIAWIKI' ) )
	die();

class TodoTemplate extends QuickTemplate {
	function execute() {
?>

<style type="text/css">
.mwTodoNewForm {
	border: solid 1px #ccc;
	background-color: #eee;
	width: 40em;
	padding-left: 2em;
	padding-right: 2em;
}
.mwTodoTitle {
	font-weight: bold;
}
</style>

<script type="text/javascript" src="<?php $this->text('script') ?>"></script>

<form action="<?php $this->text('action') ?>" method="post">
	<input type="hidden" name="wpNewItem" value="1" />
	<div class="mwTodoNewForm">
		<p>
			<label for="wpSummary">Issue summary:</label>
			<br/>
			<input id="wpSummary" name="wpSummary" size="40" />
		</p>
		
		<p>
			<label for="wpComment">Details:</label>
			<br />
			<textarea id="wpComment" name="wpComment" cols="40" rows="6" wrap="virtual"></textarea>
		</p>
		
		<p>
			<label for="wpEmail">To receive notification by email when the item is closed, type your address here:</label>
			<br />
			<input id="wpEmail" name="wpEmail" size="30" />
		</p>
		
		<p>
			<input type="submit" />
		</p>
	</div>
</form>
<?php
	}
}

?>