<?php
//
// TagContent MediaWiki extension.
// Translate from tags to parser functions.
// 
// Copyright (C) 2009 - John Erling Blad.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//

# Not a valid entry point, skip unless MEDIAWIKI is defined
if( !defined( 'MEDIAWIKI' ) ) {
	echo "TagContent: This is an extension to the MediaWiki package and cannot be run standalone.\n";
	die( -1 );
}

#----------------------------------------------------------------------------
#    Extension initialization
#----------------------------------------------------------------------------
 
$wgTagContent = array();
$TagContentVersion = '0.1';
$wgExtensionCredits['parserhook'][] = array(
	'name'=>'TagContent',
	'version'=>$TagContentVersion,
	'author'=>'John Erling Blad',
	'url'=>'http://www.mediawiki.org/wiki/Extension:TagContent',
	'description' => 'Translate from tags to parser functions'
    );
 
$dir = dirname(__FILE__) . '/';
$wgExtensionFunctions[] = 'wfTagContentSetup';
$wgExtensionMessagesFiles['TagContent'] = $dir . 'TagContent.i18n.php';

$wgTagContentDefine = array(
);

$wgTagContentBlacklist = array(

	// mediawiki
	'noinclude' => true,	'includeonly' => true,	'onlyinclude' => true,	'gallery' => true,

	// html tags
	'address' => true,	'applet' => true,	'area' => true,		'a' => true,
	'base' => true,		'basefont' => true,	'big' => true,		'blockquote' => true,
	'body' => true,		'br' => true,		'b' => true,		'caption' => true,
	'center' => true,	'cite' => true,		'code' => true,		'dd' => true,
	'dfn' => true,		'dir' => true,		'div' => true,		'dl' => true,
	'dt' => true,		'em' => true,		'font' => true,		'form' => true,
	'h1' => true,		'h2' => true,		'h3' => true,		'h4' => true,
	'h5' => true,		'h6' => true,		'head' => true,		'hr' => true,
	'html' => true,		'img' => true,		'input' => true,	'isindex' => true,
	'i' => true,		'kbd' => true,		'link' => true,		'li' => true,
	'map' => true,		'menu' => true,		'meta' => true,		'ol' => true,
	'option' => true,	'param' => true,	'pre' => true,		'p' => true,
	'samp' => true,		'script' => true,	'select' => true,	'small' => true,
	'strike' => true,	'strong' => true,	'style' => true,	'sub' => true,
	'sup' => true,		'table' => true,	'td' => true,		'textarea' => true,
	'th' => true,		'title' => true,	'tr' => true,		'tt' => true,
	'ul' => true,		'u' => true,		'var' => true,

);

class TagContent {
	private $mParms = null;			# hash
	private $mTemplate = null;		# string
	private static $mDefinitions = null;	# Title
	private $mTag = null;			# string
	private $mChangeable = false;		# bolean
	private $mTitle = null;			# Title

	// contructor
	public function TagContent( $tag, $template=null, &$params=null, $changeable=false ) {
		if ($params) {
			$h = array();
			$p = explode('|', htmlspecialchars($params));
			$n = 1;
			foreach ($p as $item) {
				if (false === strrpos($item, '='))
					$item = $n++ . '=' . $item;
				list($k, $v) = explode('=', $item, 2);
				$h[trim($k)] = $v;
			}
			$this->mParms = $h;
		}
		$this->mTag = $tag;
		$this->mTemplate = htmlspecialchars($template);
		$this->mChangeable = $changeable;
	}

	// accessor
	public function changeable ( $val ) {
		if (isset($val))
			$this->mChangeable = $val;
		return $this->mChangeable;
	}

	// our own reimplementation of addLink
	private function addLink( &$parser, &$title ) {
		if (!$title)
			throw new Exception( 'none' );
		$lc = LinkCache::singleton();
		$pdbk = $title->getPrefixedDBkey();
		if ( 0 != ( $id = $lc->getGoodLinkID( $pdbk ) ) ) {
			$parser->mOutput->addLink( $title, $id );
		}
		elseif ( $lc->isBadLink( $pdbk ) ) {
			$parser->mOutput->addLink( $title, 0 );
			throw new Exception( $title->getPrefixedText() );
		}
		else {
			$id = $title->getArticleID();
			$parser->mOutput->addLink( $title, $id );
			if (!$id)
				throw new Exception( $title->getPrefixedText() );
		}
	}

	// callback function for inserting our own rendering
	public function onRender ( $text, $params, &$parser ) {
		if ($this->mChangeable) {
			if (!$this->mDefinitions)
				$this->mDefinitions = Title::newFromText("Mediawiki:tags-definition");
			try {
				$this->addLink($parser, $this->mDefinitions);
			}
			catch (Exception $e) {
				return $parser->recursiveTagParse( wfMsg( 'tags-definitions-unknown', $e->getMessage() ) );
			}
		}
		if (!$this->mTitle)
			$this->mTitle = Title::newFromText($this->mTemplate);
		try {
			$this->addLink($parser, $this->mTitle);
		}
		catch (Exception $e) {
			return $parser->recursiveTagParse( wfMsg( 'tags-template-unknown', $e->getMessage() ) );
		}
		$cont = array($this->mTemplate, $text);
		foreach ($this->mParms as $k => $v) {
			if (isset($params[$k]))
				$cont[] = "$k=$params[$k]";
			else
				$cont[] = "$k=$v";
		}
		foreach ($params as $k => $v) {
			if (!isset($this->mParms[$k]))
				$cont[] = "$k=$v";
		}
		$output = '{{' . implode('|', $cont) . '}}';
		return $parser->recursiveTagParse($output);
	}
}

// build necessary structures
function wfTagContentSetup () {
	global $wgParser, $wgTagContentBlacklist, $wgTagContentDefine;
	wfLoadExtensionMessages('TagContent');
	foreach ($wgTagContentDefine as $k => $a) {
		$template = $a[0];
		$tag = strtolower($k);
		$c = new TagContent($tag, $template, $a[1], false);
		$wgParser->setHook( $tag, array( $c, 'onRender' ));
	}
	$defs = explode("\n", wfMsgNoTrans( 'tags-definition' ));
	foreach ($defs as $line) {
		if ('*' == substr($line, 0, 1)) {
			$line = ltrim($line, '*');
			$a = explode('|', $line, 3);
			$template = trim($a[1]);
			$tag = strtolower(trim($a[0]));
			if ( !$wgTagContentBlacklist[$tag] && !isset($wgTagContentDefine[$tag])) {
				$c = new TagContent($tag, $template, $a[2], true);
				$wgParser->setHook( $tag, array( $c, 'onRender' ));
			}
		}
	}
	return true;
}


