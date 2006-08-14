<?php

global $wgMessageCache;
$wgMessageCache->addMessages( array(
	'expandtemplates' => 'Expand templates',
	'expand_templates_intro' => 'This special page takes some text and expands 
all templates in it recursively. It also expands parser functions like 
<nowiki>{{</nowiki>#if:...}}, and variables like 
<nowiki>{{</nowiki>CURRENTDAY}}&mdash;in fact pretty much everything in double-braces.
It does this by calling the relevant parser stage from MediaWiki itself.',
	'expand_templates_title' => 'Context title, for &#123;&#123;PAGENAME}} etc.:',
	'expand_templates_input' => 'Input text',
	'expand_templates_output' => 'Result',
));

class ExpandTemplates extends SpecialPage {
	function __construct() {
		parent::__construct( 'ExpandTemplates' );
	}

	function execute( $subpage ) {
		global $wgRequest, $wgParser, $wgOut;

		$this->setHeaders();
		
		$titleStr = $wgRequest->getText( 'contexttitle' );
		$title = Title::newFromText( $titleStr );
		$selfTitle = $this->getTitle();
		if ( !$title ) {
			$title = $selfTitle;
		}
		$input = $wgRequest->getText( 'input' );
		if ( strlen( $input ) ) {
			$output = $wgParser->preprocess( $input, $title, new ParserOptions );
		} else {
			$output = '';
		}

		$encTitle = htmlspecialchars( $titleStr );
		$encInput = htmlspecialchars( $input );
		$encOutput = htmlspecialchars( $output );
		$msgTitle = wfMsg( 'expand_templates_title' );
		$msgInput = wfMsg( 'expand_templates_input' );
		$msgOutput = wfMsg( 'expand_templates_output' );
		$msgSubmit = wfMsg( 'ok' );
		$action = htmlspecialchars( $selfTitle->getLocalURL() );
		
		$wgOut->addWikiText( wfMsg( 'expand_templates_intro' ) );
		$wgOut->addHTML( <<<EOT
<br/>
<form action="$action" method="POST">
$msgTitle <input type="text" name="contexttitle" size="60" value="$encTitle"/><br/>
$msgInput<br/>
<textarea name="input" rows=10>$encInput</textarea>
<br/>
<input type="submit" name="submit" value="$msgSubmit" /><br/><br/>
$msgOutput<br/>
<textarea name="output" rows=10 readonly>$encOutput</textarea><br/>
</form>
EOT
);
	}
}

?>		
