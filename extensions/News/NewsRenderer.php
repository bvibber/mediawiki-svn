<?php
/**
 * News renderer for News extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler, brightbyte.de
 * @copyright © 2007 Daniel Kinzler
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "Not a valid entry point.\n" );
	die( 1 );
}

#no need to include, rely on autoloader
#global $IP;
#require_once( "$IP/includes/RecentChange.php" );
#require_once( "$IP/includes/ChangeList.php" );

class NewsRenderer {
	var $parser;
	var $skin;

	var $usetemplate;
	var $templatetext;
	var $templateparser;
	var $templateoptions;

	var $changelist;

	var $namespaces;
	var $categories;
	var $types;

	var $nominor;
	var $noanon;
	var $nobot;
	var $notalk;

	var $onlynew;
	var $onlypatrolled;

	function __construct( $templatetext, $argv, &$parser ) {
		global $wgContLang, $wgUser;

		$this->skin = $wgUser->getSkin();
		$this->parser = $parser;
	
		$this->templatetext = $templatetext;

		if ( !is_null( $this->templatetext ) ) {
			$this->templatetext = trim( $this->templatetext );
			if ( $this->templatetext == '' ) $this->templatetext = NULL;
		}

		$this->usetemplate = !is_null( $this->templatetext );
	
		$this->templateparser = NULL;
		$this->templateoptions = NULL;
	
		#$template = @$argv['template'];
	
		if ( $this->usetemplate ) {
			#print "<pre>$templatetitle</pre>";
	
			$this->templateparser = clone $parser;
			$this->templateparser->setOutputType( OT_HTML );
	
			#$this->templatetitle = Title::newFromText( $template, NS_TEMPLATE );
			#$templatetext = $templateparser->fetchTemplate( $templatetitle );
			#print "<pre>$templatetext</pre>";
		
			$this->templateoptions = new ParserOptions;
			#$templateoptions->setRemoveComments( true );
			#$templateoptions->setMaxIncludeSize( self::MAX_INCLUDE_SIZE );
		}
		else {
			$this->changelist = new OldChangesList( $this->skin );
		}
	
		$this->limit = @$argv['limit'];
		if ( !$this->limit ) $this->limit = 10;
		else if ( $this->limit > 100 ) $this->limit = 100;
	
		$this->unique = @$argv['unique'];
		if ( $this->unique === 'false' || $this->unique === 'no' || $this->unique === '0' )
			$this->unique = false;

		$this->namespaces = @$argv['namespaces'];
		if ( !is_null( $this->namespaces ) ) {
			$this->namespaces = preg_split('!\s*(\|\s*)+!', trim( $this->namespaces ) );
	
			foreach ($this->namespaces as $i => $ns) {
				$ns = $wgContLang->lc($ns);
	
				if ( $ns === '-' || $ns === '0' || $ns === 'main' || $ns === 'article' ) {
					$this->namespaces[$i] = 0;
				} else {
					$this->namespaces[$i] = Namespace::getCanonicalIndex( $ns );
					if ( $this->namespaces[$i] === false || $this->namespaces[$i] === NULL )
						$this->namespaces[$i] = $wgContLang->getNsIndex( $ns );
				}
	
				if ( $this->namespaces[$i] === false || $this->namespaces[$i] === NULL ) 
					unset( $this->namespaces[$i] );
			}
		}
		
		$this->categories = @$argv['categories'];
		if ( !is_null( $this->categories ) ) {
			$this->categories = preg_split('!\s*(\|\s*)+!', trim( $this->categories ) );
	
			foreach ($this->categories as $i => $n) {
				$t = Title::makeTitleSafe(NS_CATEGORY, $n);
				$n = $t->getDBkey();
				$this->categories[$i] = $n;
			}
		}
	
		$this->nominor = @$argv['nominor'];
		if ( $this->nominor === 'false' || $this->nominor === 'no' || $this->nominor === '0' )
			$this->nominor = false;
	
		$this->nobot = @$argv['nobot'];
		if ( $this->nobot === 'false' || $this->nobot === 'no' || $this->nobot === '0' )
			$this->nobot = false;
	
		$this->noanon = @$argv['noanon'];
		if ( $this->noanon === 'false' || $this->noanon === 'no' || $this->noanon === '0' )
			$this->noanon = false;
	
		$this->notalk = @$argv['notalk'];
		if ( $this->notalk === 'false' || $this->notalk === 'no' || $this->notalk === '0' )
			$this->notalk = false;
	
		$this->onlypatrolled = @$argv['onlypatrolled'];
		if ( $this->onlypatrolled === 'false' || $this->onlypatrolled === 'no' || $this->onlypatrolled === '0' )
			$this->onlypatrolled = false;
	
		$this->onlynew = @$argv['onlynew'];
		if ( $this->onlynew === 'false' || $this->onlynew === 'no' || $this->onlynew === '0' )
			$this->onlynew = false;
	
		$this->types = array( RC_EDIT, RC_NEW );
	
		/* this doesn't work right
		if ( $unique ) {
			$group[] = 'rc_namespace AND rc_title';
		}
		*/
	
	}

	function query( $dbr, $limit, $offset = 0 ) {
		list( $trecentchanges, $tpage, $tcategorylinks ) = $dbr->tableNamesN( 'recentchanges', 'page', 'categorylinks' );
	
		$where = array();
		$group = array();
		$select = "$trecentchanges.*";
	
		$sql = "SELECT $select FROM $trecentchanges ";
		
		if ( $this->categories ) {
			$sql .= " JOIN $tpage ON page_namespace = rc_namespace AND page_title = rc_title ";
			$sql .= " JOIN $tcategorylinks ON cl_from = page_id ";

			$where[] = 'cl_to IN ( ' . $dbr->makeList( $this->categories ) . ' )';
			$group[] = 'rc_id';
		}
	
		if ( $this->nominor )  $where[] = 'rc_minor = 0';
		if ( $this->nobot )  $where[] = 'rc_bot = 0';
		if ( $this->noanon )  $where[] = 'rc_user > 0';
		if ( $this->onlypatrolled )  $where[] = 'rc_patrolled = 1';
		if ( $this->onlynew )  $where[] = 'rc_new = 1';
		if ( $this->namespaces )  $where[] = 'rc_namespace IN ( ' . $dbr->makeList( $this->namespaces ) . ' )';
		else {
			if ( $this->notalk )  $where[] = 'MOD(rc_namespace, 2) = 0';
			$where[] = 'rc_namespace >= 0'; #ignore virtual namespaces (logs, mostly)
		}
	
	
		$where[] = 'rc_type IN ( ' . $dbr->makeList( $this->types ) . ' )';
	
		if ( $where ) $sql .= ' WHERE ( ' . implode( ' ) AND ( ', $where ) . ' )';
		if ( $group ) $sql .= ' GROUP BY ' . implode( ' AND ', $group );
	
		$sql .= ' ORDER BY rc_timestamp DESC ';
	
		$sql = $dbr->limitResult( $sql, $limit, $offset );
	
		$res = $dbr->query( $sql, 'newsxFetchRows' );
	
		return $res;
	}
	
	# The callback function for converting the input text to HTML output
	function renderNews( ) {
		global $wgTitle;

		$this->parser->disableCache();
	
		$dbr = wfGetDB( DB_SLAVE );
	
		$text = '';
	
		if ( !$this->usetemplate )
			$text .= $this->changelist->beginRecentChangesList();

		$remaining = $this->limit;
		$offset = 0;
		$ignore = array(); #collect stuff we already have, when in unique mode
	
		while ( $remaining > 0 ) { #chunk loop for programmatic filter
			$chunk = $this->unique ? $remaining * 2 : $remaining;
			$res = $this->query( $dbr, $chunk, $offset );
			$offset += $chunk;
		
			$has = false;
			while ( ( $remaining > 0 ) && ( $row = $dbr->fetchObject($res) ) ) {
				$has = true;
	
				if ( $this->unique && $row->rc_namespace >= 0 ) { 
					$k = $row->rc_namespace . ':' . $row->rc_title;
					if ( isset( $ignore[$k] ) ) continue;
					$ignore[$k] = true;
				}
		
				$t = $this->renderRow( $row );
				$text .= trim($t) . "\n"; #FIXME: handle blank lines at the end sanely. Paragraphs may be desired, but not when using lists.
				$remaining -= 1;
			}
	
			$dbr->freeResult( $res );
	
			if ( !$has ) break; #empty result set, stop trying 
		}
		
		if ( $this->usetemplate ) { #it's wikitext, parse
			$output = $this->templateparser->parse( $text, $wgTitle, $this->templateoptions, true );
			$html =  $output->getText();
		}
		else { #it's already html
			$text .= $this->changelist->endRecentChangesList();
			$html = $text;
		}
	
		return $html;
	}
	
	function renderRow( $row ) {
		global $wgUser, $wgLang;
	
		$change = RecentChange::newFromRow( $row );
		$change->counter = 0; //hack
	
		if ( !$this->usetemplate ) {
			#$pagelink = $this->skin->makeKnownLinkObj( $title );
		
			$this->changelist->insertDateHeader($dummy, $row->rc_timestamp); #dummy call to suppress date headers
			$html = $this->changelist->recentChangesLine( $change );

			return $html;
		}
		else {
			$params = array();

			$params['namespace'] = $row->rc_namespace;
			$params['title'] = $row->rc_title;

			$title = $change->getTitle();
			$params['pagename'] = $title->getPrefixedText();

			$params['minor'] = $row->rc_minor ? 'true' : '';
			$params['bot'] = $row->rc_bot ? 'true' : '';
			$params['patrolled'] = $row->rc_patrolled ? 'true' : '';
			$params['anon'] = ( $row->rc_user <= 0 ) ? 'true' : ''; #TODO: perhaps use (rc_user == rc_ip) instead? That would take care of entries from importing.
			$params['new'] = ( $row->rc_type == RC_NEW ) ? 'true' : '';

			$params['type'] = $row->rc_type;
			$params['user'] = $row->rc_user_text;
			
			$params['rawtime'] = $row->rc_timestamp;
			$params['time'] = $wgLang->time( $row->rc_timestamp, true, true );
			$params['date'] = $wgLang->date( $row->rc_timestamp, true, true );
			$params['timeanddate'] = $wgLang->timeanddate( $row->rc_timestamp, true, true );

			$params['old_len'] = $row->rc_old_len;
			$params['new_len'] = $row->rc_new_len;

			$params['old_rev'] = $row->rc_last_oldid;
			$params['new_rev'] = $row->rc_this_oldid;

			$diffq = $change->diffLinkTrail( false );
			$params['diff'] = $diffq ? $title->getFullURL( $diffq ) : '';

			$permaq = "oldid=" . $row->rc_this_oldid;
			$params['permalink'] = $permaq ? $title->getFullURL( $permaq ) : '';

			$params['comment'] = str_replace( array( '{{', '}}', '|', '\'' ), array( '&#123;&#123;', '&#125;&#125;', '&#124;', '$#39;' ), wfEscapeWikiText( $row->rc_comment ) );
	
			$text = $this->templateparser->replaceVariables( $this->templatetext, $params );
			return $text;
		}
	}
}

?>