<?php
/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */

/**
 * Constructor
 * Can display the main form or perform addition, changes,
 * pseudonamespace conversion or deletions.
 */
function wfSpecialNamespaces() {
	global $wgUser, $wgRequest;

	$action = $wgRequest->getVal( 'action' );
	$f = new NamespaceForm();

	if ( $action == 'submit' && $wgRequest->wasPosted() &&
		$wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
		if($wgRequest->getText('nsAction')=='addnamespaces') {
			$f->addNamespaces();
		} elseif($wgRequest->getText('nsAction')=='changenamespaces') {
			$f->changeNamespaces();
		} elseif($wgRequest->getText('nsAction')=='fixpseudonamespaces') {
			$f->fixPseudonamespaces();
		}
	} elseif($action == 'delete') {
		$f->deleteNamespace();
	} else {
		$f->showForm();
	}
}

/**
*
* @package MediaWiki
* @subpackage SpecialPage
*/
class NamespaceForm {

/**
*
* This is the main namespace manager form which gives access to
* all namespace operations.
*
* @param $errorHeader if this is an error page, we need at least a headline
* @param $errorBody wikitext for extended error descriptions
*
*/
function showForm( $errorHeader='', $errorBody='' ) {
	global $wgOut, $wgUser, $wgNamespaces, $wgTitle;
	
	$wgOut->setPagetitle( wfMsg( 'namespaces' ) );

	/* In case of an error, we generally just show what went wrong
	   and continue displaying the main form */
	if ( '' != $errorHeader ) {
		$wgOut->setSubtitle( wfMsg( 'transactionerror' ) );
		$wgOut->addHTML( '<p class="error">' . htmlspecialchars($errorHeader) . '</p>');
		if($errorBody) {
			$wgOut->addWikiText($errorBody);
		}
	}
	
	# Standard token to avoid remote form submission exploits
	$token = $wgUser->editToken();
	$action = $wgTitle->escapeLocalURL( 'action=submit' );
	$talksuffix = wfEscapeJsString(wfMsgForContent('talkpagesuffix'));
	
	# For the namespace selection box
	$name_array = Namespace::getFormattedDefaultNamespaces(true);
	$noparent = wfMsg('no_parent_namespace');
	$name_array[key($name_array)-1] = $noparent;

	# Sort for foreach loops
	ksort($name_array);

	$wgOut->addWikiText( wfMsg( 'add_namespaces_header' ) );

	# Prefill talk namespace field, but only for languages 
	# where it's not disabled
	if($talksuffix != '-') {
		$talkpagejs=
' onchange="if(!window.document.addnamespaces.nsTalkName.value &amp;&amp; window.document.addnamespaces.nsName.value &amp;&amp; window.document.addnamespaces.nsCreateTalk.checked) { window.document.addnamespaces.nsTalkName.value=window.document.addnamespaces.nsName.value+\''.$talksuffix.'\'; }"';
	} else {
		$talkpagejs='';
	}

	$addnshtmlform='
<div id="addnsForm">
<form name="addnamespaces" method="post" action="'.$action.'">
<table>
	<tr valign="top">
		<td>'.wfMsg('add_namespace_default_name').'</td>
		<td>
			<input type="hidden" name="nsAction" value="addnamespaces" />
			<input type="text" name="nsName" size="20"'.$talkpagejs.' />
		</td>
	</tr>
	<tr valign="top">
		<td>'.wfMsg('add_namespace_default_talk').'<br /></td>
		<td><input type="text" name="nsTalkName" size="20" /></td>
	</tr>
	<tr>
		<td colspan="2">
		<label>
			<input type="checkbox" name="nsCreateTalk" checked="checked" />'.wfMsg('add_namespace_talk_confirm').'
		</label>
		</td>
	</tr>
</table>
<input type="hidden" name="wpEditToken" value="'.$token.'" />
<input type="submit" value="'.wfMsg('add_namespace_button').'" />
</form>
</div>
';
		$wgOut->addHTML($addnshtmlform);
			
		$wgOut->addWikiText( wfMsg( 'modify_namespaces_header' ) );

		// Array of messages to be used
		$nsMessages = array (
			'child_of', 'default_link_prefix', 'default_name', 'delete_name',
			'existing_names', 'hide_in_lists', 'names', 'new_names',
			'properties', 'save_changes', 'search_by_default', 'slot',
			'support_subpages', 'system',
		);

		// Build variables using the array. 'child_of' will do:
		// $namespace_child_of = wfMsg('namespace_child_of');
		foreach( $nsMessages as $nsMessage ) {
			$msgName = 'namespace_' . $nsMessage ;
			$$msgName = wfMsg( $msgName );
		}

		// Initialise the form
		$htmlform = <<<END
<style type="text/css">
.mwNsAnnotation {
	font-size: 80%;
	color: #a0a0a0;
}
table.mwNsNames {
	border-spacing: 0 2px;
}
table.mwNsNames tr.mwDefaultName {
	background-color: #d2d5ff;
}
</style>
<form name="changenamespaces" method="post" action="{$action}">
<input type="hidden" name="nsAction" value="changenamespaces" />
<input type="hidden" name="wpEditToken" value="{$token}" />
END;

		// Now we proceed each namespace
		$namespaceSet = $wgNamespaces; // protective copy for nested foreach bug
		foreach ($namespaceSet as $ns) {

			$index = $ns->getIndex();

			$linkprefix = $ns->getTarget();
			$parentslot = $ns->getParentIndex();		
			# maybe make HTMLnamespaceselector more flexible and use
			# it instead here
			if( !$ns->isSpecial() ) {
				$namespaceselect=$this->getSelector($name_array,$parentslot);

				// TODO : fix code below, maybe use HTMLForm ?

				$namespaceselect_html = <<<END
<tr valign="top">
	<td colspan="2">{$namespace_child_of}<br />
	<select name="ns{$index}Parent" size="1">{$namespaceselect}</select>
	</td>
</tr>
END;
				$subpages_html = $this->checkRow( 'namespace_support_subpages',
					"ns{$index}Subpages", $ns->allowsSubpages() );
				$searchdefault_html = $this->checkRow( 'namespace_search_by_default',
					"ns{$index}Search", $ns->isSearchedByDefault() );
				$hide_html = $this->checkRow( 'namespace_hide_in_lists',
					"ns{$index}Hidden", $ns->isHidden() );
				
				$target_html = <<<END
<tr valign="top">
	<td>{$namespace_default_link_prefix}</td>
	<td align="right"><input type="text" size="10" name="ns{$index}Linkprefix" value="{$linkprefix}" /></td>
</tr>
END;
				$target_html = $this->selectorRow( 'namespace_default_link_prefix',
					"ns{$index}Linkprefix",
					Namespace::getIndexForName( $ns->getTarget() ) );
				$special_html='';

			} else {
				// For special namespace
				$namespaceselect_html = '';
				$subpages_html = '';
				$searchdefault_html = '';
				$hide_html = '';
				$target_html = '';
				$special_namespace = wfMsg('special_namespace');
				$special_html = '<tr valign="top"><td colspan="2"><em>'.$special_namespace.'</em></td></tr>' . "\n";
			}


			$systemtype = $ns->getSystemType();

			if( $ns->getSystemType() ) {
				// No delete link ?
				$systemtype_html = <<<END
<tr valign="top">
	<td><b><font color="red">{$namespace_system}</font></b></td>
	<td align="right"><b>{$systemtype}</b></td>
</tr>
END;
				$deletenamespace_html = '';
			} else {
				// Give out a link to delete the namespace
				$sk = $wgUser->getSkin();
				$delete_link = $sk->makeKnownLinkObj( $wgTitle, wfMsg('delete_namespace'), 'action=delete&ns=' . $index );
				$deletenamespace_html = '<tr valign="top"><td colspan="2"><b>'.$delete_link.'</b></td></tr>' . "\n";
				$systemtype_html='';
			}


			// Yet another table of tables :p
			$htmlform .= <<<END
<table class="specialnamespaces">
<tr valign="top"><td>
	<table border="0" style="margin-right:1em;" width="300">
		<tr><th colspan="2">{$namespace_properties}</th></tr>
		<tr><td>{$namespace_slot}</td><td align="right">{$index}</td></tr>
END;
			// Also add html part generated before
			$htmlform .=
				  $systemtype_html
				. $special_html
				. $subpages_html
				. $searchdefault_html
				. $hide_html
				. $target_html
				. $namespaceselect_html
				. $deletenamespace_html
				;
$htmlform .= <<<END
	</table>
</td><td>
	<table class="mwNsNames">
		<tr><th colspan="3">{$namespace_names}</th></tr>
		<tr>
			<th>{$namespace_default_name}</th>
			<th align="left">{$namespace_existing_names}</th>
			<th>{$namespace_delete_name}</th>
		</tr>
END;

		foreach ( $ns->names as $nsi => $nsname ) {
			$isDefault = ( $nsi === $ns->getDefaultNameIndex() );
			$isCanonical = ( $nsi === $ns->getCanonicalNameIndex() );
			$prettyName = str_replace( '_', ' ', $nsname );
			
			$default = wfRadio( "ns{$index}Default", $nsi, $isDefault );
			if( $isCanonical ) {
				$nameinput = htmlspecialchars( $prettyName ) .
					$this->annotation( wfMsg( 'canonicalname' ) );
				$delete = "N/A";
			} else {
				$nameinput = wfInput( "ns{$index}Name{$nsi}", 20, $prettyName );
				if( $isDefault ) {
					$nameinput .= $this->annotation( wfMsg( 'defaultname' ) );
				}
				$delete = wfCheck( "ns{$index}Delete{$nsi}" );
			}
			$class = $isDefault ? 'mwDefaultName' : '';
			$htmlform .=
<<<END
	<tr valign="top" class="$class">
		<td align="center">{$default}</td>
		<td>{$nameinput}</td>
		<td align="center">{$delete}</td>
	</tr>
END;
		}

		$htmlform .= '<tr><th align="left" colspan="3">' . $namespace_new_names . '</th></tr>' ;

		# 3 blank namespace fields
		// FIXME cant we just count elements ?
		if( !is_null( $ns->names ) ) {
			end( $ns->names );
			$highestName = key( $ns->names ) + 1;
		} else {
			$highestName = 0;
		}

		for( $i=$highestName; $i<$highestName+3; $i++) {
			$htmlform .=
<<<END
	<tr valign="top">
		<td align="center"><input type="radio" name="ns{$index}Default" value="{$i}" /></td>
		<td><input name="ns{$index}NewName{$i}" size="20" value="" /></td>
		<td align="center">&nbsp;</td>
	</tr>

END;
		}
		$htmlform .=
<<<END
</table>
	</td></tr>
</table>

END;
	}
	$htmlform.=
<<<END
<input type="submit" value="{$namespace_save_changes}" />
</form>
<br/>
END;


	// Ouput the form
	$wgOut->addHTML( $htmlform );

	// Pseudonamespace converter
	$all_name_array = Namespace::getFormattedDefaultNamespaces(true);
	$pseudons_select=$this->getSelector($all_name_array);
	$wgOut->addWikiText( wfMsg( 'fix_pseudonamespaces_header' ) );
	$phtmlform ='
<div id="fixPseudoNsForm">
<form name="fixpseudonamepaces" method="post" action="'.$action.'">
<input type="hidden" name="nsAction" value="fixpseudonamespaces" />
<input type="hidden" name="wpEditToken" value="'.$token.'" />
<table>
	<tr valign="top">
		<td>'.wfMsgHtml('pseudonamespace_prefix').'</td>
		<td>
			<input type="text" name="nsPrefix" size="20" />
		</td>
	</tr>
	<tr valign="top">
		<td>'.wfMsgHtml('pseudonamespace_target').'<br /></td>
		<td><select name="nsConvertTo" size="1">'.$pseudons_select.'</select></td>
	</tr>
	<tr>
		<td colspan="2">
		<label>
			<input type="checkbox" name="nsConvertTalk" checked="checked" />'.wfMsgHtml('pseudonamespace_converttalk').'
		</label>
		</td>
	</tr>';
	if( $wgUser->isAllowed( 'merge_pseudonamespaces' ) ) {
		$phtmlform .= '
	<tr>
		<td colspan="2">
		<label>
			<input type="checkbox" name="nsMerge" />'.wfMsg('pseudonamespace_merge').'
		</label>
		</td>
	</tr>';
	}
	$phtmlform.='
</table>
<input type="submit" value="'.wfMsgHtml('pseudonamespace_convert').'" />
</form>
</div>';
	$wgOut->addHTML( $phtmlform );

}

	/**
	 * Add a new namespace with a single default name,
	 * and optionally a talk namespace, also with a 
	 * defaultname. Uses the request data from the form.
	*/ 
	function addNamespaces() {

		global $wgOut, $wgUser, $wgRequest;	

		$nsname = $wgRequest->getText('nsName');
		$nstalkname = $wgRequest->getText('nsTalkName');
		$nscreatetalk = $wgRequest->getBool('nsCreateTalk');

		if(empty($nsname)) {
			$this->showForm( wfMsg('namespace_name_missing') );
		}

		$ns = new Namespace();
		$newnameindex = $ns->addName($nsname);

		if( is_null($newnameindex) ) {
			$this->showForm(
				wfMsg('namespace_error',$nsname),
				wfMsg('namespace_name_illegal_characters', NS_CHAR)
			);
			return false;
		}

		$ns->setDefaultNameIndex($newnameindex);
		$nrv=$ns->testSave();
		/* 
			The only errors which can occur here should be
			name-related.
		*/
		if( $nrv[NS_RESULT] == NS_NAME_ISSUES ) {
			$this->showForm(
				wfMsg('namespace_error',$nsname),
				$this->nameIssues($nrv)
			);
			return false;
		}

		$newnamespaceindex = $nrv[NS_SAVE_ID];

		if( $nscreatetalk && !empty($nstalkname) ) {

			// Initialize a talk namespace
			$talkns = new Namespace();
			$talkns->setParentIndex( $newnamespaceindex );
			$talkns->setSubpages();
			$newtalknameindex=$talkns->addName( $nstalkname );
			$talkns->setDefaultNameIndex( $newtalknameindex );

			// attempt to create it
			$trv = $talkns->testSave();
			// Did it success ?
			if( $trv[NS_RESULT] != NS_CREATED ) {
				$this->showForm(
					wfMsg('talk_namespace_error',$nstalkname),
					$this->nameIssues($trv)
				);
				return false;
			}
		}

		// We now have validated stuff, lets save for real.
		// No logic errors should occur beyond this point.
		$ns->save();
		$complete = wfMsg( 'namespace_created', $nsname );

		if( $nscreatetalk && !empty($nstalkname) ) {
		
			$talkns->save();
			$complete .= ' '.wfMsg('talk_namespace_created');
		}

		// Report success to user
		$wgOut->addWikiText($complete);
		$this->showForm();

		$this->logNs('add',$nsname);
		if($nscreatetalk) {
			$this->logNs('add',$nstalkname);			
		}
	}

	/**
	 * Convenient access to the logging functions
	 * @param $action - 'add','delete','modify' or 'pseudo'
	 * @param $ns - name of the namespace
	 * @param $tns - for pseudonamespaces, name of the target namespace
	 */
	function logNs($action,$ns='',$tns='') {
                $log = new LogPage( 'namespace' );
		$dummyTitle = Title::makeTitle( 0, '' );
		if($action=='pseudo') {
			$log->addEntry( $action,$dummyTitle,'',array($ns,$tns));
		} elseif($action=='modify') {
			$log->addEntry( $action,$dummyTitle,'');
		} else {
                	$log->addEntry( $action,$dummyTitle,'',array($ns));
		}
	}

	/**
	 * Modify, delete or add namespace names, set default names,
	 * or change namespace properties. Uses the request data from
	 * the form. Note that we have to create a new namespace object,
	 * since we do not want to modify the "live" namespace until
	 * we know that all the requested operations can be performed.
	 * Nothing will be done unless every transaction can be completed
	 * successfully.
	*/
	function changeNamespaces() {
	
		global $wgOut, $wgNamespaces, $wgRequest;

		$newns = array();
		foreach( $wgNamespaces as $ns ) {
			$nsindex = $ns->getIndex();
			$newns[$nsindex] = new Namespace();
			$newns[$nsindex]->setIndex($nsindex);
			$newns[$nsindex]->setSystemType($ns->getSystemType());

			if(!$ns->isSpecial()) {
				// Some variables names
				$subvar = "ns{$nsindex}Subpages";
				$searchvar = "ns{$nsindex}Search";
				$hiddenvar = "ns{$nsindex}Hidden";
				$prefixvar = "ns{$nsindex}Linkprefix";
				$parentvar = "ns{$nsindex}Parent";

				// Get data submitted by user
				$subpages = $wgRequest->getBool($subvar);
				$searchdefault = $wgRequest->getBool($searchvar);
				$hidden = $wgRequest->getBool($hiddenvar);
				$prefix = $wgRequest->getText($prefixvar);
				$parent = $wgRequest->getIntOrNull($parentvar);

				// Initialise our new namespace
				$newns[$nsindex]->setSubpages($subpages);
				$newns[$nsindex]->setSearchedByDefault($searchdefault);
				$newns[$nsindex]->setHidden($hidden);
				$newns[$nsindex]->setTarget($prefix);

				if(array_key_exists($parent,$wgNamespaces)) {
					$newns[$nsindex]->setParentIndex($parent);
				}				
			}

			// Inherit namespace names
			$newns[$nsindex]->names = $ns->names;

			// This can never be changed by the user.
			$newns[$nsindex]->setCanonicalNameIndex($ns->getCanonicalNameIndex());

			// New names, appended to end
			for($i=1;$i<=3;$i++) {
				$nvar = "ns{$nsindex}NewName{$i}";
				if( $nname = $wgRequest->getText($nvar) ) {
					$newns[$nsindex]->addName($nname);
				}
			}

			# Changes and deletions. Do them last since they can
			# affect index slots of existing names.
			foreach( $ns->names as $nameindex=>$name ) {
				$var="ns{$nsindex}Name{$nameindex}";
				if($req=$wgRequest->getText($var)) {
					#wfDebug("Name var $var contains $req\n");

					# Alter name if necessary.
					if($req != $name) {
						# The last parameter means that we do not check if the
						# name is valid - this is done later for all names.
						$newns[$nsindex]->setName($name, $req, false);

						#wfDebug("Setting name $nameindex of namespace $nsindex to $req. Old name is $name.\n");
					}
				}
				$delvar = "ns{$nsindex}Delete{$nameindex}";
				if( $wgRequest->getInt($delvar) ) {
					#wfDebug("$delvar should be deleted.\n");
					$newns[$nsindex]->removeNameByIndex($nameindex);
				}
			}

			$dvar = "ns{$nsindex}Default";

			# Did the user select a default name?
			$dindex = $wgRequest->getIntOrNull($dvar);

			# If not, get the old one.
			if(is_null($dindex)) {
				$dindex=$ns->getDefaultNameIndex();
			}

			# Does the name exist and is it non-empty?
			if(
				!is_null($dindex)
				&& array_key_exists($dindex,
				$newns[$nsindex]->names)
				&& !empty($newns[$nsindex]->names[$dindex])
			) {
				# Use this default name.
				$newns[$nsindex]->setDefaultNameIndex($dindex);
			} else {
				# We have lost our default name, perhaps it 
				# was deleted. Get a new one if possible.	
				$newns[$nsindex]->setDefaultNameIndex(
				  $newns[$nsindex]->getNewDefaultNameIndex()
				);
			}
		}

		foreach($newns as $nns) {
			$nrv = $nns->testSave();
			if( $nrv[NS_RESULT] == NS_NAME_ISSUES ) {
				$this->showForm(
					wfMsg(
					  'namespace_error',
					  $nns->getDefaultName()),
					  $this->nameIssues($nrv)
					);
				return false;
			} elseif($nrv[NS_RESULT] == NS_MISSING) {
				$this->showForm(
				  wfmsg('namespace_has_gone_missing',
				  $nns->getIndex())
				);
				return false;
			}
		}

		# Only do anything if everything can be done successfully.
		foreach($newns as $nns) {
			$nns->save();
		}

		# Unfortunately, NS_IDENTICAL does not work consistently
		# atm, so we can only add a generic log entry.
		$this->logNs('modify');

		# IMPORTANT: The namespace name indexes are unpredictable when
		# serialized, so we have to reload the definitions from the
		# database at this point; otherwise, there could be index
		# mismatches.
		Namespace::load(true);

		# Return to the namespace manager with the changes made.
		$wgOut->addWikiText( wfMsg('namespace_changes_saved') );
		$this->showForm();

		return true;
	}

	/**
	 * Creates a table showing problems with namespace name changes
	 * or additions, based on result data from the save operation.
	 *
	 * @param array Namespace::save result array
	 * @return string A HTML table with namespaces issues
	*/
	function nameIssues( $result ) {

		# Initialize table with heading	
		$htmltable=
		   '<table border="0" width="100%" cellspacing="5" cellpadding="5" rules="all">'
		 . '<tr><th colspan="2">' . wfMsg('namespace_name_issues') . '</th></tr>'
		 . '<tr><th>' . wfMsg('namespace_name_header') . '</th>'
		 . '<th>'.wfMsg('namespace_issue_header').'</th></tr>'
		 . "\n";

		foreach($result[NS_ILLEGAL_NAMES] as $illegalName) {
			$htmltable.=
			'<tr><td>'
			.$illegalName.
			'</td><td>'
			.wfMsg('namespace_name_illegal_characters').
			'</td></tr>';
		}
		foreach($result[NS_DUPLICATE_NAMES] as $duplicateName) {
			$htmltable.=
			'<tr><td>'
			.$duplicateName.
			'</td><td>'
			.wfMsg('namespace_name_dupe').
			'</td></tr>';
		}
		foreach($result[NS_INTERWIKI_NAMES] as $interwikiName) {
			$htmltable.=
			'<tr><td>'
			.$interwikiName.
			'</td><td>'
			.wfMsg('namespace_name_interwiki').
			'</td></tr>';
		}
		foreach($result[NS_PREFIX_NAMES] as $prefixName) {
			$htmltable.=
			'<tr><td>'
			.$prefixName.
			'</td><td>'
			.wfMsg('namespace_name_prefix').
			'</td></tr>';
		}

		# Close table
		$htmltable .= '</table>'."\n";
		return $htmltable;
	}

	/**
	 * List of titles which exist in a real namespace
	 * which duplicate page titles in a pseudonamespace.
	 *
	 * @return a wiki-formatted list of links
	*/
	function pseudoDupes( $prefix, $dupelist ) {
		$wikilist=wfMsg('pseudonamespace_title_dupes')."\n";
		foreach($dupelist as $dupe) {	
			$wikilist.="# [[{$prefix}:{$dupe}|{$dupe}]]\n";
		}
		return $wikilist;
	}

	/**
	 * Delete the namespace, using form data.
	 * Checks for many error conditions:
	 * - Namespace must exist
	 * - System namespaces cannot be deleted
	 * - Namespaces which are non-empty cannot be deleted
	 */
	function deleteNamespace() {

		global $wgOut,$wgRequest,$wgNamespaces;

		$nsid = $wgRequest->getInt('ns');

		/* There should be no delete links for namespaces which cannot
		   be deleted, but let's catch two possible problems just in case. */
		if(!array_key_exists( $nsid, $wgNamespaces) ) {
			$this->showForm( wfMsg('namespace_not_deletable') , wfMsg('namespace_has_gone_missing', $nsid) );
			return false;
		} elseif( $wgNamespaces[$nsid]->isSystemNamespace() ) {
			$this->showForm( wfMsg('namespace_not_deletable') , wfMsg('namespace_not_deletable_system', $nsid) );
			return false;
		}

		$nsdelete = wfClone( $wgNamespaces[$nsid] );
		$nsdeletename = $nsdelete->getDefaultName();
		$drv = $nsdelete->deleteNamespace();

		if( empty($nsdeletename) ) {
			# At least show the index
			$nsdeletename = $nsid;
		}

		if( $drv[NS_RESULT] == NS_DELETED ) {
			$wgOut->addWikiText( wfMsg('namespace_deleted',$nsdeletename) );
			$this->showForm();
			$this->logNs('delete',$nsdeletename);
			return true;
		} elseif( $drv[NS_RESULT] == NS_NAME_ISSUES ) {
			$this->showForm( wfMsg('namespace_delete_error',$nsdeletename),$this->nameIssues($drv) );
			return false;
		} elseif ($drv[NS_RESULT] == NS_HAS_PAGES) {
			/** TODO: link to Special:Allpages/namespace */
			$this->showForm(wfMsg('namespace_delete_error',$nsdeletename), wfMsg('namespace_delete_not_empty'));
			return false;
		} else {
			$this->showForm( wfMsg('namespace_delete_error') );
			return false;
		}
	}

	/**
	 *  Call Namespace::convertPseudonamespace with the correct
	 *  parameters and display the results.
	 */
	function fixPseudonamespaces() {

		global $wgOut, $wgRequest, $wgUser, $wgNamespaces;
		# Merging into non-empty namespaces is generally prohibited
		$merge=$wgRequest->getBool('nsMerge');
		if($merge && !in_array( 'merge_pseudonamespaces', $wgUser->getRights())) {
			$this->showForm( wfMsg('badaccess'), wfMsg('badaccesstext','[['.wfMsg('administrators').']]','fix_pseudonamespaces'));
			return false;
		}
		$prefix=$wgRequest->getText('nsPrefix');
		$targetid=$wgRequest->getIntOrNull('nsConvertTo');
		$converttalk = $wgRequest->getBool('nsConvertTalk');
		if(empty($prefix) || is_null($targetid)) {
			$this->showForm (wfMsg('pseudonamespace_info_missing'));
			return false;

		}
		$talktargetid=$wgNamespaces[$targetid]->getTalk();
		if($converttalk && is_null($talktargetid)) {
			$this->showForm (wfMsg('pseudonamespace_target_talk_not_found'));
			return false;
		}

		$rv=Namespace::convertPseudonamespace($prefix,$wgNamespaces[$targetid],$wgNamespaces[NS_MAIN],$merge);
		if($rv[NS_RESULT]!=NS_PSEUDO_CONVERTED) {
			if(!($rv[NS_RESULT]==NS_PSEUDO_NOT_FOUND && $converttalk)) {
				$this->showPseudoError($rv,$targetid,$prefix);
				return false;
			} else {
				# Even if the prefix doesn't exist, we still
				# want to check for possible talk page content.
				$wgOut->addWikiText(wfMsg('pseudonamespace_not_found',$prefix));
				$wgOut->addWikiText(wfMsg('pseudonamespace_trying_talk'));
			}
		} else {
			$wgOut->addWikiText(wfMsg('pseudonamespace_converted', $prefix, $wgNamespaces[$targetid]->getDefaultName()));
			$this->logNs('pseudo',$prefix,$wgNamespaces[$targetid]->getDefaultName());
		}
		if($converttalk) {
			# A pseudonamespace, by definition, exists in the 
			# main (unprefixed) namespace - therefore its talk
			# pages are NS_TALK.
			$trv=Namespace::convertPseudonamespace($prefix,$wgNamespaces[$talktargetid],$wgNamespaces[NS_TALK],$merge);
			if($trv[NS_RESULT]!=NS_PSEUDO_CONVERTED) {
				$this->showPseudoError($trv,$talktargetid,$prefix);
				return false;
			} else {
				$wgOut->addWikiText(wfMsg('pseudonamespace_talk_converted'));
			}
		}
		$this->showForm();
	}
	function showPseudoError($rv,$targetid,$prefix) {
		global $wgNamespaces;
		$istalk=$wgNamespaces[$targetid]->isTalk();
		# For messages
		$talk=$istalk ? 'talk_' : '';
		if($rv[NS_RESULT]==NS_PSEUDO_NOT_FOUND) {
			$this->showForm( wfMsg("pseudonamespace_{$talk}not_found",$prefix));
		} elseif($rv[NS_RESULT]==NS_NON_EMPTY) {
			$this->showForm(
			wfMsg("pseudonamespace_{$talk}conversion_error",$prefix),
			wfMsg("pseudonamespace_cannot_merge"));
		} elseif($rv[NS_RESULT]==NS_DUPLICATE_TITLES) {
			$this->showForm(
			wfMsg("pseudonamespace_{$talk}conversion_error",$prefix),
				$this->pseudoDupes($wgNamespaces[$targetid]->getDefaultName(),$rv[NS_DUPLICATE_TITLE_LIST]));
		}
	}

	function getSelector($name_array,$parentslot=null) {
		$noparent = wfMsg('no_parent_namespace');		
		$namespaceselect='';
		foreach ( $name_array as $arr_index => $arr_name ) {
			if( $arr_index < NS_MAIN && $arr_name!=$noparent )
				continue;
			$list_option = ($arr_index == NS_MAIN ? wfMsg ( 'blanknamespace' ) : $arr_name);
			if(is_null($parentslot)) {
				$arr_name == $noparent ? $selected = ' selected="selected" ' : $selected='';
			} else {
				$arr_index == $parentslot ? $selected = ' selected="selected"' : $selected='';
			}
			$namespaceselect .= "\n<option value='$arr_index'$selected>$list_option</option>";
		}
		return $namespaceselect;

	}
	
	function annotation( $text ) {
		return wfElement( 'div', array( 'class' => 'mwNsAnnotation' ), $text );
	}
	
	function checkRow( $labelMessage, $name, $checked ) {
		return '<tr><td colspan="2">' .
			wfCheckLabel( wfMsg( $labelMessage ), $name, $name, $checked ) .
			"</td></tr>\n";
	}
	
	function selectorRow( $labelMessage, $name, $selected ) {
		return '<tr><td colspan="2">' .
			"<div>" . wfMsgHtml( $labelMessage ) . "</div>\n" .
			"<div>" .
				wfOpenElement( 'select', array( 'name' => $name ) ) .
				$this->getSelector( Namespace::getFormattedDefaultNamespaces(true), $selected ) .
				wfCloseElement( 'select' ) .
			"</div>" .
			"</td></tr>\n";
	}
}
?>
