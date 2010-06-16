<?php

/**
 * Main classes for the PeopleCategories extension.
 *
 * @file
 * @ingroup Extensions
 */

class PeopleCategoriesPage extends CategoryPage {
	function view() {
		// From CategoryPage::view()
		if( NS_CATEGORY == $this->mTitle->getNamespace() ) {
		$this->openShowCategory();
		}

		Article::view();

		// Customization
		if( NS_CATEGORY == $this->mTitle->getNamespace() ) {
			$this->closeShowCategory();
		}
	}

	function closeShowCategory() {
		// From CategoryPage::view();
		global $wgOut, $wgRequest;
		$from = $wgRequest->getVal( 'from' );
		$until = $wgRequest->getVal( 'until' );

		// Customization.
		$viewer = new PeopleCategoriesViewer( $this->mTitle, $from, $until );

		// From CategoryPage::view();
		$wgOut->addHTML( $viewer->getHTML() );
	}
}

class PeopleCategoriesViewer extends CategoryViewer {
	function addPage( $title, $sortkey, $pageLength, $isRedirect = false ) {
		// From CategoryViewer::addPage
		global $wgContLang;
		$this->articles_start_char[] = $wgContLang->convert( $wgContLang->firstChar( $sortkey ) );

		// Customization
		global $wgPeopleCategories;
		$name = $title->getText();
		if( in_array( $this->title->getText(), $wgPeopleCategories ) && strpos( $name, ' ' ) ) {
			$first = substr( $name, 0, strrpos( $name, ' ' ) );
			$last = substr( $name, strrpos( $name, ' ' ) + 1 );
			$name = $last . ', ' . $first;
		}
		if( $title->getNamespace() !== NS_MAIN ) {
			$name = $title->getNsText() . ':' . $name;
		}

		$linker = new Linker;
		if( $isRedirect ) {
			$this->articles[] =
				'<span class="redirect-in-category">' .
				$linker->link( $title, $name ) .
				'</span>';
		} else {
			$this->articles[] = $linker->link( $title, $name );
		}
	}
}
