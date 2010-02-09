<?php
class RefHelper extends SpecialPage {
	function __construct() {
		parent::__construct( 'RefHelper','edit',true,false,'default',false );
		wfLoadExtensionMessages('RefHelper');
	}
	/** A simple helper function to output the html of a table row with an input box.
		@param	$out		$wgOut should be passed
		@param	$varname	the string of the GET variable name
		@param	$varval		the value of the GET variable name
		@param	$label		the text describing the variable
	*/
	private function addTableRow( &$out, $varname, $varval, $label ) {
		$out->addHTML( "<tr>\n\t<td class='mw-label'><label for='$varname'>$label:</label></td>\n");
		$out->addHTML( "\t<td class='mw-input'><input name='$varname' size='50' value='$varval' type='text' id='inp_$varname'/></td></tr>\n");
	}
	/** Another simple helper function to output the html of a table row, but with two input boxes.
		See addTableRow for parameter details
	*/
	private function add2ColTableRow( &$out, $varname1, $varname2, $varval1, $varval2, $label1, $label2 ) {
		$out->addHTML( "<tr>\n\t<td class='mw-label'><label for='author2'>$label1:</label></td>\n");
		$out->addHTML( "\t<td class='mw-input'>");
		$out->addHTML( "<input name='$varname1' size='15' value='$varval1' type='text' id='inp_$varname1' oninput='updateFirstName(event)'>");
		$out->addHTML( " $label2: ");
		$out->addHTML("<input name='$varname2' size='20' value='$varval2' type='text' id='inp_$varname2' oninput='updateSurname(event)'>");
		$out->addHTML( "</td></tr>\n");
	}

	/** Create the html body and (depending on the GET variables) creates the page.
	*/ 
	function execute( $par ) {
		global $wgRequest, $wgOut;
	 
		$this->setHeaders();
	
		# Get request data from, e.g.
		$action = $wgRequest->getText('action');
		$refname = htmlentities($wgRequest->getText('refname'));
		$author1 = htmlentities($wgRequest->getText('author1'), ENT_COMPAT, "UTF-8");
		$author2 = htmlentities($wgRequest->getText('author2'), ENT_COMPAT, "UTF-8");
		$author3 = htmlentities($wgRequest->getText('author3'), ENT_COMPAT, "UTF-8");
		$author4 = htmlentities($wgRequest->getText('author4'), ENT_COMPAT, "UTF-8");
		$author5 = htmlentities($wgRequest->getText('author5'), ENT_COMPAT, "UTF-8");

		$surname1 = htmlentities($wgRequest->getText('surname1'), ENT_COMPAT, "UTF-8");
		$surname2 = htmlentities($wgRequest->getText('surname2'), ENT_COMPAT, "UTF-8");
		$surname3 = htmlentities($wgRequest->getText('surname3'), ENT_COMPAT, "UTF-8");
		$surname4 = htmlentities($wgRequest->getText('surname4'), ENT_COMPAT, "UTF-8");
		$surname5 = htmlentities($wgRequest->getText('surname5'), ENT_COMPAT, "UTF-8");

		$pmid = htmlentities($wgRequest->getText('pmid'), ENT_COMPAT, "UTF-8");

		$articletitle = htmlentities($wgRequest->getText('articletitle'));
		$journal = htmlentities($wgRequest->getText('journal'));
		$volume = htmlentities($wgRequest->getText('volume'));
		$pages = htmlentities($wgRequest->getText('pages'));
		$year = htmlentities($wgRequest->getText('year'));

		$cat1 = htmlentities($wgRequest->getText('cat1'));
		$cat2 = htmlentities($wgRequest->getText('cat2'));
		$cat3 = htmlentities($wgRequest->getText('cat3'));
		$cat4 = htmlentities($wgRequest->getText('cat4'));

		$reqfilled = strlen($author1) && strlen($articletitle) && strlen($journal) && strlen($year) && strlen($refname); 
		if( $action!="submit" || !$reqfilled ) {
			if( strlen($pmid) ) {
				$result = RefSearch::query_pmid($pmid);
				$articletitle = $result["title"];
				$journal	= $result["journal"];
				$volume		= $result["volume"];
				$pages		= $result["pages"];
				$year		= $result["year"];
				$auths		= $result["authors"];
				if( isset( $auths[0] ) ) {
					$author1 	=  $auths[0][0];
					$surname1	=  $auths[0][1];
				}
				if( isset( $auths[1] ) ) {
					$author2 	=  $auths[1][0];
					$surname2	=  $auths[1][1];
				}
				if( isset( $auths[2] ) ) {
					$author3 	=  $auths[2][0];
					$surname3	=  $auths[2][1];
				}
				if( isset( $auths[3] ) ) {
					$author4 	=  $auths[3][0];
					$surname4	=  $auths[3][1];
				}
				if( isset( $auths[4] ) ) {
					$author5 	=  $auths[4][0];
					$surname5	=  $auths[4][1];
				}
			}
 
			# Output
			$wgOut->addHTML( "<fieldset>\n" );
			$wgOut->addHTML( "<legend>Create New Reference</legend>\n");
			$wgOut->addHTML( '<form enctype="multipart/form-data" method="post" action="/w/index.php?title=Special:RefHelper&amp;action=submit" id="mw-create-ref-form"><input name="action" value="submit" type="hidden"/><table id="mw-import-table"><tbody>'."\n");
	
			$wgOut->addHTML( "<tr>\n\t<td class='mw-label'>Workspace (copy and paste area):</td>\n");
			$wgOut->addHTML( "\t<td class='mw-input'><textarea id='inp_pastearea' rows='5' cols='20' size='50' oninput='autoPopulateRefFields()'></textarea></td></tr>\n");
	
			$wgOut->addHTML( "<tr>\n\t<td class='mw-label'><label for='pmid'>PMID:</label></td>\n");
			$wgOut->addHTML( "\t<td class='mw-input'>");
			$wgOut->addHTML( "<input name='pmid' size='15' value='$pmid' type='text' id='inp_pmid' oninput='updateFirstName(event)'>");
			$wgOut->addHTML( "");
			$wgOut->addHTML( "</td></tr>\n");
			self::addTableRow( $wgOut, "articletitle", $articletitle, wfMsg('title') );
	
			self::add2ColTableRow( $wgOut, 'author1', 'surname1', $author1, $surname1, 
				wfMsg('label_authorforename','1'),wfMsg('label_authorsurname','1'));
			self::add2ColTableRow( $wgOut, 'author2', 'surname2', $author1, $surname2, 
				wfMsg('label_authorforename','2'),wfMsg('label_authorsurname','2'));
			self::add2ColTableRow( $wgOut, 'author3', 'surname3', $author1, $surname3, 
				wfMsg('label_authorforename','3'),wfMsg('label_authorsurname','3'));
			self::add2ColTableRow( $wgOut, 'author4', 'surname4', $author1, $surname4, 
				wfMsg('label_authorforename','4'),wfMsg('label_authorsurname','4'));
			self::add2ColTableRow( $wgOut, 'author5', 'surname5', $author1, $surname5, 
				wfMsg('label_authorforename','5'),wfMsg('label_authorsurname','5'));
	
			self::addTableRow( $wgOut, "articletitle", $articletitle, wfMsg('title') );
			self::addTableRow( $wgOut, "journal", $journal, wfMsg('journal') );
			self::addTableRow( $wgOut, "pages", $pages, wfMsg('pages') );
			self::addTableRow( $wgOut, "year", $year, wfMsg('year') );
			self::addTableRow( $wgOut, "refname", $refname, wfMsg('refname') );
			self::addTableRow( $wgOut, "cat1", $cat1, wfMsg('category','1') );
			self::addTableRow( $wgOut, "cat2", $cat2, wfMsg('category','2') );
			self::addTableRow( $wgOut, "cat3", $cat3, wfMsg('category','3') );
			self::addTableRow( $wgOut, "cat4", $cat4, wfMsg('category','4') );
	
	
			$wgOut->addHTML( "<tr><td class=\"mw-submit\"><input value=\"Create\" type=\"submit\"></td>\n");
			$wgOut->addHTML( '				</tr></tbody></table></form></fieldset>'."\n" );
		}
		else
		{
			$db = wfGetDB(DB_MASTER);

			$citeTitle = Title::newFromText($refname, NS_CITE );
			$pageTitle = Title::newFromText($refname);

			$citeExists = $citeTitle->exists();
			$pageExists = $pageTitle->exists();

			if( $citeExists==FALSE )
			{
				$newcontent = "";
				$newcontent .= ":<onlyinclude>{{cite journal\n";
				$newcontent .= "| first = $author1\n";
				$newcontent .= "| last = $surname1\n";

				if( strlen( $author2 ) || strlen( $surname2 )) {
					$newcontent .= "| first2 = $author2\n";
					$newcontent .= "| last2 = $surname2\n";
				}
				if( strlen( $author3 ) || strlen( $surname3 )) {
					$newcontent .= "| first3 = $author3\n";
					$newcontent .= "| last3 = $surname3\n";
				}
				if( strlen( $author4 ) || strlen( $surname4 )) {
					$newcontent .= "| first4 = $author4\n";
					$newcontent .= "| last4 = $surname4\n";
				}
				if( strlen( $author5 ) || strlen( $surname5 )) {
					$newcontent .= "| first5 = $author5\n";
					$newcontent .= "| last5 = $surname5\n";
				}

				$newcontent .= "| title = [[$refname|$articletitle]]\n";
				$newcontent .= "| journal = $journal\n";
				$newcontent .= "| volume = $volume\n";
				$newcontent .= "| pages = $pages\n";
				$newcontent .= "| pmid = $pmid\n";
				$newcontent .= "| date = $year}}</onlyinclude>\n";
				$newcontent .= "{{Publication citation}}\n";

				$citePage = new Article( $citeTitle );
				$citePage->doEdit( $newcontent, "Automated page creation." );
				$rev_id = $citePage->insertOn( $db );

				$wgOut->addWikiText("Success! The page [[Cite:$refname]] didn't exist yet.\n");
			}
			else {
				$wgOut->addWikiText("The page [[Cite:$refname]] already exists!\n");
			}
			if( $pageExists==FALSE ) {
				$newcontent = "";
				$newcontent .= "==Citation==\n";
				$newcontent .= "{{ToCite}}\n";
				$newcontent .= "[[Category:References]]\n";
				if( strlen( $cat1 ) ) $newcontent .= "[[Category:$cat1]]\n";
				if( strlen( $cat2 ) ) $newcontent .= "[[Category:$cat2]]\n";
				if( strlen( $cat3 ) ) $newcontent .= "[[Category:$cat3]]\n";
				if( strlen( $cat4 ) ) $newcontent .= "[[Category:$cat4]]\n";

				$newPage = new Article( $pageTitle );
				$newPage->doEdit( $newcontent, "Automated page creation." );
				$rev_id = $newPage->insertOn( $db );

				$wgOut->addWikiText("Success! The page [[$refname]] didn't exist yet.");
			}
			else {
				$wgOut->addWikiText("The page [[$refname]] already exists!");
			}

			$wgOut->addWikiText("[[Special:RefHelper|Create New Reference]]");
		}
	}
}

