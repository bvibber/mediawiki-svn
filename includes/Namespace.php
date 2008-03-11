<?php
/**
 * Class for creating namespace objects
 * and static namespace-related functions.
 *
 * Definitions:
 *
 * Namespace: 
 *  A prefix of the form "Abc:" represented on the database level
 *  with an integer number. Using namespaces, pages with the same title
 *  can exist in different contexts; for example, a page 
 *  "User:Stephen King" could coexist with "Stephen King". (No prefix
 *  refers to the "article namespace", or to the target namespace; see
 *  below.
 *
 * Default name, synonyms:
 *  Any namespace can have an arbitrary number of valid names. This is
 *  to ensure that, for example, "Image:", "File:", "Video:" etc. can
 *  all be used to access uploaded files. The default name is the name
 *  which all these valid names ''redirect to'', that is, if you access
 *  index.php?title=Image:Bear.jpg, it will redirect you to
 *  index.php?Title=File:Bear.jpg, because "File:" is the default name
 *  for this namespace. Any name which is not the default name will be
 *  referred to herein as a synyonm.
 *
 * Canonical namespace name:
 *  Due to the fact that namespace names are translated into many 
 *  languages, and customized to many wikis, it is desirable to have
 *  a reliable way by which a namespace with a particular ''meaning''
 *  can be accessed on any wiki installation. For example, you may
 *  want to access a user page on the Serbian Wikipedia, but you don't
 *  know the equivalent of "User:" in Serbian. During installation,
 *  some namespace names are flagged as canonical. These cannot
 *  be changed using the namespace manager, and can be expected
 *  to work on all wikis. A canonical namespace name does not have
 *  to be the default name, e.g., "User:" would still redirect to
 *  the Serbian equivalent.
 *
 * Namespace target:
 *  Depending on the nature of your project, it may be desirable
 *  that all [[unprefixed links]] within a namespace point to a
 *  particular destination (another namespace or InterWiki). An
 *  example is Wikibooks, where namespaces are used to separate
 *  different types of books, and you would like all links within
 *  a book to point to other chapters in the book unless otherwise
 *  specified. Another example is Wikinews, where links from the
 *  main namespace typically point to Wikipedia.
 *
 *  Quite simply put, if this target is set for a namespace, all
 *  links without a valid namespace or InterWiki prefix in pages 
 *  in that namespace are treated as if they were written:
 *  [[LINK TARGET:Actual title|Actual title]].
 * 
 * How to modify a namespace and its properties:
 *  Do NOT alter the $wgNamespaces object. Instead, create a clone() 
 *  of the object and change its properties. To delete a name,
 *  set it to null (not ''). Call its save() method. It will be 
 *  compared to the matching object in $wgNamespaces and modified 
 *  if posible. 
 *
 * How to create a new namespace:
 *  Create a new namespace object with the desired properties. Set
 *  the index to NULL. Call its save() method.
 *
 */


require_once("Defines.php");

# Namespace name operations
# used by save() function
define('NS_NAME_MODIFY',1);
define('NS_NAME_DELETE',2);
define('NS_NAME_ADD',3);

/**
 * This class defines the namespace objects which are stored in
 * $wgNamespaces.
 *
 */
class Namespace {

        # Fundamentals
	var 
	$index,               # Database-level index of this namespace
	$systemType;          # If non-empty, string constant that defines 
	                      # a system namespace.

	# Options
	var 
	$isMovable,           # Can pages in this namespace be moved?
	$isCountable,         # Should pages in this namespace count as content (stats)?
	$parentIndex,         # If this is a talk page, what is its mother
	                      # namespace? Otherwise NULL.
	$allowsSubpages,      # Are subpages of the form [[Namespace:Foo/Bar]]
	                      # valid?
	$isSearchedByDefault, # Are pages in this namespace searched by default?
	$isHidden,            # Should this namespace be hidden from the UI?
	$handlerClass,        # Name of an external class to use for handling content
	$target;	      # Treat unprefixed links as prefixed with "$target:"?

	# Associated names
	var 
	$names = array(),     # Contains all the namespace names	
	$defaultNameIndex,    # Index of the name all other names redirect to?
	$canonicalNameIndex;  # Index of the name that's valid everywhere


	/** 
	 * Constructor with reasonable defaults.
	 */
	function Namespace() {
	
		$this->setIndex(NULL);
		$this->setMovable();
		$this->setParentIndex(NULL);
		$this->setSubpages(false);
		$this->setSearchedByDefault(false);
		$this->setTarget(NULL);
		$this->setHidden(false);	
	}	

	/**
	 * @return index to the correct $wgNamespaces object
	 *  or database record for this namespace.
	 */
	function getIndex() {
		return $this->index;
	}

	/**
	 * @param $index New index for this namespace. 
	 *  Generally only used during creation.
	 */
	function setIndex($index) {
		$this->index=$index;
	}

	/**
	 * @return String like NS_MAIN for identifying
	 *  system namespaces (see Defines.php).	 */
	function getSystemType() {
		return $this->systemType;	
	}
	
	/**
	 * Set the system type for this namespace.
	 * @param  string Constant name - needs to exist
	 *  in Defines.php.
	 * @return bool depending on success
	 *
	 */
	function setSystemType($type) {
		$typeString=(string)$type;
		if(defined($typeString)) {
			$this->systemType=$typeString;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Is this a system namsepace?
	 * @return bool
	 */
	function isSystemNamespace() {
		$sys=$this->getSystemType();
		return !empty($sys);
	}
	
	/**
	 * Check if pages in this namespace can be moved.
	 * Special pages, images and categories cannot be moved
	 * @return bool
	 */
	function isMovable() {
		#return $this->isMovable;	
		# FIXME: This means that classes with custom handlers are assumed to never be movable
		return $this->isMovable && empty($this->handlerClass);
	}

	/**
	 * Can pages in this namespace be moved?
	 * @param bool
	 */
	function setMovable($movable=true) {
		$this->isMovable=(bool)$movable;
	}

	/**
	 * Check if pages in this namespaces should be counted in the site statistics
	 * @return bool
	 */
	function isCountable() {
		return $this->isCountable;	
	}

	/**
	 * Set if pages in this namespace should be counted in the site statistics
	 * @param bool
	 */
	function setCountable($countable=true) {
		$this->isCountable=(bool)$countable;
	}

	/**
	 * Are pages from this namespace hidden in lists?
	 * @return bool
	 */	
	function isHidden() {
		return $this->isHidden;
	}

	/**
	 * Should pages from this namespace be hidden in lists?
	 * @param bool
	 */
	function setHidden($hidden=true) {
		$this->isHidden=(bool)$hidden;	
	}
	
	/**
	 * @return int Index of the parent namespace to a 
	 *   child namespace (talk or otherwise), NULL if none
	 */
	function getParentIndex() {
		return $this->parentIndex;
	}

	/**
	 * @return int Same as getParentIndex(), but returns this
	 *    namespace's index if no parent namespace exists. Doubles as
	 *    a static function which returns the same given the namespace
	 *    index.
	 */
	function getSubject($index = null) {
		if( is_null( $index ) ) {
			$ns = $this;
		}
		else {
			$ns = Namespace::get($index);
		}

		if($ns->isTalk()) {
			return $ns->getParentIndex();
		} else {
			return $ns->getIndex();
		}
	}
	

	/**
	 * Set parent namespace
	 * @param int
	 */
	function setParentIndex($index) {
		$this->parentIndex=$index;
	}

	/**
	 * Does this namespace have a parent namespace?
	 * @return bool
	 */	
	function hasParent() {
		return ($this->getParentIndex()!=NULL);
	}

	/**
	 * Synonym for hasParent(), but might be logically different in
	 * the near future, if parent/child relationships go beyond talk
	 * pages.  Doubles as a static function which returns the same
	 * given the namespace index.
	 * @return bool
	 */
	function isTalk( $index = null ) {
		if( is_null( $index ) ) {
			return $this->hasParent();	
		}
		else {
			$nsstore = wfGetNamespaceStore();
			$ns = Namespace::get($index);
			return $ns->hasParent();
		}
	}

	/**
	 * Check if the given namespace is not a talk page
	 * @return bool
	 */
	function isMain( $index = null) {
		if( is_null( $index ) ) {
			return !$this->isTalk();
		}
		else {
			$nsstore = wfGetNamespaceStore();
			$ns = $nsstore->getNamespaceObjectByIndex($index);
			return !$ns->isTalk();
		}
	}


	/**
	 * Is this a "special" namespace (Media:, Special:)?
	 * Special namespaces cannot contain any pages.
	 * @return bool
	 */	
	function isSpecial() {
		return($this->getIndex()<NS_MAIN);
	}

	/**
	 * Is content in this namespace searched by default?
	 * @return bool
	 */
	function isSearchedByDefault() {
		return $this->isSearchedByDefault;		
	}

	/**
	 * Should this namespace be searched by default?
	 * @param bool
	 */
	function setSearchedByDefault($search=true) {
		$this->isSearchedByDefault=(bool)$search;
	}
	
	/**
	 Get the index of the discussion namespace associated
	 with a namespace. If this _is_ a discussion namespace, 
	 return its index.
	 Doubles as a static function which returns the same
	 given the namespace index.

	 @return int	 

	 TODO: support multiple discussion namespaces,
	 so that things like a Review: namespace
	 become possible in parallel to normal talk
	 pages. 
	*/
	function getTalk($index = null) {
		if( is_null( $index ) ) {
			$ns = $this;
		}
		else {
			$nsstore = wfGetNamespaceStore();
			$ns = Namespace::get($index);
		}

		/* This behavior is expected by Title.php! */
		if($ns->isTalk()) return $ns->getIndex();

		$nsstore = wfGetNamespaceStore();
		return $nsstore->getTalk($ns);
	}
	

	/**
	 * Return the default prefix for unprefixed links
	 * from this namespace.
	 * @return string
	 */
	function getTarget() {
		return $this->target;	
	}

	/**
	 * Set the default prefix for unprefixed links, e.g.
	 * "User:" (local namespace prefix) or "MeatBall:"
	 * (InterWiki prefix).
	 *
	 * @param string
	 */
	function setTarget($target) {
		$this->target=(string)$target;
	}

	/**
	 * Does this namespace allow [[/subpages]]?
	 * @return bool
	 */
	function allowsSubpages() {
		return $this->allowsSubpages;
	
	}

	/**
	 * Should this namespace allow [[/subpages]]?
	 * @param bool
	 */
	function setSubpages($subpages=true) {
		$this->allowsSubpages=(bool)$subpages;
	}	

	/**
	 * Return the default name for this namespace, if any.
	 * The default name is the one all others redirect to.
	 *
	 * @return string	
	 */
	function getDefaultName() {	
		if(isset($this->defaultNameIndex) && array_key_exists($this->defaultNameIndex,$this->names)) {
			return $this->names[$this->defaultNameIndex];	
		} else {
			return null;
		}
	}

	/** 
	  Used when a default name is deleted, to assign a new one
	  @return int - index to the first non-empty name of this namespace
			null if there are no non-empty names.
        */
	function getNewDefaultNameIndex() {
		foreach($this->names as $nsi=>$name) {
			if(!empty($name)) {
				return $nsi;
			}
		}
		return null;
	}

	/**
	 * Among the names of this namespace, which one should
	 * be set as the default name?
	 * @param int Key to the names array
	 */	
	function setDefaultNameIndex($index) {
		$this->defaultNameIndex=$index;
	}

	/**
	 * Among the names of this namespace, which one
	 * should be "canonical" (i.e. not editable, and
	 * assumed to exist under this name in other
	 * wikis)?
	 * @param int Key to the names array
	 */
	function setCanonicalNameIndex($index) {
		$this->canonicalNameIndex=$index;
	}
	
	/**
	 * Add a name to the list of names for this
	 * namespace.
	 * @return index of the newly added name,
	 *  or NULL if hte name is not valid.	
	 */
	function addName($name) {
		$index=count($this->names);
		if($this->isValidName($name)) {
			$name=strtr($name, ' ','_');
			$this->names[$index]=$name;
			return $index;
		} else {
			return NULL;
		}
	}

	/**
	 * Return the key in the name list for a given
	 * name.	
	 * @return int Matching key or NULL
	 */
	function getNameIndexForName($findname) {
		foreach($this->names as $nsi=>$name) {
			if($name==$findname) {
				return $nsi;
			}
		}
		return null;
	}

	/**
	 * Change a namespace name.
	 * @param $oldname The old name
	 * @param $newname The new name
	 * @param $checkvalid Does the new name have to be
	 *  valid? (is checked by save() in any case)
	 */
	function setName($oldname,$newname,$checkvalid=true) {
		if($checkvalid && !$this->isValidName($newname)) {
			return NULL;
		}
		$newname=strtr($newname, ' ','_');
		$nsi=$this->getNameIndexForName($oldname);
		if(!is_null($nsi)) {
			$this->names[$nsi]=$newname;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Is this a valid namespace name? Valid characters
	 * are defined in the NS_CHAR constant.
	 * @return bool
	 */
	function isValidName($name) {
		# Consist only of (at least one) valid char(s)
		if(preg_match("/^".NS_CHAR."+$/",$name)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * How many pages does this namespace contain?
	 * @return The number of pages
	*/
	function countPages() {
		$dbs =& wfGetDB(DB_SLAVE);
		return $dbs->selectField(
			'page',
			'count(*)',
			array('page_namespace'=>$this->getIndex())
		);		
	}

	/**
	 * Get the default name with spaces instead of 
	 * underscores.
	 * @return string
	 */
	function getFormattedDefaultName() {
		$ns=$this->getDefaultName();
		return strtr($ns, '_',' ');
	}
	/**
	 * @return the key in the names array for the default
	 * name of this namespace
	 */
	function getDefaultNameIndex() {
		return $this->defaultNameIndex;
	}

	/**
	 * @return the key in the names array for the 
	 * canonical name of this namespace
	 */
	function getCanonicalNameIndex() {
		return $this->canonicalNameIndex;
	}

	/**
	 * @return The canonical name associated with this namespace, or
	 *  NULL.  Doubles as a static function which returns the same
	 *  given the namespace index.
	 */
	function getCanonicalName( $index = null ) {
		if( is_null( $index ) ) {
			$ns = $this;
		}
		else {
			$nsstore = wfGetNamespaceStore();
			$ns = $nsstore->getNamespaceObjectByIndex($index);
		}
		if(!is_null($ns->getCanonicalNameIndex())) {
			return $ns->names[$ns->getCanonicalNameIndex()];
		} else {
			return null;
		}
	}

	/**
	 * An external handler class can be configured for a namespace.
	 * It is expected in extensions/ClassName/ClassName.php by default
	 * and should have a view(), edit() and delete() function.
	 * 
	 * @param $classname
	*/
	function setHandlerClass($classname) {
		$this->handlerClass=$classname;
	}

	/**
	 * @return name of the handler class (string)
	*/
	function getHandlerClass() {
		return $this->handlerClass;
	}
	
	/**
	 * @return directory where the handler class PHP file can be found.
	*/
	function getHandlerPath() {
		global $wgCustomHandlerPath;
		$handler=$this->getHandlerClass();
		if(array_key_exists($handler,$wgCustomHandlerPath)) {
			return $wgCustomHandlerPath[$handler];
		} else {
			return $wgCustomHandlerPath['*'];
		}
	}


	/**
	 * Returns the index for a given canonical name, or NULL
	 * The input *must* be converted to lower case first
	 */
	static function getCanonicalIndex( $name ) {
		$nsstore = wfGetNamespaceStore();
		return $nsstore->getIndexForName( $name );
	}
	
	/**
	 * Can this namespace ever have a talk namespace?
	 * @param $index Namespace index
	 */
	 static function canTalk( $index ) {
	 	return( $index >= NS_MAIN );
	 }


	/**
	 * @param int Key to the names array of the name
	 *  which should be removed.
	 */
	function removeNameByIndex($index) {
		if(array_key_exists($index,$this->names)) {
			unset($this->names[$index]);
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Kill them all! Well, all the ones in this namespace
	 * object. And only if we save().
	 */
	function removeAllNames() {
		$this->names=array();
		return true;
	}

	function save($overrideInterwiki=false,
				  $testSave=false) {
		$nsstore = wfGetNamespaceStore();
		return $nsstore->saveNamespace($this,$overrideInterwiki,$testSave);
	}
		
	/**
 	 * A simple shortcut to save() with the right parameters
	 * to run in test mode. See save() documentation.
	 */
	function testSave($overrideInterwiki=false) {
		return $this->save($overrideInterwiki,true);
	}

	function deleteNamespace($deleteSystem=false) {
		$nsstore = wfGetNamespaceStore();
		return $nsstore->deleteNamespace($this,$deleteSystem);
	}

	/**
	 * Retrieve a namespace object from the store.
	 * This function should be used for all logical operations
	 * on namespaces. The store does not care if an object
	 * exists or not; this function will log all failures
	 * but proceed with a reasonable default dummy namespace.
	 *
	 * If called with no parameter, will return the
	 * namespace object for the current title object.
	 *
	 * @return the object or a harmless, friendly dummy
	*/ 
	static function get($id=null) {
		if(is_null($id)) {
			global $wgTitle;
			$id=$wgTitle->getNamespace();
		}
		$nsstore=wfGetNamespaceStore();
		$rv=$nsstore->getNamespaceObjectByIndex($id);
		if(is_null($rv)) {
			$dummyNs=new Namespace();
			$di=$dummyNs->addName(wfMsg('namespace_dummy_name')."_$id");
			wfDebug("Namespace $id has no object. Verify the namespace definitions in the database! This is quite bad! Moving on ..\n");
			$dummyNs->setDefaultNameIndex($di);
			$rv=$dummyNs;
		}
		return $rv;
	}

	static function getIndexForName($text) {
		$nsstore = wfGetNamespaceStore();
		if(!is_null($id=$nsstore->getIndexForName($text))) {
			return $id;
		} else {
			# If a namespace name gets lost, all its appearances will be replaced
			# with 'Missing namespace $FOO'. Here we do the reverse thing, so we
			# can still retrieve pages, even if their namespaces have gone AWOL.
			if(($sp=strpos(strtolower($text),strtolower(wfMsg('namespace_dummy_name')))) !== false) {
				$missid=substr($text,strlen(wfMsg('namespace_dummy_name'))+1);
				wfDebug("Resolving missing namespace to $missid\n");
				return $missid;
			} else {
				wfDebug('Bad namespace:'.$text."\n");
				return false;
			}
		}
	}
}

?>
