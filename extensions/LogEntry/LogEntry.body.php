<?php

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
	global $wgUser;
	
	# Don't cache since we are passing the token in the form
	$parser->disableCache();
	
	# Create token
	$token = $wgUser->editToken();
	
	# Internationalization
	wfLoadExtensionMessages( 'LogEntry' );
	$msgAppend = wfMsgHtml( 'logentry-append' );
	
	# Build target URL
	$target = SpecialPage::getTitleFor( 'LogEntry' );
	$urlTarget = $target->escapeLocalURL();
	
	# Build page name
	$page = $parser->getTitle();
	$encPage = htmlspecialchars( $page->getPrefixedText() );
	
	# Show form
	return <<<END
		<form id="logentryform" name="logentryform" method="post" action="{$urlTarget}" enctype="multipart/form-data">
			<input type="text" name="line" style="width:80%;" />
			<input type="submit" name="append" value="{$msgAppend}" />
			<input type="hidden" name="page" value="{$encPage}" />
			<input type="hidden" name="token" value="{$token}" />
		</form>
END;
}

# Special Page
class LogEntry extends UnlistedSpecialPage {
	function LogEntry() {
		UnlistedSpecialPage::UnlistedSpecialPage( 'LogEntry' );
		wfLoadExtensionMessages( 'LogEntry' );
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;
		
		$this->setHeaders();
		
		if( $wgRequest->wasPosted() ) {
			# Check token
			if( !$wgUser->matchEditToken( $wgRequest->getText('token') ) )
			{
				# Alert of invalid page
				$wgOut->addWikiMsg( 'logentry-invalidtoken' );
				return;
			}
			
			# Get page
			$page = $wgRequest->getText('page');
			
			# Get title
			$title = Title::newFromText( $page );
			if( $title && $title->userCan( 'edit', $page ) )
			{
				# Get article
				$article = new Article( $title );
				
				# Build new line
				$newLine = sprintf( "* %s %s: %s", gmdate( 'H:i' ), $wgUser->getName(),
					trim( htmlspecialchars( $wgRequest->getText( 'line' ) ) ) );
				
				# Get content without logentry tag in it
				$content = $article->getContent();
				
				# Detect section date
				$contentLines = explode( "\n", $content );
				
				# Build heading
				$heading = sprintf( '== %s ==', gmdate( 'F j' ) );
				
				# Find line of first section
				$sectionLine = false;
				foreach( $contentLines as $i => $contentLine )
				{
					# Look for == starting at the first character
					if(strpos( $contentLine, '==' ) === 0) {
						$sectionLine = $i;
						break;
					}
				}
				
				# Assemble final output
				$output = '';
				if( $sectionLine !== false )
				{
					# Lines up to section
					$preLines = array_slice( $contentLines, 0, $sectionLine );
					
					# Lines after section
					$postLines = array_slice( $contentLines, $sectionLine + 1 );
					
					# Output Lines
					$outputLines = array(); 
					
					if( trim( $contentLines[$sectionLine] ) == $heading ) {
						# Top section is current
						$outputLines = array_merge(
							$preLines,
							array(
								$contentLines[$sectionLine],
								$newLine
							),
							$postLines
						);
					}
					else
					{
						# Top section is old
						$outputLines = array_merge(
							$preLines,
							array(
								$heading,
								$newLine,
								$contentLines[$sectionLine]
							),
							$postLines
						);
					}
					$output = implode( "\n", $outputLines );
				}
				else
				{
					# There is no section, make one
					$output = sprintf( "%s\n%s\n%s", $content, $heading, $newLine );
				}
				
				# Edit article
				$article->quickEdit( $output );
				
				# Redirect
				$wgOut->redirect( $title->getPrefixedURL() );
			}
		}
		# Alert of invalid page
		$wgOut->addHTML( wfMsgHtml( 'logentry-invalidpage' ) . ": {$page}" );
	}
}