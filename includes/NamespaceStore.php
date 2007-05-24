<?php
/**
 * Class for storing and retrieving namespace objects
 *
*/
class NamespaceStore {
	function NamespaceStore() {
		global $wgNamespaces;
		$this->nsarray = $wgNamespaces;
	}
	
	function getTalk($parentns) {
		if($parentns->isTalk()) return $parentns->getIndex();

		foreach($this->nsarray as $ns) {
			if($ns->hasParent() && $ns->parentIndex==$parentns->index) {
				return $ns->index;
			}
		}

		return null;
	}

	/**
	 * Serialize this namespace to the database.
	 * No part of the operation will be completed
	 * unless it cannot be fully done.
	 *
	 * If the namespace index is NULL, a new namespace 
	 * will be created.
	 *
	 * @param boolean $testSave
	 *   If this is set to true, no actual changes
	 *   will be made. This is useful for testing
	 *   transactions on a number of namespaces,
	 *   and not completing any of them unless 
	 *   all of them will succeed.
	 * @param boolean $overrideInterwiki
	 *   If a namespace name overlaps with an Interwiki
	 *   prefix, should it be created anyway?
	 *  
	 * @return array()
	 *   An array that describes the results of the
	 *   operation, as follows:
	 *
	 *       array(
	 *        NS_RESULT=>
	 *          NS_MODIFIED | NS_CREATED | NS_NAME_ISSUES
	 *          NS_MISSING | NS_IDENTICAL
	 *        NS_SAVE_ID=>namespace ID or NULL
	 *        NS_ILLEGAL_NAMES=>array(names)
	 *        NS_DUPLICATE_NAMES=>array(names)
	 *        NS_INTERWIKI_NAMES=>array(names)
	 *        NS_PREFIX_NAMES=>array(names)
	 *       )
	 *
	 *  NS_RESULT can be:
	 * 
	 *  NS_MODIFIED    - existing namespace successfully changed
	 *  NS_CREATED     - new namespace successfully created
	 *  NS_IDENTICAL   - the version in the database is
	 *                   identical with the one to be saved
	 *  NS_NAME_ISSUES - operation failed due to issues with
	 *                   name changes
	 *  NS_MISSING     - namespace with $nsobj->index not found 
	 *                   in DB, cannot be altered.
	 *
	 *  In order to show useful result information, we record
	 *  exactly which names have been added, removed or changed
	 *  (NS_NAMES_ADDED, NS_NAMES_MODIFIED, NS_NAMES_DELETED).
	 *  If the save fails, we record which name change(s) caused
	 *  the problem:
	 *
	 *      NS_ILLEGAL_NAMES
	 *      names which contain illegal characters
	 *
	 *      NS_DUPLICATE_NAMES
	 *      names which already exist
	 *
	 *      NS_INTERWIKI_NAMES 
	 *      names which are also used as Interwiki prefixes
	 *      (cf. $overrideInterwiki parameter)
	 *
	 *      NS_PREFIX_NAMES
	 *      names which are used as hardcoded title prefixes
	 *
	 *  How this function works:
	 *  ------------------------
	 *  Check if the namespace has a valid ID (not null)
	 *
	 *     NO: We will try to create it.
	 *
	 *      1.1) Obtain an index from $this->nsarray
	 *      1.2) Make a list of the names that are going
	 *           to be added.
	 *      1.3) Proceed to 2.2)
	 * 
	 *     YES: We will try to modify it.
	 *
	 *      2.1) Compare this object with the corresponding
	 *           one in $this->nsarray and make a list of
	 *           the names that are going to be removed
	 *           (set to NULL), changed, or added.
	 *      2.2) Verify whether all namespace name operations
	 *           are possible. If not, return the appropriate
	 *           error codes.
	 *      2.3) If all operations are possible, update or
	 *           update the namespace and return the appropriate
	 *           result array.
	 */
	function saveNamespace($nsobj,
						   $overrideInterwiki=false,
						   $testSave=false) {

		$fname='NamespaceStore::saveNamespace';
		$rv=array(
		 NS_RESULT=>null,
		 NS_ILLEGAL_NAMES=>array(),
		 NS_DUPLICATE_NAMES=>array(),
		 NS_INTERWIKI_NAMES=>array(),
		 NS_PREFIX_NAMES=>array()
		);
		$nameOperations=array();
		$dbs =& wfGetDB( DB_SLAVE );
		$index=$nsobj->getIndex();
		if(is_null($index)) {
			$create = true;
			end($this->nsarray);
			$index=$this->nsarray[key($this->nsarray)]->getIndex()+1;
			$nsobj->setIndex($index);
			foreach($nsobj->names as $name) {
				$nameOperations[$name]=NS_NAME_ADD;
			}
		} else {
			$create = false;
			# Does this namespace exist?			
			if(!array_key_exists($index,$this->nsarray)) {
				$rv[NS_RESULT]=NS_MISSING;
				return $rv;
			}			
			# Has anything actually been changed?
			if($nsobj===$this->nsarray[$nsobj->getIndex()]) {
				$rv[NS_RESULT]=NS_IDENTICAL;
				return $rv;			
			}
			$oldcount=count($this->nsarray[$index]->names);
			$newcount=count($nsobj->names);
			for($i=0;$i<$oldcount || $i<$newcount;$i++) {
				$existsOld=array_key_exists($i, $this->nsarray[$index]->names);
				$existsNew=array_key_exists($i, $nsobj->names);
				if($existsOld && $existsNew) {
					if(strcasecmp($this->nsarray[$index]->names[$i], $nsobj->names[$i])!=0) {
						$nameOperations[$nsobj->names[$i]]=NS_NAME_MODIFY;
					}
				} elseif($existsOld && !$existsNew) {
					$nameOperations[$this->nsarray[$index]->names[$i]]=NS_NAME_DELETE;
				} elseif(!$existsOld && $existsNew) {
					$nameOperations[$nsobj->names[$i]]=NS_NAME_ADD;
				}
			}
			
		}

		# Are there any name operations to do? If so, check 
		# whether they are possible before doing anything else.
		foreach($nameOperations as $name=>$operation) {
			if($operation==NS_NAME_ADD || $operation==NS_NAME_MODIFY) {
				
				# Illegal characters?
				# This should never happen if the setters
				# are used.
				if(!$nsobj->isValidName($name)) {
					$rv[NS_RESULT]=NS_NAME_ISSUES;
					$rv[NS_ILLEGAL_NAMES][]=$name;
				}

				# Duplicate names
				foreach($this->nsarray as $exns) {
					$dupes=array_keys($exns->names,$name);
					if(count($dupes)) {
						$rv[NS_RESULT] = NS_NAME_ISSUES;
						$rv[NS_DUPLICATE_NAMES][]=$name;
					}
				}

				# Interwiki
				if(Title::getInterwikiLink( $name)) {
					$rv[NS_RESULT]=NS_NAME_ISSUES;
					$rv[NS_INTERWIKI_NAMES][]=$name;
				}

				# Pseudo-namespaces (title prefixes)
				$likename = str_replace( '_', '\\_', $name);
				$likename = str_replace( '%', '\\%', $likename);
				$match = $dbs->addQuotes($likename.":%");
				$res = $dbs->select(
					'page',
					array('page_title'),
					array('page_namespace'=>0,
					      'page_title LIKE '.$match,
					      ),
					$fname,
					array('LIMIT'=>1)
				);
				if($dbs->numRows($res) > 0) {
					$rv[NS_RESULT]=NS_NAME_ISSUES;
					$rv[NS_PREFIX_NAMES][]=$name;
				}
				$dbs->freeResult($res);
			} 
		}

		# If there are problems, return the array
		if($rv[NS_RESULT]==NS_NAME_ISSUES) {
			return $rv;
		}

		$dbm =& wfGetDB(DB_MASTER);		
		$nsasdb=array(
			'ns_id'=>$index,
			'ns_system'=>$nsobj->getSystemType(),
			'ns_subpages'=>$nsobj->allowsSubpages(),
			'ns_search_default'=>$nsobj->isSearchedByDefault(),
			'ns_target'=>$nsobj->getTarget(),
			'ns_parent'=>$nsobj->getParentIndex(),
			'ns_hidden'=>$nsobj->isHidden()
			);		
		if($create) {
			# testSave checks should always be placed
			# right before a transaction that alters
			# the database or memory state.
			if(!$testSave) {
				$dbm->insert(
					'namespace',
					$nsasdb,
					$fname,
					array()
					);
			}
		} 
		foreach($nameOperations as $name=>$operation) {
			if($operation==NS_NAME_ADD) {
				$isDefault = ($name==$nsobj->getDefaultName());
				$isCanonical = ($name==$nsobj->getCanonicalName());
				if(!$testSave) {
					$dbm->insert(
						'namespace_names',
						array(
						'ns_id'=>$nsobj->getIndex(),
						'ns_name'=>$name,
						'ns_default'=>$isDefault,
						'ns_canonical'=>$isCanonical
						),
						$fname,
						array()
					);
				}
			} elseif($operation==NS_NAME_MODIFY) {				$oldname = $this->nsarray[$index]->names[$nsobj->getNameIndexForName($name)];
				if(!$testSave) {
					$dbm->update(
						'namespace_names',
						array( /* SET */
						'ns_name'=>$name,
						),
						array(
						'ns_name'=>$oldname),
					        $fname);
				}
			} elseif($operation==NS_NAME_DELETE) {
				$dbm->delete(
					'namespace_names',
					array('ns_name'=>$name),
					'*');
			}
		}
		if($create) {
			$rv[NS_RESULT]=NS_CREATED; 
			
			# If this was just a test for a new
			# namespace, reset the index to NULL so
			# it will be created for real
			# if save() is called on the same object.
			if($testSave) {
				$nsobj->setIndex(NULL);
			}
		} else {
			# Set canonical and default names.
			# This needs to happen after other name operations
			# because we can't operate on the new names until
			# they exist. :-)
			$oldDefaultName=$this->nsarray[$index]->getDefaultName();
			$newDefaultName=$nsobj->getDefaultName();

			# Note that canonical names normally should NEVER change,
			# but we provide the functionality just in case it's needed
			# by some maintenance scripts.
			$oldCanonical=$this->nsarray[$index]->getCanonicalName();
			$newCanonical=$nsobj->getCanonicalName();
			if(!$testSave) {
				$dbm->update(
					'namespace',
					$nsasdb, /* SET */
					array('ns_id'=>$index),
					$fname
				);
				if($oldDefaultName != $newDefaultName) {
					$dbm->update(
						'namespace_names',
						array('ns_default'=>0), /* SET */
						array('ns_name'=>$oldDefaultName),
						$fname
					);
					$dbm->update(
						'namespace_names',
						array('ns_default'=>1), /* SET */
						array('ns_name'=>$newDefaultName),
						$fname
					);					
				}
				if($oldCanonical != $newCanonical) {
					$dbm->update(
						'namespace_names',
						array('ns_canonical'=>0), /* SET */
						array('ns_name'=>$oldCanonical),
						$fname
					);
					$dbm->update(
						'namespace_names',
						array('ns_canonical'=>1), /* SET */
						array('ns_name'=>$newCanonical),
						$fname
					);					
				}
			}

			$rv[NS_RESULT]=NS_MODIFIED;
		}
		$rv[NS_SAVE_ID]=$index;

		# Note that it may be desirable to call Namespace::load()
		# in addition to this since the name (not namespace) indexes in
		# the database can be different from the one in the array.
		if(!$testSave) {
			$this->nsarray[$index]=$nsobj;
		}
		$this->refreshReverseIndex();
		return $rv;
	}
	/**
	 * Delete a namespace from the database and the namespace array.
	 * Only use this on clone()s of objects in the $this->nsarray array.
	 * This function allows deleting system namespaces if explicitly
	 * specified; this should however not be possible through the 
	 * user interface.
	 *
	 * @param $deleteSystem bool Override system namespace protection
	 */
	function deleteNamespace($nsobj, $deleteSystem=false) {
		if(is_null($nsobj->getIndex())) {
			return array(NS_RESULT=>NS_MISSING);
		}
		
		if($nsobj->isSystemNamespace() && !$deleteSystem) {
			return array(NS_RESULT=>NS_PROTECTED);
		}
		if($nsobj->countPages()>0) {
			return array(NS_RESULT=>NS_HAS_PAGES);
		}
		# Remove all names
		$nsobj->removeAllNames();
		# Try saving
		$trv=$nsobj->testSave();
		if($trv[NS_RESULT]!=NS_MODIFIED) {
			return $trv;
		}

		# As we just have to delete everything, we go
		# right into the database if the test succeeds.
		$dbm =& wfGetDB( DB_MASTER );
		$dbm->delete(
			'namespace',
			array('ns_id'=>$nsobj->getIndex()),
			'*');		
		$dbm->delete(
			'namespace_names',
			array('ns_id'=>$nsobj->getIndex()),
			'*');
		# Don't forget it's still in memory.
		unset($this->nsarray[$nsobj->getIndex()]);
		$this->refreshReverseIndex();
		return array(NS_RESULT=>NS_DELETED);
	}


	/**
	 * Maintain index used by getIndexByName
        */
	function refreshReverseIndex() {
		$this->reverseindex = array();
		foreach ($this->nsarray as $ns) {
			foreach($ns->names as $name) {
				$this->reverseindex[strtolower($name)]=$ns->getIndex();
			}
		}
	}

	/**
	 * For _any_ name (among all namespaces), return
	 * the index of the namespace to which it belongs.
	 *
	 * @param string Name to search for
	 * @return int Index of the namespace associated 
	 *  with this name (or NULL)
	 *
	 * call refreshReverseIndex() first if there's any doubt whether
	 * the reverse index is up-to-date (though for performance
	 * reasons, don't do it if it isn't necessary.
	 *
	 * @static
	 */
	function getIndexForName ( $name ) {
		$index = @$this->reverseindex[strtolower($name)];
		return isset($index) ? $index : NULL;
	}
	
	/**
	 * Return the default name for any namespace name
	 * given as a parameter, even if it is the default
	 * name already. Searches all namespaces.
	 *
	 * @param string Any namespace name
	 * @return string The name (may be identical)
	 * @static
	 */	
	function getDefaultNameForName ( $name ) {
                $index=$this->getIndexForName($name);
                if(!is_null($index)) {
                        return $this->nsarray[$index]->getDefaultName();
                } else {
                        return null;
                }
	}
	
	/**
	 * Return an array of the default names of all
	 * namespaces. Resets array pointer of $this->nsarray.
	 * @param $includeHidden Should hidden namespaces
	 *  be part of the array?
	 * @return array
	 * @static
	 */
	function getDefaultNamespaces($includeHidden=false) {
		$dns=array();
		foreach($this->nsarray as $ns) {
			if(!$ns->isHidden() || $includeHidden) {
				$dn=$ns->getDefaultName();
				if(!is_null($dn)) {
					$dns[$ns->getIndex()]=$dn;
				} else {
					$dns[$ns->getIndex()]='';
				}
			}
		}		
		return $dns;
	}

	function getAllNamespaceArray() {
		$allns=array();
		foreach($this->nsarray as $ns) {
			if(!$ns->isHidden() || $includeHidden) {
				$allns[$ns->getIndex()]=$ns->getNames();
			}
		}		
		return $allns;
	}

	/**
	 * A convenience function that returns the same thing as
	 * getDefaultNamespaces() except with the array values changed to ' '
	 * where it found '_', useful for producing output to be displayed
	 * e.g. in <select> forms.
	 *
	 * @static
	 * @param $includeHidden
	 * @return array
	 */
	function &getFormattedDefaultNamespaces($includeHidden=false) {
		$ns = $this->getDefaultNamespaces($includeHidden);
		foreach($ns as $k => $v) {
			$ns[$k] = strtr($v, '_', ' ');
		}
		return $ns;
	}
	
	/**
	* Load or reload namespace definitions from the database
	* into a global array.
	*
	* @param $purgeCache If definitions exist in memory, should
	*   they be reloaded anyway?
	*
	* @static
	*/
	function load($purgeCache=false) {

		global $wgMemc, $wgDBname;
		$key="$wgDBname:namespaces:list";
		if(!$purgeCache) {
			$fromMemory = $wgMemc->get($key);
			if(is_array($fromMemory)) {
				# Cached definitions found
				$this->nsarray=$fromMemory;
				# TODO: cache reverse index too
				$this->refreshReverseIndex();
				return true;
			}
		}
		$this->nsarray = array();
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( 
			array('namespace','namespace_names'),
			array('namespace.ns_id','ns_search_default','ns_subpages', 'ns_parent', 'ns_target', 'ns_system', 'ns_hidden', 'ns_count', 'ns_class','ns_name','ns_default','ns_canonical'),
			array('namespace_names.ns_id=namespace.ns_id'),
			'Setup',
			array('ORDER BY'=>'namespace.ns_id ASC')
		);

		while( $row = $dbr->fetchObject( $res ) ){	
			# See Namespace.php for documentation on all namespace
			# properties which are accessed below.	
			$id=$row->ns_id;
			if(!array_key_exists($id,$this->nsarray)) {

				$this->nsarray[$id]=new Namespace();	
				# Cannot currently be changed through the UI - is
				# there a need for it to be changeable?
				$this->nsarray[$id]->setMovable(
					$id < NS_MAIN || $id==NS_FILE || 
					$id==NS_CATEGORY ? false : true );
				$this->nsarray[$id]->setIndex($id);
				$this->nsarray[$id]->setSystemType($row->ns_system);
				$this->nsarray[$id]->setSearchedByDefault($row->ns_search_default);
				$this->nsarray[$id]->setSubpages($row->ns_subpages);
				$this->nsarray[$id]->setHidden($row->ns_hidden);
				$this->nsarray[$id]->setTarget($row->ns_target);
				$this->nsarray[$id]->setHandlerClass($row->ns_class);
				$this->nsarray[$id]->setCountable($row->ns_count);
				$this->nsarray[$id]->setParentIndex($row->ns_parent);
				$nsi=$this->nsarray[$id]->addName($row->ns_name);
				if($row->ns_default) {
					$this->nsarray[$id]->setDefaultNameIndex($nsi);
				}
				if($row->ns_canonical) {
					$this->nsarray[$id]->setCanonicalNameIndex($nsi);
				}


			} else {

				$nsi=$this->nsarray[$id]->addName($row->ns_name);
				if($row->ns_default) {
					$this->nsarray[$id]->setDefaultNameIndex($nsi);
				}
				if($row->ns_canonical) {
					$this->nsarray[$id]->setCanonicalNameIndex($nsi);
				}

			}
		}
		$dbr->freeResult( $res );
		$wgMemc->set($key,$this->nsarray);
		$this->refreshReverseIndex();
	}

	/**
	* Convert a "pseudonamespace" (just prefixed titles) into a real
	* one.
	*
	* @param string $prefix - The pseudonamespace prefix string
	* @param Namespace $target - the target namespace object
	* @param Namespace $source - the source namespace object (should
	*  usually be $this->nsarray[NS_MAIN] or ..[NS_TALK]). This is the
	*  one we expect the prefixed titles to be stored in.
	* @param boolean $merge - Is it acceptable to merge into a namespace
	*  which does already contain pages? This is potentially irreversible!
	*
	* Why pass around Namespace objects? This saves us some validation,
	* since the indexes can be assumed to exist.
	*
	* @static
	*/
	function convertPseudonamespace($prefix,$target,$source,$merge=false) {
		$dbm =& wfGetDB(DB_MASTER);
		$dbs =& wfGetDB(DB_SLAVE);
		$fname="Namespace::convertPseudonamespace";
		$targetcount=$target->countPages();
		if(!$merge && $targetcount>0) {
			return array(NS_RESULT=>NS_NON_EMPTY);
		}
		$table = $dbs->tableName( 'page' );
		$eprefix     = $dbs->strencode( $prefix );
		$likeprefix = str_replace( '_', '\\_', $eprefix);
		$targetid=$target->getIndex();
		$sourceid=$source->getIndex();
		
		$sql = "SELECT page_id AS id,
		               page_title AS oldtitle,
		               TRIM(LEADING '$eprefix:' FROM page_title) AS title
		          FROM {$table}
		         WHERE page_namespace=$sourceid
		           AND page_title LIKE '$likeprefix:%'";
		
		$result = $dbs->query( $sql, $fname );
		$set = array();
		while( $row = $dbs->fetchObject( $result ) ) {
			$set[] = $row;
		}
		$dbs->freeResult( $result );
		
		if(!count($set)) {
			return array(NS_RESULT=>NS_PSEUDO_NOT_FOUND);
		} else {
			# Check duplicates
			if($targetcount) {
				$dupeTitles=array();
				foreach($set as $row) {
					$pageExists=$dbs->selectField(
						'page',
						'count(*)',
						array('page_title'=>$row->title,
						      'page_namespace'=>$targetid)
					);
					if($pageExists) {
						$dupeTitles[]=$row->title;
					}
				}
				if(count($dupeTitles)) {
					return(array(
						NS_RESULT => NS_DUPLICATE_TITLES,
						NS_DUPLICATE_TITLE_LIST => $dupeTitles));

				}
			}		
			foreach($set as $row) {
				$dbm->update( $table,
					array(
						"page_namespace" => $targetid,
						"page_title"     => $row->title,
					),
					array(
						"page_namespace" => $sourceid,
						"page_title"     => $row->oldtitle,
					),
					$fname );
			}
		}
		return array(NS_RESULT=>NS_PSEUDO_CONVERTED);

	}

	function getNamespaceObjectByIndex( $index ) {
		if(array_key_exists($index,$this->nsarray)) {
			return $this->nsarray[$index];
		} else {
			return null;
		}
	}

	function hasIndex( $index ) {
		return !empty($this->nsarray[$index]);
	}

	function getNamespaceObjectByName( $name ) {
		$index=$this->getIndexForName($name);
		return $this->nsarray[$index];
	}

	function getAllNamespaceObjects( ) {
		return $this->nsarray;
	}

}


?>
