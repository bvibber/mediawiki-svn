<?

# Register
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	# Modern
    $wgHooks['ParserFirstCallInit'][] = 'efLogEntryInit';
} else {
	# Legacy
    $wgExtensionFunctions[] = 'efLogEntryInit';
}

# Initialization
function efLogEntryInit() {
	global $wgParser;
	$wgParser->setHook( 'logentry', 'efLogEntryRender' );
	return true;
}

# Render
function efLogEntryRender( $input, $args, &$parser ) {
	wfLoadExtensionMessages( 'LogEntry' );
	$msgAppend = wfMsg('append');
	$target = SpecialPage::getTitleFor( 'LogEntry' );
	$urlTarget = $target->escapeLocalURL();
	$page = $parser->getTitle();
	$encPage = htmlspecialchars($page->getPrefixedText());
	return <<<END
		<form id="logentryform" name="logentryform" method="post" action="{$urlTarget}" enctype="multipart/form-data">
			<input type="text" name="line" size="70" />
			<input type="submit" name="append" value="{$msgAppend}" />
			<input type="hidden" name="page" value="{$encPage}" />
		</form>
END;
}

# Special Page
class LogEntry extends SpecialPage {
	function LogEntry() {
		SpecialPage::SpecialPage("LogEntry");
		wfLoadExtensionMessages('LogEntry');
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;
		
		$this->setHeaders();
		
		# Get page
		$page = $wgRequest->getText('page');
		
		if( $wgRequest->wasPosted()) {
			# Get title
			$title = Title::newFromText( $page );
			if($title)
			{
				if( $title->userCan( 'edit', $page ) ) {
					# Get article
					$article = new Article($title);
					
					# Build new line
					$line = sprintf("* %s %s: %s", date('H:i'), $wgUser->getName(),
						htmlspecialchars( $wgRequest->getText('line') ) );
					
					# Get content without logentry tag in it
					$content = trim( str_replace( '<logentry />', '', $article->getContent() ) );
					
					# Detect section date
					$lines = explode( "\n", $content );
					
					# Build heading
					$heading = sprintf( "== %s ==", date( 'F j' ) );
					
					# Insert new line
					$output = false;
					if( count( $lines ) > 0 ) {
						if( strpos( $lines[0], '==' ) !== false ) {
							if( trim( $lines[0] ) == $heading ) {
								# Use today's section
								$lines[0] = trim( $line );
								$output = sprintf ("%s\n%s", $heading, implode( "\n", $lines ) );
							}
						}
					}
					if( !$output ) {
						# Make new section
						$output = sprintf( "%s\n%s\n%s", $heading, trim( $line ), $content );
					}
					
					# Edit article
					$article->quickEdit( "<logentry />\n" . $output );
					
					# Redirect
					$wgOut->redirect( $title->getPrefixedURL() );
				}
			}
		}
		# Alert of invalid page
		$wgOut->addHTML(wfMsg('invalidpage') . ": {$page}");
	}
}


?>