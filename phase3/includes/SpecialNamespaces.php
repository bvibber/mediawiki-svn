<?php
/**
*
* @package MediaWiki
* @subpackage SpecialPage
*/

/**
* Constructor
*/
function wfSpecialNamespaces()
{
global $wgUser, $wgOut, $wgRequest;

$action = $wgRequest->getVal( 'action' );
$f = new NamespaceForm();

if ( $action == 'submit' && $wgRequest->wasPosted() &&
	$wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
	if($wgRequest->getText('nsAction')=='addnamespaces') {
		$f->addNamespaces();
	} elseif($wgRequest->getText('nsAction')=='changenamespaces') {
		$f->changeNamespaces();
	}
} elseif($action == 'delete') {

	$f->deleteNamespace();
}
else {
	$f->showForm();
}
}

/**
*
* @package MediaWiki
* @subpackage SpecialPage
*/
class NamespaceForm {

function showForm( $errorHeader='', $errorBody='' )
{
	global $wgOut, $wgUser, $wgLang, $wgNamespaces,$wgTitle;
	
	$wgOut->setPagetitle( wfMsg( "namespaces" ) );

	/* In case of an error, we generally just show what went wrong
	   and continue displaying the main form */
	if ( '' != $errorHeader ) {
		$wgOut->setSubtitle( wfMsg( "transactionerror" ) );
		$wgOut->addHTML( "<p class='error'>".htmlspecialchars($errorHeader)."</P>");
		if($errorBody) {
			$wgOut->addWikiText($errorBody);
		}
	}
	
	# Standard token to avoid remote form submission exploits
	$token = $wgUser->editToken();
			$action = $wgTitle->escapeLocalURL( "action=submit" );
	$talksuffix = wfEscapeJsString(wfMsgForContent("talkpagesuffix"));
	
	# For the namespace selection box
	$name_array= Namespace::getFormattedDefaultNamespaces();
	$noparent=wfMsg('no_parent_namespace');
	$name_array[key($name_array)-1]=$noparent;
	# Sort for foreach loops
	ksort($name_array);

	$wgOut->addWikiText( wfMsg( "add_namespaces_header" ) );
	# Prefill talk namespace field, but only for languages 
	# where it's not disabled
	if($talksuffix != '-') {
		$talkpagejs=
' onchange="if(!window.document.addnamespaces.nsTalkName.value && window.document.addnamespaces.nsName.value && window.document.addnamespaces.nsCreateTalk.checked) { window.document.addnamespaces.nsTalkName.value=window.document.addnamespaces.nsName.value+\''.$talksuffix.'\'; }"';

	} else {
		$talkpagejs='';
	}

	$addnshtmlform='
<form name="addnamespaces" method="post" action="'.$action.'">
<table border="0">
<tr valign="top"><td>
'.wfMsg('add_namespace_default_name').'</td>
<td>
<input type="hidden" name="nsAction" value="addnamespaces" />
<input type="text" name="nsName" size="20"'.$talkpagejs.' />
</td>
</tr>
<tr valign="top">
<td>
'.wfMsg('add_namespace_default_talk').'<br />
</td>
<td><input type="text" name="nsTalkName" size="20" />
</td>
</tr>
<tr>
<td colspan="2">
<label><input type="checkbox" name="nsCreateTalk" checked />
'.wfMsg('add_namespace_talk_confirm').'
</label>
</td>
</tr>
</table>
<input type="hidden" name="wpEditToken" value="'.$token.'" />
<input type="submit" value="'.wfMsg('add_namespace_button').'" />
</form>';
		$wgOut->addHTML($addnshtmlform);
			
		$wgOut->addWikiText( wfMsg( "modify_namespaces_header" ) );

		$namespace_child_of=wfMsg('namespace_child_of');
		$namespace_support_subpages=wfMsg('namespace_support_subpages');
		$namespace_search_by_default=wfMsg('namespace_search_by_default');
		$namespace_hide_in_lists=wfMsg('namespace_hide_in_lists');
		$namespace_default_link_prefix=wfMsg('namespace_default_link_prefix');
		$namespace_system=wfMsg('namespace_system');		
		$namespace_properties=wfMsg('namespace_properties');
		$namespace_slot=wfMsg('namespace_slot');
		$namespace_names=wfMsg('namespace_names');
		$namespace_existing_names=wfMsg('namespace_existing_names');
		$namespace_new_names=wfMsg('namespace_new_names');
		$namespace_default_name=wfMsg('namespace_default_name');
		$namespace_delete_name=wfMsg('namespace_delete_name');
		$namespace_save_changes=wfMsg('namespace_save_changes');
				
		$htmlform=<<<END
<form name="changenamespaces" method="post" action="{$action}">
<input type="hidden" name="nsAction" value="changenamespaces" />
<input type="hidden" name="wpEditToken" value="{$token}" />
END;
		foreach ($wgNamespaces as $ns) {
	
					
			$index=$ns->getIndex();
			$subpages=$ns->allowsSubpages() ? ' checked' : '';
			$searchdefault=$ns->isSearchedByDefault() ? ' checked' :'';
			$hidden=$ns->isHidden ? ' checked' : '';
			$linkprefix=$ns->getTarget();
			$namespaceselect='';
			$parentslot=$ns->getParentIndex();
			
			# maybe make HTMLnamespaceselector more flexible and use
			# it instead here
			if(!$ns->isSpecial()) {

				foreach ( $name_array as $arr_index => $arr_name ) {
					if( $arr_index < NS_MAIN && $arr_name!=$noparent)
						continue;
					$list_option = ($arr_index == NS_MAIN ? wfMsg ( 'blanknamespace' ) : $arr_name);
					if(is_null($parentslot)) {
						$arr_name==$noparent ? $selected = ' selected ' : $selected='';
					} else {
						$arr_index == $parentslot ? $selected = ' selected' : $selected='';
					}
					$namespaceselect .= "\n<option value='$arr_index'$selected>$list_option</option>";
				}
				$namespaceselect_html=<<<END
<tr valign="top"><td colspan="2">
{$namespace_child_of}<br />
<select name="ns{$index}Parent" size="1">
{$namespaceselect}
</select>
</td>
</tr>
END;
				$subpages_html=<<<END
<tr valign="top"><td>
{$namespace_support_subpages}
</td>
<td align="right">
<input type="checkbox" name="ns{$index}Subpages" {$subpages} />
</td>
</tr>		
END;
				$searchdefault_html=<<<END
<tr valign="top"><td>
{$namespace_search_by_default}
</td>
<td  align="right">
<input type="checkbox" name="ns{$index}Search" {$searchdefault} />
</td>
</tr>			
END;
				$hide_html=<<<END
<tr valign="top"><td>
{$namespace_hide_in_lists}
</td>
<td  align="right">
<input type="checkbox" name="ns{$index}Hidden" {$hidden} />
</td>
</tr>				
END;
				$target_html=<<<END
<tr valign="top"><td>
{$namespace_default_link_prefix}
</td>
<td align="right">
<input type="text" size="10" name="ns{$index}Linkprefix" value="{$linkprefix}" />
</td>
</tr>				
END;
				$special_html='';

			} else {

				$namespaceselect_html='';
				$subpages_html='';
				$searchdefault_html='';
				$hide_html='';
				$target_html='';
				$special_namespace=wfMsg('special_namespace');
				$special_html=<<<END
<tr valign="top"><td colspan="2">
<em>{$special_namespace}</em>
</td>
</tr>				
END;

			}

			$systemtype=$ns->getSystemType();
			if($ns->getSystemType()) {
				$systemtype_html=<<<END
<tr valign="top"><td>
<B><font color="red">{$namespace_system}</font></B>
</td>
<td align="right">
<B>{$systemtype}</B>
</td>
</tr>
END;
				$deletenamespace_html='';
			} else {
				$sk=$wgUser->getSkin();
				$delete_link=$sk->makeKnownLinkObj($wgTitle,wfMsg('delete_namespace'),'action=delete&ns='.$index);
				$deletenamespace_html=<<<END
<tr valign="top"><td colspan="2">
<strong>{$delete_link}</strong>
</td>
</tr>
END;
				$systemtype_html='';
			}
			

			$htmlform .= <<<END
<table border="0">
<tr valign="top">
<td>
<table border="0" style="margin-right:1em;" width="300">
<tr><th colspan="2">
{$namespace_properties}
</th>
</tr>
<tr><td>
{$namespace_slot}
</td>
<td align="right">{$index}
</td>
</tr>
{$systemtype_html}
{$special_html}
{$subpages_html}
{$searchdefault_html}
{$hide_html}
{$target_html}
{$namespaceselect_html}
{$deletenamespace_html}
</table>
</td>
<td>
<table border="0">
<tr>
<th colspan="3">
{$namespace_names}
</th>
</tr>
<tr>
<th align="left">
{$namespace_existing_names}
</th>
<th>
{$namespace_default_name}
</th>
<th>
{$namespace_delete_name}
</th>
</tr>
END;

		foreach ($ns->names as $nsi=>$nsname) {
			if (!is_null($ns->getDefaultNameIndex()) && $ns->getDefaultNameIndex() == $nsi) {
				$dc=" checked";
			} else {
				$dc="";
			}
			$default = "<input type=\"radio\" name=\"ns{$index}Default\" value=\"{$nsi}\"{$dc} />";
			if (!is_null($ns->getCanonicalNameIndex()) &&$ns->getCanonicalNameIndex()== $nsi) {
			 	$nameinput = $nsname . '<br/><small>'.wfMsg('canonicalname').'</small>';
				$delete = 'N/A';
			 } else {
				$nameinput = "<input name=\"ns{$index}Name{$nsi}\" size=\"20\" value=\"{$nsname}\" />";
				$delete = "<input name=\"ns{$index}Delete{$nsi}\" type=\"checkbox\" value=\"1\" />";
			}
			$htmlform.=
<<<END
<tr valign="top">
<td width="300">
{$nameinput}
</td>
<td align="center">
{$default}
</td>
<td align="center">
{$delete}
</td>
</tr>
END;

		}
		$htmlform.="<tr><th align=\"left\">{$namespace_new_names}</th></tr>";
		# 3 blank namespace fields
		if(!is_null($ns->names)) {
			end($ns->names);
			$highestName=key($ns->names)+1;
		} else {
			$highestName=0;
		}
		for($i=$highestName;$i<$highestName+3;$i++) {
			$htmlform.=  
<<<END
<tr valign="top">
<td width="300">
<input name="ns{$index}NewName{$i}" size="20" value="" />
</td>
<td align="center">
<input type="radio" name="ns{$index}Default" value="{$i}" />
</td>
<td align="center">
&nbsp;
</td>
</tr>
END;
		}
		$htmlform .= '</table></td></tr>';	
		$htmlform .= '<tr><td colspan="2"><hr noshade /></td></tr>';
	}
	$htmlform.=
<<<END
<tr><td>
<input type="submit" value="{$namespace_save_changes}" />
</td></tr>
</table>
</form>
END;

	$wgOut->addHTML($htmlform);
	}

	function addNamespaces() {
		global $wgOut, $wgUser, $wgLang, $wgRequest;	
		$nsname=$wgRequest->getText('nsName');
		$nstalkname=$wgRequest->getText('nsTalkName');
		$nscreatetalk=$wgRequest->getBool('nsCreateTalk');

		if(empty($nsname)) {
			$this->showForm(wfMsg('namespace_name_missing'));
		}
		$dbr=&wfGetDB(DB_SLAVE);
		$ns=new Namespace();
		$newnameindex=$ns->addName($nsname);
		if(is_null($newnameindex)) {
			$this->showForm(wfMsg('namespace_error',$nsname),
			wfMsg('namespace_name_illegal_characters', NS_CHAR));
			return false;
		}
		$ns->setDefaultNameIndex($newnameindex);
		$nrv=$ns->testSave();
		/* 
			The only errors which can occur here should be
			name-related.
		*/
		if($nrv[NS_RESULT]==NS_NAME_ISSUES) {
			$this->showForm(wfMsg("namespace_error",$nsname),$this->nameIssues($nrv));
			return false;
		}
		$newnamespaceindex=$nrv[NS_SAVE_ID];
		if($nscreatetalk && !empty($nstalkname)) {
			$talkns=new Namespace();
			$talkns->setParentIndex($newnamespaceindex);
			$talkns->setSubpages();
			$newtalknameindex=$talkns->addName($nstalkname);
			$talkns->setDefaultNameIndex($newtalknameindex);
			$trv=$talkns->testSave();
			if($trv[NS_RESULT]!=NS_CREATED) {
				$this->showForm(wfMsg("talk_namespace_error",$nstalkname),$this->nameIssues($trv));
				return false;
			}
		}

		# Save for real.
		$ns->save();
		$complete=wfMsg('namespace_created',$nsname);
		if($nscreatetalk) {
			$talkns->save();
			$complete.=' '.wfMsg('talk_namespace_created');
		}
		$wgOut->addWikiText($complete);
		$this->showForm();
	}

	function changeNamespaces() {
	
	global $wgOut, $wgNamespaces, $wgRequest;
		$newns=array();
		foreach($wgNamespaces as $ns) {
			$nsindex=$ns->getIndex();
			$newns[$nsindex]=new Namespace();
			$newns[$nsindex]->setIndex($nsindex);
			$newns[$nsindex]->setSystemType($ns->getSystemType());

			if(!$ns->isSpecial()) {
				$subvar="ns{$nsindex}Subpages";
				$searchvar="ns{$nsindex}Search";
				$hiddenvar="ns{$nsindex}Hidden";
				$prefixvar="ns{$nsindex}Linkprefix";
				$parentvar="ns{$nsindex}Parent";
				$subpages=$wgRequest->getBool($subvar);
				$searchdefault=$wgRequest->getBool($searchvar);
				$hidden=$wgRequest->getBool($hiddenvar);
				$prefix=$wgRequest->getText($prefixvar);
				$parent=$wgRequest->getIntOrNull($parentvar);
				$newns[$nsindex]->setSubpages($subpages);
				$newns[$nsindex]->setSearchedByDefault($searchdefault);
				$newns[$nsindex]->setHidden($hidden);
				$newns[$nsindex]->setTarget($prefix);
				if(array_key_exists($parent,$wgNamespaces)) {
					$newns[$nsindex]->setParentIndex($parent);
				}				
			}
			$newns[$nsindex]->names=$ns->names;

			# This can never be changed by the user.
			$newns[$nsindex]->setCanonicalNameIndex($ns->getCanonicalNameIndex());

			# New names, appended to end
			for($i=1;$i<=3;$i++) {
				$nvar="ns{$nsindex}NewName{$i}";
				if($nname=$wgRequest->getText($nvar)) {
					$newns[$nsindex]->addName($nname);
				}
			}

			# Changes and deletions. Do them last since they can
			# affect index slots of existing names.
			foreach($ns->names as $nameindex=>$name) {
				$var="ns{$nsindex}Name{$nameindex}";
				if($req=$wgRequest->getText($var)) {
					#wfDebug("Name var $var contains $req\n");

					# Alter name if necessary.
					if($req!=$name) {
						
						# The last parameter means
						# that we do not check if the
						# name is valid - this
						# is done later for all names.
						$newns[$nsindex]->setName(
							$name,$req,false
						);

						#wfDebug("Setting name $nameindex of namespace $nsindex to $req. Old name is $name.\n");
					}
				}
				$delvar="ns{$nsindex}Delete{$nameindex}";
				if($wgRequest->getInt($delvar)) {
					#wfDebug("$delvar should be deleted.\n");
					$newns[$nsindex]->removeNameByIndex($nameindex);
				}
			}

			$dvar="ns{$nsindex}Default";
			# Did the user select a default name?
			$dindex=$wgRequest->getIntOrNull($dvar);
			# If not, get the old one.
			if(is_null($dindex)) { $dindex=$ns->getDefaultNameIndex();}
			# Does the name exist and is it non-empty?
			if(!is_null($dindex) && array_key_exists($dindex, $newns[$nsindex]->names) && !empty($newns[$nsindex]->names[$dindex]) ) {
				# Use this default name.
				$newns[$nsindex]->setDefaultNameIndex($dindex);
				#wfDebug("Setting index for $nsindex to $dindex!\n");
			} else {
				# We have lost our default name, perhaps
				# it was deleted. Get a new one if
				# possible.	
				$newns[$nsindex]->setDefaultNameIndex($newns[$nsindex]->getNewDefaultNameIndex());
				
			}
		}
		foreach($newns as $nns) {
			$nrv=$nns->testSave();
			if($nrv[NS_RESULT]==NS_NAME_ISSUES) {
				$this->showForm(wfMsg("namespace_error",$nns->getDefaultName()),$this->nameIssues($nrv));
				return false;
			}
			$nns->save();
		}
	
		# IMPORTANT: The namespace name indexes are unpredictable when
		# serialized, so we have to reload the definitions from the
		# database at this point; otherwise, there could be index
		# mismatches.
		Namespace::load();

		# Return to the namespace manager with the changes made.
		$wgOut->addWikiText(wfMsg("namespace_changes_saved"));
		$this->showForm();
		return true;
	}
	
	function nameIssues($result) {
	
		$htmltable='
		<table border="0" width="100%" cellspacing="5" cellpadding="5" rules="all">
		<tr>
		<th colspan="2">'.wfMsg('namespace_name_issues').'</th>
		</tr><tr>
		<th>'.wfMsg('namespace_name_header').'</th>
		<th>'.wfMsg('namespace_issue_header').'</th>
		</tr>';
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
		foreach($result[NS_LINKED_NAMES] as $linkedName) {
			$htmltable.=
			'<tr><td>'
			.$linkedName.
			'</td><td>'
			.wfMsg('namespace_name_linked').
			'</td></tr>';
		}
		
		$htmltable.='</table>';
		return $htmltable;
	}
	
	function deleteNamespace() {
		global $wgOut,$wgRequest,$wgNamespaces;
		$nsid=$wgRequest->getInt('ns');
		/* There should be no delete links for namespaces which cannot
		   be deleted, but let's catch two possible problems just in case. */
		if(!array_key_exists($nsid,$wgNamespaces)) {
			$this->showForm(wfMsg('namespace_not_deletable'),wfMsg('namespace_not_deletable_missing',$nsid));
			return false;
		} elseif($wgNamespaces[$nsid]->isSystemNamespace()) {
			$this->showForm(wfMsg('namespace_not_deletable'),wfMsg('namespace_not_deletable_system',$nsid));
			return false;
		}
		$nsdelete=clone($wgNamespaces[$nsid]);
		$nsdeletename=$nsdelete->getDefaultName();		
		$drv=$nsdelete->deleteNamespace();
		if(empty($nsdeletename)) {
			# At least show the index
			$nsdeletename=$nsid;
		}
		if($drv[NS_RESULT]==NS_DELETED) {
			$wgOut->addWikiText(wfMsg("namespace_deleted",$nsdeletename));
			$this->showForm();
			return true;
		} elseif($drv[NS_RESULT]==NS_NAME_ISSUES) {
			$this->showForm(wfMsg('namespace_delete_error',$nsdeletename),$this->nameIssues($drv));
			return false;
		} else {
			$this->showForm(wfMsg('namespace_delete_error'));
			return false;
		}
	}

}

?>
