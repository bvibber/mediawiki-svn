<?php
error_reporting( E_ALL ^ ( E_NOTICE | E_WARNING ) );
require_once 'commandLine.inc';

class NamespaceBootstrap {
	var $mStdNs;
	var $mExtraNs;
	var $dbw;

	var $mContLangNs;
	var $mContLangNsSynonyms;

	function NamespaceBootstrap() {
		global $wgExtraNamespaces, $wgContLang;
		
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

		$this->mContLangNs = $wgContLang->getNamespaces();
		$this->mContLangNsSynonyms = $wgContLang->getNamespaceSynonyms();

		$this->dbw =& wfGetDB( DB_MASTER );
	}

	function initialize() {
		global $wgContLang;

		$fname = 'NamespaceBootstrap::execute';
		
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
					'ns_hidden' => null
				),
				$fname
			);
		}

		foreach ( $this->mExtraNs as $id => $name ) {
			$subject = $this->getSubject( $id );
			
			$this->dbw->insert( 'namespace',
				array(
					'ns_id' => $id,
					'ns_system' => "NS_$id",
					'ns_subpages' => $this->getSubpages( $id ),
					'ns_search_default' => $this->getSearch( $id ),
					'ns_target' => null,
					'ns_parent' => $subject === $id ? null : $subject,
					'ns_hidden' => null
				),
				$fname
			);
		}
					
		$nscache = array();

		foreach ( $this->mContLangNs as $ns => $text ) {
			if ( $text === '' || @$nscache[$ns] === $text )
				continue;
			
			$nscache[$ns] = $text;

			$this->dbw->insert( 'namespace_names',
				array(
					'ns_id' => $ns,
					'ns_name' => $nscache[$ns],
					'ns_default' => 1,
					'ns_canonical' => $ns < NS_MAIN ? 1 : 0
				),
				$fname
			);
			
		}

		foreach ( Language::getNamespaces() as $id => $text ) {
			if ( @$nscache[$id] === $text || $text === '' )
				continue;
			else
				$nscache[$id] = $text;
			
			$this->dbw->insert( 'namespace_names',
				array(
					'ns_id' => $id,
					'ns_name' => $text,
					'ns_default' => 0,
					'ns_canonical' => 0,
				),
				$fname
			);
		}

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

	/**
	 * Check if the give namespace is a talk page
	 * @return bool
	 */
	function isTalk( $index ) {
		return ($index > 0)  // Special namespaces are negative
			&& ($index % 2); // Talk namespaces are odd-numbered
	}

	/**
	 * Get the talk namespace corresponding to the given index
	 */
	function getTalk( $index ) {
		if ( $this->isTalk( $index ) ) {
			return $index;
		} else {
			# FIXME
			return $index + 1;
		}
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
