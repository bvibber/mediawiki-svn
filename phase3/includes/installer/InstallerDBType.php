<?php

/**
 * Base class for DBMS-specific installation helper classes
 */
abstract class InstallerDBType {
	/** The Installer object */
	var $parent;

	/** 
	 * Construct and initialise parent.
	 * This is typically only called from Installer::getDBInstaller()
	 */
	function __construct( $parent ) {
		$this->parent = $parent;
	}

	/**
	 * Convenience function
	 * Check if a named extension is present
	 */
	function checkExtension( $name ) {
		wfSuppressWarnings();
		$compiled = extension_loaded( $name )
			|| ( $this->parent->haveDl() && dl( $name . '.' . PHP_SHLIB_SUFFIX ) );
		wfRestoreWarnings();
		return $compiled;
	}

	/**
	 * Return the internal name, e.g. 'mysql', or 'sqlite'
	 */
	abstract function getName();

	/**
	 * Get the internationalised name for this DBMS
	 */
	function getReadableName() {
		return wfMsg( 'config-type-' . $this->getName() );
	}

	/**
	 * @return true if the client library is compiled in
	 */
	abstract function isCompiled();

	/**
	 * Get an array of MW configuration globals that will be configured by this class.
	 */
	abstract function getGlobalNames();
	
	/**
	 * Get a name=>value map of MW configuration globals that overrides
	 * DefaultSettings.php
	 */
	function getGlobalDefaults() {
		return array();
	}

	/**
	 * Get a name=>value map of internal variables used during installation
	 */
	function getInternalDefaults() { 
		return array();
	}

	/**
	 * Get HTML for a web form that configures this database
	 * If this is called, $this->parent can be assumed to be a WebInstaller
	 */
	abstract function getConnectForm();

	/**
	 * Set variables based on the request array, assuming it was submitted
	 * via the form returned by getConnectForm()
	 * If this is called, $this->parent can be assumed to be a WebInstaller
	 */
	abstract function submitConnectForm();

	/**
	 * Get a variable, taking local defaults into account
	 */
	function getVar( $var, $default = null ) {
		$defaults = $this->getGlobalDefaults();
		$internal = $this->getInternalDefaults();
		if ( isset( $defaults[$var] ) ) {
			$default = $defaults[$var];
		} elseif ( isset( $internal[$var] ) ) {
			$default = $internal[$var];
		}
		return $this->parent->getVar( $var, $default );
	}

	/**
	 * Convenience function for a labelled text box to configure a variable
	 */
	function getLabelledTextBox( $var, $label ) {
		$name = $this->getName() . '_' . $var;
		$value = $this->getVar( $var );
		return 
			"<div class=\"config-input\">\n" .
			$this->parent->getLabel( $label, $name ) . 
			$this->parent->getTextBox( $name, $value ) .
			"</div>\n";
	}

	/**
	 * Convenience function for a labelled password box.
	 * Implements password hiding
	 */
	function getLabelledPasswordBox( $var, $label ) {
		$name = $this->getName() . '_' . $var;
		$realPassword = $this->getVar( $var );
		if ( strlen( $var ) ) {
			$fakeValue = $this->parent->getFakePassword( $realPassword );
		} else {
			$fakeValue = '';
		}
		return 
			"<div class=\"config-input\">\n" .
			$this->parent->getLabel( $label, $name ) . 
			$this->parent->getTextBox( $name, $fakeValue, 'password' ) .
			"</div>\n";
	}

	/**
	 * Convenience function for a labelled checkbox
	 */
	function getLabelledCheckBox( $var, $label, $attribs = array() ) {
		$name = $this->getName() . '_' . $var;
		$value = $this->getVar( $var );
		return 
			"<div class=\"config-input-check\">\n" .
			"<label>\n" .
			$this->parent->getCheckBox( $name, $value, $attribs ) . "\n" .
			wfMsgHtml( $label ) . "\n" .
			"</label>\n" .
			"</div>\n";
	}

	/**
	 * Convenience function to set variables based on form data
	 * Has some heuristics that may need to be overridden in child classes.
	 */
	function setVarsFromRequest() {
		$newValues = array();
		$varNames = array_merge( $this->getGlobalNames(), 
			array_keys( $this->getInternalDefaults() ) );
		foreach ( $varNames as $name ) {
			$value = $this->parent->request->getVal( $this->getName() . '_' . $name );
			$newValues[$name] = $value;
			if ( $value !== null ) {
				if ( stripos( $name, 'password' ) !== false ) {
					$this->parent->setPassword( $name, $value );
				} else {
					$this->parent->setVar( $name, $value );
				}
			}
		}
		return $newValues;
	}
}

