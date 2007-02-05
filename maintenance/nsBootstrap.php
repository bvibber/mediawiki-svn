<?php
// Purpose: Create entries in the namespace and namespace_names tables,
// based on the configured language. Take into account existing (deprecated)
// namespace settings in an update.
//
// We can't depend on commandLine.inc because this has to be runnable from the installer


global $wgLanguageCode;
$wgContLanguageCode = $wgLanguageCode;
$wgContLangClass = 'Language' . str_replace( '-', '_', ucfirst( $wgContLanguageCode ) );

$wgContLang = new StubContLang;
$wgContLang->initEncoding();

class NamespaceBootstrap {
	var $mStdNs;
	var $mExtraNs;
	var $dbw;

	var $mContLangNs;
	var $mContLangNsSynonyms;
	var $mLanguageCode;

	function NamespaceBootstrap() {
		global $wgExtraNamespaces, $wgContLang, $wgNamespaceSynonymsEn;
		
		$this->mStdNs = array(
			'NS_MEDIA' => NS_MEDIA,
			'NS_SPECIAL' => NS_SPECIAL,
			'NS_MAIN' => NS_MAIN,
			'NS_MAIN' => NS_MAIN,
			'NS_TALK' => NS_TALK,
			'NS_USER' => NS_USER,
			'NS_USER_TALK' => NS_USER_TALK,
			'NS_PROJECT' => NS_PROJECT,
			'NS_PROJECT_TALK' => NS_PROJECT_TALK,
			'NS_IMAGE' => NS_IMAGE,
			'NS_IMAGE_TALK' => NS_IMAGE_TALK,
			'NS_MEDIAWIKI' => NS_MEDIAWIKI,
			'NS_MEDIAWIKI_TALK' => NS_MEDIAWIKI_TALK,
			'NS_TEMPLATE' => NS_TEMPLATE,
			'NS_TEMPLATE_TALK' => NS_TEMPLATE_TALK,
			'NS_HELP' => NS_HELP,
			'NS_HELP_TALK' => NS_HELP_TALK,
			'NS_CATEGORY' => NS_CATEGORY,
			'NS_CATEGORY_TALK' => NS_CATEGORY_TALK
		);

		$this->mExtraNs = isset( $wgExtraNamespaces ) ? $wgExtraNamespaces : array();
		$this->mContLangNs = $wgContLang->getNamespacesBootstrap();
		$this->mLanguageCode = $wgContLang->getCode();
		
		if ( $wgNamespaceSynonymsEn == $wgContLang->getNamespaceSynonymsBootstrap() && $this->mLanguageCode !== 'en' )
			$this->mContLangNsSynonyms = array();
		else
			$this->mContLangNsSynonyms = $wgContLang->getNamespaceSynonymsBootstrap();
		
		
		$this->dbw =& wfGetDB( DB_MASTER );
	}

	function initialize() {
		global $wgContLang;

		$fname = 'NamespaceBootstrap::initialize';
		
		// namespace table, standard namespaces
		foreach ( $this->mStdNs as $system => $id ) {
			$subject = $this->getSubject( $id );
			
			$this->dbw->insert( 'namespace',
				array(
					'ns_id' => $id,
					'ns_system' => $system,
					'ns_subpages' => $this->getSubpages( $id ),
					'ns_search_default' => $this->getSearch( $id ),
					'ns_target' => null,
					'ns_parent' => $subject === $id ? null : $subject,
					'ns_hidden' => null,
					'ns_count' => $id == NS_MAIN ? true : false,
					'ns_class' => null,
				),
				$fname
			);
		}
		
		// namespace table, extra namespaces
		foreach ( $this->mExtraNs as $id => $name ) {
			$subject = $this->getSubject( $id );
			
			$this->dbw->insert( 'namespace',
				array(
					'ns_id' => $id,
					'ns_system' => null, // extra namespaces are null
					'ns_subpages' => $this->getSubpages( $id ),
					'ns_search_default' => $this->getSearch( $id ),
					'ns_target' => null,
					'ns_parent' => $subject === $id ? null : $subject,
					'ns_hidden' => null,
					'ns_count' => false,
					'ns_class' => null,
				),
				$fname
			);
		}

		// Cache already inserted results so we won't get a case where
		// we'll do a bogus insert because namespaces haven't been
		// translated or the translation equals the original.
		$nscache = array();

		// namespace_names, English fallbacks

		//FIXME: need to use proper language code
		$langobj = Language::factory( 'en' );
		$langobj->initEncoding();
		$langobj->initContLang();

		foreach ( $langobj->getNamespaces() as $id => $text ) {
			if ( @$nscache[$id] === $text || $text === '' )
				continue;
			
			$nscache[$id] = $text;

			$this->dbw->insert( 'namespace_names',
				array(
					'ns_id' => $id,
					'ns_name' => $text,
					'ns_default' => $this->mLanguageCode == 'en' || $this->mContLangNs[$id] === $text ? 1 : 0,
					'ns_canonical' => 1,
				),
				$fname
			);
		}


		// namespace_names, content language
		foreach ( $this->mContLangNs as $ns => $text ) {
			if ( $text === '' || @$nscache[$ns] === $text )
				continue;
			
			$nscache[$ns] = $text;

			$this->dbw->insert( 'namespace_names',
				array(
					'ns_id' => $ns,
					'ns_name' => $text,
					'ns_default' => 1,
					'ns_canonical' => 0
				),
				$fname
			);
			
		}

		// namespace_names, synonyms 
		foreach ( $this->mContLangNsSynonyms as $id => $synonyms )
			foreach ( $synonyms as $synonym ) {
				if ( $nscache[$id] === $synonym )
					continue;
				
				$this->dbw->insert( 'namespace_names',
					array(
						'ns_id' => $id,
						'ns_name' => $synonym,
						'ns_default' => 0,
						'ns_canonical' => 0
					),
					$fname
				);
			}

		// namespace_names, Project and Project_talk are special cases, should be canonical
		foreach ( array( NS_PROJECT => 'Project', NS_PROJECT_TALK => 'Project_talk' ) as $id => $text ) {
			if ( $nscache[$id] === $text )
				continue;
			
			$this->dbw->insert( 'namespace_names',
				array(
					'ns_id' => $id,
					'ns_name' => $text,
					'ns_default' => 0,
					'ns_canonical' => 1
				),
				$fname
			);
		}
		
		// namespace_names, Import extra namespaces specified using
		// legacy syntax.
		foreach ( $this->mExtraNs as $id => $name ) {
			$this->dbw->insert( 'namespace_names',
				array(
					'ns_id' => "$id",
					'ns_name' => $name,
					'ns_default' => 1,
					'ns_canonical' => 0,
				),
				$fname
			);
		}
	}

	function getSubpages( $ns ) {
		global $wgNamespacesWithSubpages;

		return @$wgNamespacesWithSubpages[$ns] ? 1 : 0;
		
	}
	
	function getSearch( $ns ) {
		global $wgNamespacesToBeSearchedDefault;

		return @$wgNamespacesToBeSearchedDefault[$ns] ? 1 : 0;
	}

	function isTalk( $index ) {
		return ($index > 0)  // Special namespaces are negative
			&& ($index % 2); // Talk namespaces are odd-numbered
	}

	function getSubject( $index ) {
		if ( $this->isTalk( $index ) ) {
			return $index - 1;
		} else {
			return $index;
		}
	}
}

$nb = new NamespaceBootstrap;
$nb->initialize();
