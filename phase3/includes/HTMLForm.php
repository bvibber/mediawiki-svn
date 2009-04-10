<?php

class HTMLForm {

	/* The descriptor is an array of arrays.
		i.e. array(
					'fieldname' => array( 'section' => 'section/subsection',
											properties... ),
					...
				  )
	 */
	 
	 static $typeMappings = array(
	 	'text' => 'HTMLTextField',
	 	'select' => 'HTMLSelectField',
	 	'radio' => 'HTMLRadioField',
	 	'multiselect' => 'HTMLMultiSelectField',
	 	'check' => 'HTMLCheckField',
	 	'toggle' => 'HTMLCheckField',
	 	'int' => 'HTMLIntField',
	 	'info' => 'HTMLInfoField',
	 );
	 
	function __construct( $descriptor, $messagePrefix ) {
		$this->mMessagePrefix = $messagePrefix;
	
		// Expand out into a tree.
		$loadedDescriptor = array();
		$this->mFlatFields = array();
		
		foreach( $descriptor as $fieldname => $info ) {
			$section = '';
			if ( isset( $info['section'] ) )
				$section = $info['section'];
				
			$info['name'] = $fieldname;
			
			$field = $this->loadInputFromParameters( $info );
			$field->mParent = $this;
			
			$setSection =& $loadedDescriptor;
			if ($section) {
				$sectionParts = explode( '/', $section );
				
				while( count($sectionParts) ) {
					$newName = array_shift( $sectionParts );
					
					if ( !isset($setSection[$newName]) ) {
						$setSection[$newName] = array();
					}
					
					$setSection =& $setSection[$newName];
				}
			}
			
			$setSection[$fieldname] = $field;
			$this->mFlatFields[$fieldname] = $field;
		}
		
		$this->mFieldTree = $loadedDescriptor;
	}

	static function loadInputFromParameters( $descriptor ) {
		if ( isset( $descriptor['class'] ) ) {
			$class = $descriptor['class'];
		} elseif ( isset( $descriptor['type'] ) ) {
			$class = self::$typeMappings[$descriptor['type']];
		}
		
		if (!$class) {
			throw new MWException( "Descriptor with no class: ".print_r( $descriptor, true ) );
		}
		
		$obj = new $class( $descriptor );
		
		return $obj;
	}

	function show() {
		$html = '';
		
		// Load data from the request.
		$this->loadData();
		
		// Try a submission
		$result = $this->trySubmit();
 		
		if ($result === true)
			return $result;
			
		// Display form.
		$this->displayForm( $result );
	}
	
	/** Return values:
	  * TRUE == Successful submission
	  * FALSE == No submission attempted
	  * Anything else == Error to display.
	  */
	function trySubmit() {
		global $wgRequest, $wgUser;
		
		$editToken = $wgRequest->getVal( 'wpEditToken' );
		
		if ( !$wgUser->matchEditToken( $editToken ) ) {
			return false;
		}
		
		// Check for validation
		foreach( $this->mFlatFields as $fieldname => $field ) {
			if ( !empty($field->mParams['nodata']) ) continue;
			if ( $field->validate( $this->mFieldData[$fieldname],
					$this->mFieldData ) !== true ) {
				return isset($this->mValidationErrorMessage) ?
						$this->mValidationErrorMessage : array( 'htmlform-invalid-input' );
			}
		}
		
		$callback = $this->mSubmitCallback;
		
		$res = call_user_func( $callback, $this->mFieldData );
		
		return $res;
	}
	
	function setSubmitCallback( $cb ) {
		$this->mSubmitCallback = $cb;
	}
	
	function setValidationErrorMessage( $msg ) {
		$this->mValidationErrorMessage = $msg;
	}
	
	function displayForm( $submitResult ) {
		global $wgUser, $wgOut;
		
		if ( $submitResult !== false ) {
			$this->displayErrors( $submitResult );
		}
		
		$html = $this->displaySection( $this->mFieldTree );
		
		// Hidden fields
		$html .= Xml::hidden( 'wpEditToken', $wgUser->editToken() );
		$html .= Xml::hidden( 'title', $this->getTitle() );
		
		$attribs = array();
		
		if ( isset($this->mSubmitID) )
			$attribs['id'] = $this->mSubmitID;
		
		$html .= Xml::submitButton( $this->getSubmitText(), $attribs );
		
		$html = Xml::tags( 'form',
							array(
								'action' => $this->getTitle()->getFullURL(),
								'method' => 'post',
							),
							$html );
							
		$wgOut->addHTML( $html );
	}
	
	function displayErrors( $errors ) {
		if ( is_array( $errors ) ) {
			$errorstr = $this->formatErrors( $errors );
		} else {
			$errorstr = $errors;
		}
		
		$errorstr = Xml::tags( 'div', array( 'class' => 'error' ), $errorstr );
		
		global $wgOut;
		$wgOut->addHTML( $errorstr );
	}
	
	static function formatErrors( $errors ) {
		$errorstr = '';
		foreach ( $errors as $error ) {
			if (is_array($error)) {
				$msg = array_shift($error);
			} else {
				$msg = $error;
				$error = array();
			}
			$errorstr .= Xml::tags( 'li',
									null,
									wfMsgExt( $msg, array( 'parseinline' ), $error )
									);
		}
		
		$errorstr = Xml::tags( 'ul', null, $errorstr );
		
		return $errorstr;
	}
	
	function setSubmitText( $t ) {
		$this->mSubmitText = $t;
	}
	
	function getSubmitText() {
		return $this->mSubmitText;
	}
	
	function setSubmitID( $t ) {
		$this->mSubmitID = $t;
	}
	
	function setMessagePrefix( $p ) {
		$this->mMessagePrefix = $p;
	}
	
	function setTitle( $t ) {
		$this->mTitle = $t;
	}
	
	function getTitle() {
		return $this->mTitle;
	}
	
	function displaySection( $fields ) {
		$tableHtml = '';
		$subsectionHtml = '';
		
		foreach( $fields as $key => $value ) {
			if ( is_object( $value ) ) {
				$v = empty($value->mParams['nodata'])
							? $this->mFieldData[$key]
							: $value->getDefault();
				$tableHtml .= $value->getTableRow( $v );
			} elseif ( is_array( $value ) ) {
				$section = $this->displaySection( $value );
				$legend = wfMsg( "{$this->mMessagePrefix}-$key" );
				$subsectionHtml .= Xml::fieldset( $legend, $section );
			}
		}
		
		$tableHtml = "<table><tbody>\n$tableHtml</tbody></table>\n";
		
		return $subsectionHtml . "\n" . $tableHtml;
	}
	
	function loadData() {
		global $wgRequest;
		
		$fieldData = array();
		
		foreach( $this->mFlatFields as $fieldname => $field ) {
			if ( !empty($field->mParams['nodata']) ) continue;
			$fieldData[$fieldname] = $field->loadDataFromRequest( $wgRequest );
		}
		
		// Filter data.
		foreach( $fieldData as $name => &$value ) {
			$field = $this->mFlatFields[$name];
			$value = $field->filter( $value, $this->mFlatFields );
		}
		
		$this->mFieldData = $fieldData;
	}
}

abstract class HTMLFormField {
	abstract function getInputHTML( $value );
	
	function validate( $value, $alldata ) {
		if ( isset($this->mValidationCallback) ) {
			return call_user_func( $this->mValidationCallback, $value, $alldata );
		}
		
		return true;
	}
	
	function filter( $value, $alldata ) {
		if( isset( $this->mFilterCallback ) ) {
			$value = call_user_func( $this->mFilterCallback, $value, $alldata );
		}
		
		return $value;
	}
	
	function loadDataFromRequest( $request ) {
		if ($request->getCheck( $this->mName ) ) {
			return $request->getText( $this->mName );
		} else {
			return $this->getDefault();
		}
	}
	
	function __construct( $params ) {
		$this->mParams = $params;
		
		if (isset( $params['label-message'] ) ) {
			$msgInfo = $params['label-message'];
			
			if ( is_array( $msgInfo ) ) {
				$msg = array_shift( $msgInfo );
			} else {
				$msg = $msgInfo;
				$msgInfo = array();
			}
			
			$this->mLabel = wfMsgExt( $msg, 'parseinline', $msgInfo );
		} elseif ( isset($params['label']) ) {
			$this->mLabel = $params['label'];
		}
		
		if ( isset( $params['name'] ) ) {
			$this->mName = 'wp'.$params['name'];
			$this->mID = 'mw-input-'.$params['name'];
		}
		
		if ( isset( $params['default'] ) ) {
			$this->mDefault = $params['default'];
		}
		
		if ( isset( $params['id'] ) ) {
			$this->mID = $params['id'];
		}
		
		if ( isset( $params['validation-callback'] ) ) {
			$this->mValidationCallback = $params['validation-callback'];
		}
		
		if ( isset( $params['filter-callback'] ) ) {
			$this->mFilterCallback = $params['filter-callback'];
		}
	}
	
	function getTableRow( $value ) {
		// Check for invalid data.
		global $wgRequest;
		
		$errors = $this->validate( $value, $this->mParent->mFieldData );
		if ( $errors === true || !$wgRequest->wasPosted() ) {
			$errors = '';
		} else {
			$errors = Xml::tags( 'span', array( 'class' => 'error' ), $errors );
		}
		
		$html = '';
		
		$html .= Xml::tags( 'td', array( 'style' => 'text-align: right;' ),
					Xml::tags( 'label', array( 'for' => $this->mID ), $this->getLabel() )
				);
		$html .= Xml::tags( 'td', array( 'class' => 'mw-input' ),
							$this->getInputHTML( $value ) ."\n$errors" );
		
		$html = Xml::tags( 'tr', null, $html ) . "\n";
		
		return $html;
	}
	
	function getLabel() {
		return $this->mLabel;
	}
	
	function getDefault() {
		if ( isset( $this->mDefault ) ) {
			return $this->mDefault;
		} else {
			return null;
		}
	}
}

class HTMLTextField extends HTMLFormField {

	function getSize() {
		return isset($this->mParams['size']) ? $this->mParams['size'] : 45;
	}

	function getInputHTML( $value ) {
		$attribs = array( 'id' => $this->mID );
		
		if ( isset($this->mParams['maxlength']) ) {
			$attribs['maxlength'] = $this->mParams['maxlength'];
		}
		
		return Xml::input( $this->mName,
							$this->getSize(),
							$value,
							$attribs );
	}
	
}

class HTMLIntField extends HTMLTextField {
	function getSize() {
		return isset($this->mParams['size']) ? $this->mParams['size'] : 20;
	}
	
	function validate( $value, $alldata ) {
		$p = parent::validate($value, $alldata);
		
		if ($p !== true) return $p;
		
		if ( intval($value) != $value ) {
			return wfMsgExt( 'htmlform-int-invalid', 'parse' );
		}
		
		$in_range = true;
		
		if ( isset($this->mParams['min']) ) {
			$min = $this->mParams['min'];
			if ( $min > $value )
				return wfMsgExt( 'htmlform-int-toolow', 'parse', array($min) );
		}
		
		if ( isset($this->mParams['max']) ) {
			$max = $this->mParams['max'];
			if ($max < $value)
				return wfMsgExt( 'htmlform-int-toohigh', 'parse', array($max) );
		}
		
		return true;
	}
}

class HTMLCheckField extends HTMLFormField {
	function getInputHTML( $value ) {
		return Xml::check( $this->mName, $value, array( 'id' => $this->mID ) ) . '&nbsp;' .
				Xml::tags( 'label', array( 'for' => $this->mID ), $this->mLabel );
	}
	
	function getLabel( ) {
		return '&nbsp;'; // In the right-hand column.
	}
	
	function loadDataFromRequest( $request ) {
		$invert = false;
		if ( isset( $this->mParams['invert'] ) && $this->mParams['invert'] ) {
			$invert = true;
		}
		
		// GetCheck won't work like we want for checks.
		if ($request->getCheck( 'wpEditToken' ) ) {
			// XOR has the following truth table, which is what we want
			// INVERT VALUE | OUTPUT
			// true   true  | false
			// false  true  | true
			// false  false | false
			// true   false | true
			return $request->getBool( $this->mName ) xor $invert;
		} else {
			return $this->getDefault();
		}
	}
}

class HTMLSelectField extends HTMLFormField {
	
	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );
		if ($p !== true) return $p;
		if ( array_key_exists( $value, $this->mParams['options'] ) )
			return true;
		else
			return wfMsgExt( 'htmlform-select-badoption', 'parseinline' );
	}
	
	function getInputHTML( $value ) {
		$select = new XmlSelect( $this->mName, $this->mID, $value );
		
		foreach( $this->mParams['options'] as $key => $label ) {
			$select->addOption( $label, $key );
		}
		
		return $select->getHTML();
	}
}

class HTMLMultiSelectField extends HTMLFormField {
	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );
		if ($p !== true) return $p;
		
		if (!is_array($value)) return false;
		// If all options are valid, array_intersect of the valid options and the provided
		//  options will return the provided options.
		$validValues = array_intersect( $value, array_keys($this->mParams['options']) );
		if ( count( $validValues ) == count($value) )
			return true;
		else
			return wfMsgExt( 'htmlform-select-badoption', 'parseinline' );
	}
	
	function getInputHTML( $value ) {
		$html = '';
		foreach( $this->mParams['options'] as $key => $label ) {
			global $wgRequest;
			$checkbox = Xml::check( $this->mName.'[]', in_array( $key, $value ),
							array( 'id' => $this->mID, 'value' => $key ) );
			$checkbox .= '&nbsp;' . Xml::tags( 'label', array( 'for' => $this->mID ), $label );
			
			$html .= Xml::tags( 'p', null, $checkbox );
		}
		
		return $html;
	}
	
	function loadDataFromRequest( $request ) {
		// won't work with getCheck
		if ($request->getCheck( 'wpEditToken' ) ) {
			$arr = $request->getArray( $this->mName );
			
			if (!$arr)
				$arr = array();
				
			return $arr;
		} else {
			return $this->getDefault();
		}
	}
	
	function getDefault() {
		if ( isset( $this->mDefault ) ) {
			return $this->mDefault;
		} else {
			return array();
		}
	}
}

class HTMLRadioField extends HTMLFormField {
	function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );
		if ($p !== true) return $p;
		
		if (!is_string($value) && !is_int($value))
			return false;
		
		if ( array_key_exists( $value, $this->mParams['options'] ) )
			return true;
		else
			return wfMsgExt( 'htmlform-select-badoption', 'parseinline' );
	}
	
	function getInputHTML( $value ) {
		$html = '';
		
		foreach( $this->mParams['options'] as $key => $label ) {
			$html .= Xml::radio( $this->mName, $key, $key == $value,
									array( 'id' => $this->mID."-$key" ) );
			$html .= '&nbsp;' .
				Xml::tags( 'label', array( 'for' => $this->mID."-$key" ), $label );
			$html .= "<br/>";
		}
		
		return $html;
	}
}

class HTMLInfoField extends HTMLFormField {
	function __construct( $info ) {
		$info['nodata'] = true;
		
		parent::__construct($info);
	}
	
	function getInputHTML( $value ) {
		return !empty($this->mParams['raw']) ? $value : htmlspecialchars($value);
	}
	
	function getTableRow( $value ) {
		if ( !empty($this->mParams['rawrow']) ) {
			return $value;
		}
		
		return parent::getTableRow( $value );
	}
}
