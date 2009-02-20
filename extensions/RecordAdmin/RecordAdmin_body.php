<?php
/**
 * Define a new class based on the SpecialPage class
 */
class SpecialRecordAdmin extends SpecialPage {

	var $form = '';
	var $type = '';
	var $types = array();
	var $orderby = '';
	var $desc = false;
	var $guid = '';

	function __construct() {
		# Name to use for creating a new record either via RecordAdmin or a public form
		# todo: should add a hook here for custom default-naming
		$this->guid = strftime( '%Y%m%d', time() ) . '-' . substr( strtoupper( uniqid() ), -5 );
		wfLoadExtensionMessages ( 'RecordAdmin' );
		SpecialPage::SpecialPage( 'RecordAdmin', 'recordadmin' );
	}

	/**
	 * Override SpecialPage::execute()
	 */
	function execute( $param ) {
		global $wgOut, $wgRequest, $wgRecordAdminUseNamespaces, $wgLang;
		$this->setHeaders();
		$type     = $wgRequest->getText( 'wpType' ) or $type = $param;
		$record   = $wgRequest->getText( 'wpRecord' );
		$invert   = $wgRequest->getText( 'wpInvert' );
		$title    = Title::makeTitle( NS_SPECIAL, 'RecordAdmin' );
		$wpTitle  = trim( $wgRequest->getText( 'wpTitle' ) );

		if ( $type && $wgRecordAdminUseNamespaces ) {
			if ( $wpTitle && !ereg( "^$type:.+$", $wpTitle ) ) $wpTitle = "$type:$wpTitle";
		}

		$wgOut->addHTML( $wgLang->pipeList( array(
			"<div class='center'><a href='" . $title->getLocalURL() . "/$type'>" . wfMsg( 'recordadmin-newsearch', $type ) . "</a>",
			"<a href='" . $title->getLocalURL() . "'>" . wfMsg( 'recordadmin-newrecord' ) . "</a></div><br>\n"
		) ) );

		# Get posted form values if any
		$posted = array();
		foreach ( $_POST as $k => $v ) if ( ereg( '^ra_(.+)$', $k, $m ) ) $posted[$m[1]] = $v;

		# Read in and prepare the form for this record type if one has been selected
		if ( $type ) $this->preProcessForm( $type );

		# Extract the input names and types used in the form
		$this->examineForm();

		# Clear any default values
		$this->populateForm( array() );

		# If no type selected, render select list of record types from Category:Records
		if ( empty( $type ) ) {
			$wgOut->addWikiText( "==" . wfMsg( 'recordadmin-select' ) . "==\n" );

			# Get titles in 'recordadmin-category' (default: Category:Records) and build option list
			$options = '';
			$dbr  = wfGetDB( DB_SLAVE );
			$cl   = $dbr->tableName( 'categorylinks' );
			$cat  = $dbr->addQuotes( wfMsgForContent( 'recordadmin-category' ) );
			$res  = $dbr->select( $cl, 'cl_from', "cl_to = $cat", __METHOD__, array( 'ORDER BY' => 'cl_sortkey' ) );
			while ( $row = $dbr->fetchRow( $res ) ) $options .= '<option>' . Title::newFromID( $row[0] )->getText() . '</option>';

			# Render type-selecting form
			$wgOut->addHTML( Xml::element( 'form', array( 'action' => $title->getLocalURL( 'action=submit' ), 'method' => 'post' ), null )
				. "<select name='wpType'>$options</select> "
				. Xml::element( 'input', array( 'type' => 'submit', 'value' => wfMsg( 'recordadmin-submit' ) ) )
				. '</form>'
			);
		}

		# Record type known, but no record selected, render form for searching or creating
		elseif ( empty( $record ) ) {
			$wgOut->addWikiText( "==" . wfMsg( 'recordadmin-create', $type ) . "==\n" );

			# Process Create submission
			if ( count( $posted ) && $wgRequest->getText( 'wpCreate' ) ) {
				if ( empty( $wpTitle ) ) {
					$wpTitle = $this->guid;
					if ( $wgRecordAdminUseNamespaces ) $wpTitle = "$type:$wpTitle";
				}
				$t = Title::newFromText( $wpTitle );
				if ( is_object( $t ) ) {
					if ( $t->exists() ) $wgOut->addHTML( "<div class='errorbox'>" . wfMsg( 'recordadmin-alreadyexist' , $wpTitle ) . "</div>\n" );
					else {

						# Attempt to create the article
						$article = new Article( $t );
						$summary = "[[Special:RecordAdmin/$type|RecordAdmin]]:" . wfMsg( 'recordadmin-summary-typecreated' );
						$text = '';
						foreach ( $posted as $k => $v ) if ( $v ) {
							if ( $this->types[$k] == 'bool' ) $v = 'yes';
							$text .= "| $k = $v\n";
						}
						$text = $text ? "{{" . "$type\n$text}}" : "{{" . "$type}}";
						$success = $article->doEdit( $text, $summary, EDIT_NEW );

						# Report success or error
						if ( $success ) $wgOut->addHTML( "<div class='successbox'>" . wfMsg( 'recordadmin-createsuccess', $wpTitle ) . "</div>\n" );
						else $wgOut->addHTML( "<div class='errorbox'>" . wfMsg( 'recordadmin-createerror', $type ) . "</div>\n" );
					}
				} else $wgOut->addHTML( "<div class='errorbox'>" . wfMsg( 'recordadmin-badtitle' ) . "</div>\n" );
				$wgOut->addHTML( "<br><br><br><br>\n" );
			}

			# Populate the search form with any posted values
			$this->populateForm( $posted );

			# Render the form
			$wgOut->addHTML(
				Xml::element( 'form', array( 'class' => strtolower($type).'-record recordadmin', 'action' => $title->getLocalURL( 'action=submit' ), 'method' => 'post' ), null )
				. '<b>' . wfMsg( 'recordadmin-recordid' ) . '</b> ' . Xml::element( 'input', array( 'name' => 'wpTitle', 'size' => 30, 'value' => $wpTitle ) )
				. '&nbsp;&nbsp;&nbsp;' . Xml::element( 'input', array( 'name' => 'wpInvert', 'type' => 'checkbox' ) ) . ' ' . wfMsg( 'recordadmin-invert' )
				. "\n<br><br><hr><br>\n{$this->form}"
				. Xml::element( 'input', array( 'type' => 'hidden', 'name' => 'wpType', 'value' => $type ) )
				. '<br><hr><br><table width="100%"><tr>'
				. '<td>' . Xml::element( 'input', array( 'type' => 'submit', 'name' => 'wpFind', 'value' => wfMsg( 'recordadmin-buttonsearch' ) ) ) . '</td>'
				. '<td>' . Xml::element( 'input', array( 'type' => 'submit', 'name' => 'wpCreate', 'value' => wfMsg( 'recordadmin-buttoncreate' ) ) ) . '</td>'
				. '<td width="100%" align="left">' . Xml::element( 'input', array( 'type' => 'reset', 'value' => wfMsg( 'recordadmin-buttonreset' ) ) ) . '</td>'
				. '</tr></table></form>'
			);

			# Process Find submission
			if ( count( $posted ) && $wgRequest->getText( 'wpFind' ) ) {
				$wgOut->addWikiText( "<br>\n== " . wfMsg( 'recordadmin-searchresult' ) . " ==\n" );

				# Select records which use the template and exhibit a matching title and other fields
				$records = $this->getRecords( $type, $posted, $wpTitle, $invert );

				# Render resulting records
				$wgOut->addHTML( $this->renderRecords($records) );

			}
		}

		# A specific record has been selected, render form for updating
		else {
			$wgOut->addWikiText( "== " . wfMsg( 'recordadmin-edit', $record ) . " ==\n" );
			$article = new Article( Title::newFromText( $record ) );
			$text = $article->fetchContent();

			# Update article if form posted
			if ( count( $posted ) ) {

				# Get the location and length of the record braces to replace
				foreach ( $this->examineBraces( $text ) as $brace ) if ( $brace['NAME'] == $type ) $braces = $brace;

				# Attempt to save the article
				$summary = "[[Special:RecordAdmin/$type|" . wfMsgForContent( 'recordadmin' ) . "]]: " . wfMsgForContent( 'recordadmin-typeupdated', $type );
				$replace = '';
				foreach ( $posted as $k => $v ) if ( $v ) {
					if ( $this->types[$k] == 'bool' ) $v = 'yes';
					$replace .= "| $k = $v\n";
				}
				$replace = $replace ? "{{" . "$type\n$replace}}" : "{{" . "$type}}";
				$text = substr_replace( $text, $replace, $braces['OFFSET'], $braces['LENGTH'] );
				$success = $article->doEdit( $text, $summary, EDIT_UPDATE );

				# Report success or error
				if ( $success ) $wgOut->addHTML( "<div class='successbox'>" . wfMsg( 'recordadmin-updatesuccess', $type ) . "</div>\n" );
				else $wgOut->addHTML( "<div class='errorbox'>" . wfMsg( 'recordadmin-updateerror' ) . "</div>\n" );
				$wgOut->addHTML( "<br><br><br><br>\n" );
			}

			# Populate the form with the current values in the article
			foreach ( $this->examineBraces( $text ) as $brace ) if ( $brace['NAME'] == $type ) $braces = $brace;
			$this->populateForm( substr( $text, $braces['OFFSET'], $braces['LENGTH'] ) );

			# Render the form
			$wgOut->addHTML( Xml::element( 'form', array( 'class' => 'recordadmin', 'action' => $title->getLocalURL( 'action=submit' ), 'method' => 'post' ), null ) );
			$wgOut->addHTML( $this->form );
			$wgOut->addHTML( Xml::element( 'input', array( 'type' => 'hidden', 'name' => 'wpType', 'value' => $type ) ) );
			$wgOut->addHTML( Xml::element( 'input', array( 'type' => 'hidden', 'name' => 'wpRecord', 'value' => $record ) ) );
			$wgOut->addHTML( '<br><hr><br><table width="100%"><tr>'
				. '<td>' . Xml::element( 'input', array( 'type' => 'submit', 'value' => wfMsg( 'recordadmin-buttonsave' ) ) ) . '</td>'
				. '<td width="100%" align="left">' . Xml::element( 'input', array( 'type' => 'reset', 'value' => wfMsg( 'recordadmin-buttonreset' ) ) ) . '</td>'
				. '</tr></table></form>'
			);
		}
	}

	/**
	 * Return an array of records given type and other criteria
	 */
	function getRecords($type, $posted, $wpTitle = '', $invert = false, $orderby = 'created desc') {
		$records = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$tbl  = $dbr->tableName( 'templatelinks' );
		$ty   = $dbr->addQuotes( $type );
		$res  = $dbr->select( $tbl, 'tl_from', "tl_namespace = 10 AND tl_title = $ty", __METHOD__ );
		while ( $row = $dbr->fetchRow( $res ) ) {
			$t = Title::newFromID( $row[0] );
			if ( empty( $wpTitle ) || eregi( $wpTitle, $t->getPrefixedText() ) ) {
				$a = new Article( $t );
				$text = $a->getContent();
				$match = true;
				$r = array( 0 => $t, 'title' => $t->getPrefixedText() );
				foreach ( array_keys( $this->types ) as $k ) {
					$v = isset( $posted[$k] ) ? ( $this->types[$k] == 'bool' ? 'yes' : $posted[$k] ) : '';
					$i = preg_match( "|\s*\|\s*$k\s*=\s*(.*?)\s*(?=[\|\}])|si", $text, $m );
					if ( $v && !( $i && eregi( $v, $m[1] ) ) ) $match = false;
					$r[$k] = isset( $m[1] ) ? $m[1] : '';
				}
				if ( $invert ) $match = !$match;
				if ( $match ) $records[] = $r;
			}
		}
		$dbr->freeResult( $res );
		
		# Add the creation date column to the records
		foreach ( $records as $i => $r ) {
			$t = $r[0];
			$id = $t->getArticleID();
			$tbl = $dbr->tableName( 'revision' );
			$row = $dbr->selectRow(
				$tbl,
				'rev_timestamp',
				"rev_page = $id",
				__METHOD__,
				array( 'ORDER BY' => 'rev_timestamp' )
			);
			$records[$i]['created'] = $row->rev_timestamp;
		}

		# Sort the records according to "orderby" parameter
		if ($this->desc = eregi(' +desc *$', $orderby)) $orderby = eregi_replace(' +desc *$', '', $orderby);
		$this->orderby = $orderby;
		usort($records, array($this, 'sortCallback'));

		return $records;
	}
	
	/**
	 * Compares to arrays by column
	 */
	function sortCallback($row1, $row2) {
		if (!isset($row1[$this->orderby]) || !isset($row1[$this->orderby])) return 0;
		if ($row1[$this->orderby] == $row2[$this->orderby]) return 0;
		$cmp = $row1[$this->orderby] > $row2[$this->orderby] ? 1 : -1;
		return $this->desc ? -$cmp : $cmp;
	}

	/**
	 * Render a set of records returned by getRecords() as an HTML table
	 */
	function renderRecords($records, $cols = false, $sortable = true) {
		if (count($records) < 1) return wfMsg( 'recordadmin-nomatch' );

		$special = Title::makeTitle( NS_SPECIAL, 'RecordAdmin' );
		$type = $this->type;
		$sortable = $sortable ? ' sortable' : '';
		$br = $sortable ? '<br />' : '';

		# Table header
		$table = "<table class='recordadmin$sortable $type-record'>\n<tr>";
		$th = array(
			'title'   => "<th class='col0'>" . wfMsg( 'recordadmin-title', $type ) . "$br</th>",
			'actions' => "<th class='col1'>" . wfMsg( 'recordadmin-actions' ) . "$br</th>",
			'created' => "<th class='col2'>" . wfMsg( 'recordadmin-created' ) . "$br</th>"
		);
		foreach ( array_keys($this->types) as $col ) {
			$class = 'col'.preg_replace('|\W|', '-', $col);
			$th[$col] = "<th class='$class'>$col$br</th>";
		}
		foreach ( $cols ? $cols : array_keys($th) as $col ) $table .= $th[$col]."\n";
		$table .= "</tr>\n";

		$stripe = '';
		foreach ( $records as $r ) {
			$ts  = preg_replace( '|^..(..)(..)(..)(..)(..)..$|', '$3/$2/$1&nbsp;$4:$5', $r['created'] );
			$t   = $r[0];
			$u   = $t->getLocalURL();
			$col = $r['title'];
			$stripe = $stripe ? '' : ' class="stripe"';
			$table .= "<tr$stripe>";
			$row = array(
				'title'   => "<td class='col0'><a href='$u'>$col</a></td>",
				'actions' => "<td class='col1'>(<a href='$u'>" . wfMsg( 'recordadmin-viewlink' ) . "</a>)".
				             "(<a href='" . $special->getLocalURL( "wpType=$type&wpRecord=$col" ) . "'>" . wfMsg( 'recordadmin-editlink' ) . "</a>)</td>",
				'created' => "<td class='col2'>$ts</td>\n"
			);
			foreach ( array_keys( $this->types ) as $col ) {
				$v = isset( $r[$col] ) ? $r[$col] : '&nbsp;';
				$class = 'col'.preg_replace('|\W|', '-', $col);
				$row[$col] = "<td class='$class'>$v</td>";
			}
			foreach ($cols ? $cols : array_keys($th) as $col) $table .= $row[$col]."\n";
			
			$table .= "</tr>\n";
		}
		$table .= "</table>\n";

		return $table;
	}

	/**
	 * Read in and prepare the form (for use as a search filter) for passed record type
	 * - we're using the record's own form as a filter for searching for records
	 * - extract only the content from between the form tags and remove any submit inputs
	 */
	function preProcessForm( $type ) {
		$this->type = $type;
		$title = Title::newFromText( $type, NS_FORM );
		if ( $title->exists() ) {
			$form = new Article( $title );
			$form = $form->getContent();
			$form = preg_replace( '#<input.+?type=[\'"]?submit["\']?.+?/(input| *)>#', '', $form );    # remove submits
			$form = preg_replace( '#^.+?<form.+?>#s', '', $form );                                     # remove up to and including form open
			$form = preg_replace( '#</form>.+?$#s', '', $form );                                       # remove form close and everything after
			$form = preg_replace( '#name\s*=\s*([\'"])(.*?)\\1#s', 'name="ra_$2"', $form );            # prefix input names with ra_
			$form = preg_replace( '#(<select.+?>)\s*(?!<option/>)#s', '$1<option selected/>', $form ); # ensure all select lists have default blank
		}

		# Create a red link to the form if it doesn't exist
		else {
			$form = "<b>" . wfMsg( 'recordadmin-noform', $type ) . "</b>"
			       . "<br><br>" . wfMsg( 'recordadmin-createlink', $title->getLocalURL( 'action=edit' ) ) . "</div>";
		}
		$this->form = $form;
	}


	/**
	 * Populates the form values from the passed values
	 * - $form is HTML text
	 * - $values may be a hash or wikitext template syntax
	 */
	function populateForm( $values ) {

		# If values are wikitext, convert to hash
		if ( !is_array( $values ) ) {
			$text = $values;
			$values = array();
			preg_match_all( "|\|\s*(.+?)\s*=\s*(.*?)\s*(?=[\|\}])|s", $text, $m );
			foreach ( $m[1] as $i => $k ) $values[$k] = $m[2][$i];
		}

		# Add the values into the form's HTML depending on their type
		foreach ( $this->types as $k => $type ) {

			# Get this input element's html text and position and length
			preg_match( "|<([a-zA-Z]+)[^<]+?name=\"ra_$k\".*?>(.*?</\\1>)?|s", $this->form, $m, PREG_OFFSET_CAPTURE );
			list( $html, $pos ) = $m[0];
			$len = strlen( $html );

			# Modify the element according to its type
			# - clears default value, then adds new value
			$v = isset( $values[$k] ) ? $values[$k] : '';
			switch ( $type ) {
				case 'text':
					$html = preg_replace( "|value\s*=\s*\".*?\"|", "", $html );
					if ( $v ) $html = preg_replace( "|(/?>)$|", " value=\"$v\" $1", $html );
				break;
				case 'bool':
					$html = preg_replace( "|checked|", "", $html );
					if ( $v ) $html = preg_replace( "|(/?>)$|", " checked $1", $html );
				break;
				case 'list':
					$html = preg_replace( "|(<option[^<>]*) selected|", "$1", $html );
					if ( $v ) $html = preg_replace( "|(?<=<option)(?=>$v</option>)|s", " selected", $html );
				break;
				case 'blob':
					$html = preg_replace( "|>.*?(?=</textarea>)|s", ">$v", $html );
				break;
			}

			# Replace the element in the form with the modified html
			$this->form = substr_replace( $this->form, $html, $pos, $len );
		}
	}

	/**
	 * Returns an array of types used by the passed HTML text form
	 * - supported types, text, select, checkbox, textarea
	 */
	function examineForm() {
		$this->types = array();
		preg_match_all( "|<([a-zA-Z]+)[^<]+?name=\"ra_(.+?)\".*?>|", $this->form, $m );
		foreach ( $m[2] as $i => $k ) {
			$tag = $m[1][$i];
			$type = preg_match( "|type\s*=\s*\"(.+?)\"|", $m[0][$i], $n ) ? $n[1] : '';
			switch ( $tag ) {
				case 'input':
					switch ( $type ) {
						case 'checkbox':
							$this->types[$k] = 'bool';
						break;
						default:
							$this->types[$k] = 'text';
						break;
					}
				break;
				case 'select':
					$this->types[$k] = 'list';
				break;
				case 'textarea':
					$this->types[$k] = 'blob';
				break;
			}
		}
	}

	/**
	 * Return array of braces used and the name, position, length and depth
	 * See http://www.organicdesign.co.nz/MediaWiki_code_snippets
	 */
	function examineBraces( &$content ) {
		$braces = array();
		$depths = array();
		$depth = 1;
		$index = 0;
		while ( preg_match( "/\\{\\{\\s*([#a-z0-9_]*)|\\}\\}/is", $content, $match, PREG_OFFSET_CAPTURE, $index ) ) {
			$index = $match[0][1] + 2;
			if ( $match[0][0] == '}}' ) {
				$brace =& $braces[$depths[$depth - 1]];
				$brace['LENGTH'] = $match[0][1] - $brace['OFFSET'] + 2;
				$brace['DEPTH']  = $depth--;
			}
			else {
				$depths[$depth++] = count( $braces );
				$braces[] = array(
					'NAME'   => $match[1][0],
					'OFFSET' => $match[0][1]
				);
			}
		}
		return $braces;
	}

	/**
	 * A callback for processing public forms
	 */
	function createRecord() {
		global $wgRequest, $wgRecordAdminUseNamespaces;
		$type = $wgRequest->getText( 'wpType' );
		$title = $wgRequest->getText( 'wpTitle' );

		# Get types in this kind of record from form
		$this->preProcessForm( $type );
		$this->examineForm();

		# Use guid if no title specified
		if ( empty( $title ) ) {
			$title = $this->guid;
			if ( $wgRecordAdminUseNamespaces ) $title = "$type:$title";
		}

		# Attempt to create the article
		$title = Title::newFromText( $title );
		if ( is_object( $title ) && !$title->exists() ) {
			$article = new Article( $title );
			$summary = wfMsg( 'recordadmin-newcreated' );
			$text = '';
			foreach ( $_POST as $k => $v ) if ( $v && isset( $this->types[$k] ) ) {
				if ( $this->types[$k] == 'bool' ) $v = 'yes';
				$text .= "| $k = $v\n";
			}
			$text = $text ? "{{" . "$type\n$text}}" : "{{" . "$type}}";
			$success = $article->doEdit( $text, $summary, EDIT_NEW );
		}
	}

	/**
	 * Render a record search in a parser-function
	 */
	function expandMagic(&$parser, $type) {
		$parser->mOutput->mCacheTime = -1;
		$filter   = array();
		$title    = '';
		$invert   = false;
		$orderby  = 'created desc';
		$cols     = false;
		$sortable = true;
		foreach (func_get_args() as $arg) if (!is_object($arg)) {
			if (preg_match("/^(.+?)\\s*=\\s*(.+)$/", $arg, $match)) {
				list(, $k, $v) = $match;
				if ($k == 'title') $title = $v;
				elseif ($k == 'invert')   $invert   = $v;
				elseif ($k == 'orderby')  $orderby  = $v;
				elseif ($k == 'cols')     $cols     = preg_split('/\s*,\s*/', $v);
				elseif ($k == 'sortable') $sortable = eregi('1|yes|true|on', $v);
				else $filter[$match[1]] = $match[2];
			}
		}
		$this->preProcessForm($type);
		$this->examineForm();
		$records = $this->getRecords($type, $filter, $title, $invert, $orderby);
		$table = $this->renderRecords($records, $cols, $sortable);
		return array(
			$table,
			'noparse' => true,
			'isHTML' => true
		);
		
	}

	/**
	 * If a record was created by a public form, make last 5 digits of ID available via a tag
	 */
	function expandTag( $text, $argv, &$parser ) {
		$parser->mOutput->mCacheTime = -1;
		return $this->guid ? substr( $this->guid, -5 ) : '';
	}

}
