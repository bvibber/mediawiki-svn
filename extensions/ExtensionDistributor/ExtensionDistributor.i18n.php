<?php

$messages = array();

$messages['en'] = array(
		'extensiondistributor' => 'Download MediaWiki extension',
		'extdist-desc' => 'Extension for distributing snapshot archives of extensions',
		'extdist-not-configured' => 'Please configure $wgExtDistTarDir and $wgExtDistWorkingCopy',
		'extdist-wc-missing' => 'The configured working copy directory does not exist!',
		'extdist-no-such-extension' => 'No such extension "$1"',
		'extdist-no-such-version' => 'The extension "$1" does not exist in the version "$2".',
		'extdist-choose-extension' => 'Select which extension you want to download:',
		'extdist-wc-empty' => 'The configured working copy directory has no distributable extensions!',
		'extdist-submit-extension' => 'Continue',
		'extdist-current-version' => 'Current version (trunk)',
		'extdist-choose-version' => '
<big>You are downloading the <b>$1</b> extension.</big>

Select your MediaWiki version. 

Most extensions work across multiple versions of MediaWiki, so if your MediaWiki version is not here, or if you have a need for the latest extension features, try using the current version.',
		'extdist-no-versions' => 'The selected extension ($1) is not available in any version!',
		'extdist-submit-version' => 'Continue',
		'extdist-no-remote' => 'Unable to contact remote subversion client.',
		'extdist-remote-error' => 'Error from remote subversion client: <pre>$1</pre>',
		'extdist-remote-invalid-response' => 'Invalid response from remote subversion client.',
		'extdist-svn-error' => 'Subversion encountered an error: <pre>$1</pre>',
		'extdist-svn-parse-error' => 'Unable to process the XML from "svn info": <pre>$1</pre>',
		'extdist-tar-error' => 'Tar returned exit code $1:',
		'extdist-created' => "A snapshot of version <b>$2</b> of the <b>$1</b> extension for MediaWiki <b>$3</b> has been created. Your download should start automatically in 5 seconds. 

The URL for this snapshot is:
:$4
It may be used for immediate download to a server, but please do not bookmark it, since the contents will not be updated, and it may be deleted at a later date.

The tar archive should be extracted into your extensions directory, then follow the extension's documentation to enable it in MediaWiki.
",
		'extdist-want-more' => 'Get another extension',
);
