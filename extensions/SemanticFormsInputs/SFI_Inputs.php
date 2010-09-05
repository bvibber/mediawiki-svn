<?php
/**
 * Input definitions for the Semantic Forms Inputs extension.
 *
 * @author Stephan Gambke
 * @author Sanyam Goyal
 * @author Yaron Koren
 * @version 0.3.1
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point.' );
}

class SFIInputs {

	/**
	 * Setup for input type regexp.
	 * Adds the Javascript code used by all regexp filters.
	*/
	static private function regexpSetup() {

		global $wgOut;

		static $hasRun = false;

		if ( !$hasRun ) {
			$hasRun = true;

			wfLoadExtensionMessages( 'SemanticFormsInputs' );

			$jstext = <<<JAVASCRIPT
		function validate_input_with_regexp(input_number, retext, inverse, message, multiple){

			var decoded = jQuery("<div/>").html(retext).text();
			var re = new RegExp(decoded);

			if (multiple) {
				res = true;
				for (i = 1; i <= num_elements; i++) {
					field = document.getElementById('input_' + i + "_" + input_number);
					if (field) {
						match = re.test(field.value);

						if ( !(match && !inverse) && !(!match && inverse) ) {
							infobox = document.getElementById('info_' + i + "_" + input_number);
							infobox.innerHTML += " " + message;
							res=false;
						}
					}
				}
				return res;
			} else {
				field = document.getElementById('input_' + input_number);
				match = re.test(field.value);

				if ( (match && !inverse) || (!match && inverse) ) {
					return true;
				} else {
					infobox = document.getElementById('info_' + input_number);
					infobox.innerHTML += " " + message;
					return false;
				}
			}
		}
JAVASCRIPT;

			$wgOut->addInlineScript( $jstext );
		}
	}


	/**
	 * Definition of input type "regexp"
	 *
	 * Returns an array containing two elements: the html text to be included
	 * and an empty string (the js code is written directly without piping it
	 * through SF)
	 *
	 * @return array of two strings
	*/
	static function regexpHTML ( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {

		global $wgRequest, $wgUser, $wgParser;
		global $sfgTabIndex; // used to represent the current tab index in the form
		global $sfgFieldNum; // used for setting various HTML IDs
		global $sfgJSValidationCalls; // array of Javascript calls to determine if page can be saved
		global $sfgFormPrinter;

		self::regexpSetup();

		// set base type string
		if ( array_key_exists( 'base type', $other_args ) ) {
			$baseType = trim( $other_args['base type'] );
			unset( $other_args['base type'] );
		}
		else $baseType = null;

		if ( ! $baseType || ! array_key_exists( $baseType, $sfgFormPrinter->mInputTypeHooks ) )
			$baseType = 'text';

		// set base prefix string
		if ( array_key_exists( 'base prefix', $other_args ) ) {
			$basePrefix = trim( $other_args['base prefix'] ) . ".";
			unset( $other_args['base prefix'] );
		}
		else $basePrefix = '';

		// set OR character
		if ( array_key_exists( 'or char', $other_args ) ) {
			$orChar = trim( $other_args['or char'] );
			unset( $other_args['or char'] );
		}
		else $orChar = '!';

		// set inverse string
		if ( array_key_exists( 'inverse', $other_args ) ) {
			$inverseString = 'true';
			unset( $other_args['inverse'] );
		}
		else $inverseString = 'false';

		// set regexp string
		if ( array_key_exists( 'regexp', $other_args ) ) {

			$regexp = str_replace( $orChar, '|', trim( $other_args['regexp'] ) );
			unset( $other_args['regexp'] );

			// check for leading/trailing delimiter and remove it (else dump regexp)
			if ( preg_match  ( "/^\/.*\/\$/", $regexp ) ) {

				$regexp = substr( $regexp, 1, strlen( $regexp ) - 2 );

			}
			else $regexp = '.*';

		}
		else $regexp = '.*';

		// set failure message string
		if ( array_key_exists( 'message', $other_args ) ) {
			$message = trim( $other_args['message'] );
			unset( $other_args['message'] );
		}
		else $message = wfMsg( 'semanticformsinputs-wrongformat' );

		$new_other_args = array();

		foreach ( $other_args as $key => $value )
			if ( $basePrefix && strpos( $key, $basePrefix ) === 0 ) {
				$new_other_args[substr( $key, strlen( $basePrefix ) )] = $value;
			} else
				$new_other_args[$key] = $value;

		$funcArgs = array();
		$funcArgs[] = $cur_value;
		$funcArgs[] = $input_name;
		$funcArgs[] = $is_mandatory;
		$funcArgs[] = $is_disabled;
		$funcArgs[] = $new_other_args;

		$hook_values = $sfgFormPrinter->mInputTypeHooks[$baseType];

		// sanitize error message and regexp for JS
		$message = Xml::encodeJsVar( $message );
		$regexp = Xml::encodeJsVar( $regexp );

		// $sfgJSValidationCalls are sanitized for HTML by SF before output, no htmlspecialchars() here
		if ( array_key_exists( 'part_of_multiple', $other_args ) && $other_args['part_of_multiple'] == 1 ) {
			$sfgJSValidationCalls[] = "validate_input_with_regexp($sfgFieldNum, {$regexp}, {$inverseString}, {$message}, true)";
		} else {
			$sfgJSValidationCalls[] = "validate_input_with_regexp($sfgFieldNum, {$regexp}, {$inverseString}, {$message}, false)";
		}

		list( $htmltext, $jstext ) = call_user_func_array( $hook_values[0], $funcArgs );

		$wgOut->addScript( '<script type="text/javascript">' . $jstext . '</script>' );

		return array( $htmltext, "" );

	}


	/**
	 * Setup for input type jqdatepicker.
	 * Adds the Javascript code used by all date pickers.
	*/
	static private function jqDatePickerSetup () {
		global $wgOut, $wgLang, $sfgScriptPath;
		global $sfigSettings;

		static $hasRun = false;

		if ( !$hasRun ) {
			$hasRun = true;

			$wgOut->addScript( '<script type="text/javascript" src="' . $sfgScriptPath . '/libs/jquery-ui/jquery.ui.datepicker.min.js"></script> ' );
			$wgOut->addScript( '<script type="text/javascript" src="' . $sfigSettings->scriptPath . '/libs/datepicker.js"></script> ' );

			$jstext =
					"jQuery(function($){\n"
					. "	$.datepicker.regional['wiki'] = {\n"
					. "		closeText: '" . wfMsg( 'semanticformsinputs-close' ) . "',\n"
					. "		prevText: '" . wfMsg( 'semanticformsinputs-prev' ) . "',\n"
					. "		nextText: '" . wfMsg( 'semanticformsinputs-next' ) . "',\n"
					. "		currentText: '" . wfMsg( 'semanticformsinputs-today' ) . "',\n"
					. "		monthNames: ['"
						. wfMsg( 'january' ) . "','"
						. wfMsg( 'february' ) . "','"
						. wfMsg( 'march' ) . "','"
						. wfMsg( 'april' ) . "','"
						. wfMsg( 'may_long' ) . "','"
						. wfMsg( 'june' ) . "','"
						. wfMsg( 'july' ) . "','"
						. wfMsg( 'august' ) . "','"
						. wfMsg( 'september' ) . "','"
						. wfMsg( 'october' ) . "','"
						. wfMsg( 'november' ) . "','"
						. wfMsg( 'december' ) . "'],\n"
					. "		monthNamesShort: ['"
						. wfMsg( 'jan' ) . "','"
						. wfMsg( 'feb' ) . "','"
						. wfMsg( 'mar' ) . "','"
						. wfMsg( 'apr' ) . "','"
						. wfMsg( 'may' ) . "','"
						. wfMsg( 'jun' ) . "','"
						. wfMsg( 'jul' ) . "','"
						. wfMsg( 'aug' ) . "','"
						. wfMsg( 'sep' ) . "','"
						. wfMsg( 'oct' ) . "','"
						. wfMsg( 'nov' ) . "','"
						. wfMsg( 'dec' ) . "'],\n"
					. "		dayNames: ['"
						. wfMsg( 'sunday' ) . "','"
						. wfMsg( 'monday' ) . "','"
						. wfMsg( 'tuesday' ) . "','"
						. wfMsg( 'wednesday' ) . "','"
						. wfMsg( 'thursday' ) . "','"
						. wfMsg( 'friday' ) . "','"
						. wfMsg( 'saturday' ) . "'],\n"
					. "		dayNamesShort: ['"
						. wfMsg( 'sun' ) . "','"
						. wfMsg( 'mon' ) . "','"
						. wfMsg( 'tue' ) . "','"
						. wfMsg( 'wed' ) . "','"
						. wfMsg( 'thu' ) . "','"
						. wfMsg( 'fri' ) . "','"
						. wfMsg( 'sat' ) . "'],\n"
					. "		dayNamesMin: ['"
						. $wgLang->firstChar( wfMsg( 'sun' ) ) . "','"
						. $wgLang->firstChar( wfMsg( 'mon' ) ) . "','"
						. $wgLang->firstChar( wfMsg( 'tue' ) ) . "','"
						. $wgLang->firstChar( wfMsg( 'wed' ) ) . "','"
						. $wgLang->firstChar( wfMsg( 'thu' ) ) . "','"
						. $wgLang->firstChar( wfMsg( 'fri' ) ) . "','"
						. $wgLang->firstChar( wfMsg( 'sat' ) ) . "'],\n"
					. "		weekHeader: '',\n"
					. "		dateFormat: '" . wfMsg( 'semanticformsinputs-dateformat' ) . "',\n"
					. "		firstDay: '" . wfMsg( 'semanticformsinputs-firstday' ) . "',\n"
					. "		isRTL: " . ( $wgLang->isRTL() ? "true":"false" ) . ",\n"
					. "		showMonthAfterYear: false,\n"
					. "		yearSuffix: ''};\n"
					. "	$.datepicker.setDefaults($.datepicker.regional['wiki']);\n"
					. "});\n";


			$wgOut->addInlineScript( $jstext );

		}
	}

	/**
	 * expects a two dimensional array
	 * the inner arrays must contain two dates representing the start and end
	 * date of a time range
	 *
	 * returns an array with the same structur with the date ranges sorted and
	 * overlapping ranges merged
	 *
	 *
	 * @param array of arrays of DateTimes
	 * @return array of arrays of DateTimes
	*/
	static private function sortAndMergeRanges ( $ranges ) {

		// sort ranges, earliest date first
		sort( $ranges );

		$currmin = FALSE;
		$nextmin = FALSE;

		$mergedRanges = array();

		foreach ( $ranges as $range ) {

			if ( !$range ) continue;

			if ( !$currmin ) { // found first valid range

				$currmin = $range[0];
				$nextmin = $range[1];
				$nextmin->modify( '+1 day' );

			} elseif ( $range[0] <=  $nextmin ) { // overlap detected

				$currmin = min( $currmin, $range[0] );

				$range[1]->modify( '+1 day' );
				$nextmin = max( $nextmin, $range[1] );

			} else { // no overlap

				$nextmin->modify( '-1 day' );
				$mergedRanges[] = array( $currmin, $nextmin );

				$currmin = $range[0];
				$nextmin = $range[1];
				$nextmin->modify( '+1 day' );

			}

		}

		// store last range
		if ( $currmin ) {
			$nextmin->modify( '-1 day' );
			$mergedRanges[] = array( $currmin, $nextmin );
		}

		return $mergedRanges;

	}

	/**
	 * expects a comma-separated list of dates or date ranges in the format
	 * "yyyy/mm/dd" or "yyyy/mm/dd-yyyy/mm/dd"
	 *
	 * returns an array of arrays, each of the latter consisting of two dates
	 * representing the start and end date of the range
	 *
	 * @param string
	 * @return array of arrays of DateTimes
	*/
	static private function createRangesArray ( $rangesAsStrings ) {

		// transform array of strings into array of array of dates
		return array_map(
				function( $range ) {

					if ( strpos ( $range, '-' ) === FALSE ) { // single date
						$date = date_create( $range );
						return ( $date ) ? array( $date, clone $date ):null;
					} else { // date range
						$dates = array_map( "date_create", explode( '-', $range ) );
						return  ( $dates[0] && $dates[1] ) ? $dates:null;
					}

				} ,
				$rangesAsStrings
		);

	}

	/**
	 * Takes an array of date ranges and returns an array containing the gaps
	 * between the ranges of the input array.
	 *
	 * @param array of arrays of DateTimes
	 * @return array of arrays of DateTimes
	*/
	static private function invertRangesArray( $ranges ) {

		$invRanges = null;
		$min = null;

		foreach ( $ranges as $range ) {

			if ( $min ) {
				$min->modify( "+1day " );
				$range[0]->modify( "-1day " );
				$invRanges[] = array( $min, $range[0] );
			}

			$min = $range[1];

		}

		return $invRanges;
	}


	/**
	 * Definition of input type "datepicker".
	 *
	 * Returns an array containing two elements: the html text to be included
	 * and an empty string (the js code is written directly without piping it
	 * through SF)
	 *
	 * @return array of two strings
	*/
	static function jqDatePickerHTML( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {

		global $wgOut, $wgLang, $wgAmericanDates;
		global $sfgFieldNum, $sfgScriptPath, $sfigSettings;
		global $sfgTabIndex; // used to represent the current tab index in the form


		// call common setup for all jqdatepickers
		self::jqDatePickerSetup();

		// first: set up HTML attributes

		// array of attributes to pass to the input field
		$attribs = array(
				"class" => "createboxInput",
				"value" => $cur_value,
				"type" => "text"
		);


		// set size attrib
		if ( array_key_exists( 'size', $other_args ) ) $attribs['size'] = $other_args['size'];

		// set maxlength attrib
		if ( array_key_exists( 'maxlength', $other_args ) ) $attribs['maxlength'] = $other_args['maxlength'];


		// modify class attribute for mandatory form fields
		if ( $is_mandatory ) $attribs["class"] .= ' mandatory';

		// add user class(es) to class attribute of input field and to all other datepicker components
		if ( array_key_exists( 'class', $other_args ) ) {
			$attribs["class"] .= ' ' . $other_args['class'];
			$userClasses = $other_args['class'];
		}

		// set readonly attrib
		if ( array_key_exists( 'disable input field', $other_args )
				|| ( !array_key_exists( 'enable input field', $other_args ) && $sfigSettings->datePickerDisableInputField )
				|| $is_disabled	) {

			$attribs["readonly"] = "1";

		}

		// second: set up JS attributes, but only if we need them
		if ( !$is_disabled ) {

			// find min date, max date and disabled dates

			// set first date
			if ( array_key_exists( 'first date', $other_args ) ) $minDate = date_create( $other_args['first date'] );
			elseif ( $sfigSettings->datePickerFirstDate ) $minDate = date_create( $sfigSettings->datePickerFirstDate );
			else $minDate = null;



			// set last date
			if ( array_key_exists( 'last date', $other_args ) ) $maxDate = date_create( $other_args['last date'] );
			elseif ( $sfigSettings->datePickerLastDate ) $maxDate = date_create( $sfigSettings->datePickerLastDate );
			else $maxDate = null;

			// $disabledDates = null;

			// find possible values and invert them to get disabled values
			if ( array_key_exists( 'possible_values', $other_args ) && count( $other_args['possible_values'] ) ) {

				$enabledDates = self::sortAndMergeRanges( self::createRangesArray( $other_args['possible_values'] ) );

				// correct min/max date to the first/last allowed value
				if ( !$minDate || $minDate < $enabledDates[0][0] ) $minDate = $enabledDates[0][0];
				if ( !$maxDate || $maxDate > $enabledDates[count( $enabledDates ) - 1][1] ) $maxDate = $enabledDates[count( $enabledDates ) - 1][1];

				$disabledDates = self::invertRangesArray( $enabledDates );

			} else $disabledDates = array();

			// add user-defined disabled values
			if ( array_key_exists( 'disable dates', $other_args ) ) {

				$disabledDates =
						self::sortAndMergeRanges(
						array_merge( $disabledDates, self::createRangesArray( explode( ',' , $other_args['disable dates'] ) ) ) );

			} elseif ( $sfigSettings->datePickerDisabledDates ) {

				$disabledDates =
						self::sortAndMergeRanges(
						array_merge( $disabledDates, self::createRangesArray( explode( ',' , $sfigSettings->datePickerDisabledDates ) ) ) );

			}

			if ( $minDate ) {
				// discard all disabled dates below the min date
				while ( $minDate && count( $disabledDates ) && $disabledDates[0][1] < $minDate ) array_shift( $disabledDates );

				// if min date is in first disabled date range, discard that range and adjust min date
				if ( count( $disabledDates ) && $disabledDates[0][0] <= $minDate && $disabledDates[0][1] >= $minDate ) {
					$minDate = $disabledDates[0][1];
					array_shift( $disabledDates );
					$minDate->modify( "+1 day" );
				}
			}

			if ( $maxDate ) {
				// discard all disabled dates above the max date
				while ( count( $disabledDates ) && $disabledDates[count( $disabledDates ) - 1][0] > $maxDate ) array_pop( $disabledDates );

				// if max date is in last disabled date range, discard that range and adjust max date
				if ( count( $disabledDates ) && $disabledDates[count( $disabledDates ) - 1][0] <= $maxDate && $disabledDates[count( $disabledDates ) - 1][1] >= $maxDate ) {
					$maxDate = $disabledDates[count( $disabledDates ) - 1][0];
					array_pop( $disabledDates );
					$maxDate->modify( "-1 day" );
				}
			}
			// finished with disabled dates

			// find highlighted dates
			if ( array_key_exists( "highlight dates", $other_args ) ) {
				$highlightedDates = self::sortAndMergeRanges ( self::createRangesArray( explode( ',' , $other_args["highlight dates"] ) ) ) ;
			} else if ( $sfigSettings->datePickerHighlightedDates ) {
				$highlightedDates = self::sortAndMergeRanges ( self::createRangesArray( explode( ',' , $sfigSettings->datePickerHighlightedDates  ) ) ) ;
			} else {
				$highlightedDates = null;
			}


			// find disabled week days and mark them in an array
			if ( array_key_exists( "disable days of week", $other_args ) ) $disabledDaysString = $other_args['disable days of week'];
			else $disabledDaysString = $sfigSettings->datePickerDisabledDaysOfWeek;

			if ( $disabledDaysString != null ) {

				$disabledDays = array( false, false, false, false, false, false, false );

				foreach ( explode( ',', $disabledDaysString ) as $day ) {

					if ( is_numeric( $day ) && $day >= 0 && $day <= 6 ) {
						$disabledDays[$day] = true;
					}

				}

			} else {
				$disabledDays = null;
			}

			// find highlighted week days and mark them in an array
			if ( array_key_exists( "highlight days of week", $other_args ) ) $highlightedDaysString = $other_args['highlight days of week'];
			else $highlightedDaysString = $sfigSettings->datePickerHighlightedDaysOfWeek;


			if ( $highlightedDaysString != null ) {

				$highlightedDays = array( false, false, false, false, false, false, false );

				foreach ( explode( ',', $highlightedDaysString ) as $day ) {

					if ( is_numeric( $day ) && $day >= 0 && $day <= 6 ) {
						$highlightedDays[$day] = true;
					}

				}
			} else {
				$highlightedDays = null;
			}

			// set datepicker widget attributes
			$jsattribs = array(
					'showOn' => 'both',
					'buttonImage' => $sfigSettings->scriptPath . '/DatePickerButton.gif',
					'buttonImageOnly' => false,
					'changeMonth' => true,
					'changeYear' => true,
					'altField' => "#input_{$sfgFieldNum}",
					'altFormat' => "yy/mm/dd",
					// Today button does not work (http://dev.jqueryui.com/ticket/4045)
					// do not show button panel for now
					// TODO: show date picker button panel when bug is fixed
					'showButtonPanel' => false
			);

			// set first day of the week
			if ( array_key_exists( 'week start', $other_args ) ) $jsattribs['firstDay'] = $other_args['week start'];
			else if ( $sfigSettings->datePickerWeekStart != null ) $jsattribs['firstDay'] = $sfigSettings->datePickerWeekStart;
			else $jsattribs['firstDay'] = wfMsg( 'semanticformsinputs-firstdayofweek' );

			// set show week number
			if ( array_key_exists( 'show week numbers', $other_args )
					|| ( !array_key_exists( 'hide week numbers', $other_args ) && $sfigSettings->datePickerShowWeekNumbers ) ) {

				$jsattribs['showWeek'] = true;

			}

			// set date format
			if ( $wgAmericanDates && $wgLang->getCode() == "en" ) {

				if ( array_key_exists( 'date format', $other_args ) ) {

					if ( $other_args['date format'] == 'SHORT' ) $jsattribs['dateFormat'] = 'mm/dd/yy';
					elseif ( $other_args['date format'] == 'LONG' ) $jsattribs['dateFormat'] = 'MM d, yy';
					else $jsattribs['dateFormat'] = $other_args['date format'];

				} elseif ( $sfigSettings->datePickerDateFormat ) {

					if ( $sfigSettings->datePickerDateFormat == 'SHORT' ) $jsattribs['dateFormat'] = 'mm/dd/yy';
					elseif ( $sfigSettings->datePickerDateFormat == 'LONG' ) $jsattribs['dateFormat'] = 'MM d, yy';
					else $jsattribs['dateFormat'] = $sfigSettings->datePickerDateFormat;

				} else $jsattribs['dateFormat'] = 'yy/mm/dd';

			} else {

				if ( array_key_exists( 'date format', $other_args ) ) {

					if ( $other_args['date format'] == 'SHORT' ) $jsattribs['dateFormat'] = wfMsg( 'semanticformsinputs-dateformatshort' );
					elseif ( $other_args['date format'] == 'LONG' ) $jsattribs['dateFormat'] = wfMsg( 'semanticformsinputs-dateformatlong' );
					else $jsattribs['dateFormat'] = $other_args['date format'];

				} elseif ( $sfigSettings->datePickerDateFormat ) {

					if ( $sfigSettings->datePickerDateFormat == 'SHORT' ) $jsattribs['dateFormat'] = wfMsg( 'semanticformsinputs-dateformatshort' );
					elseif ( $sfigSettings->datePickerDateFormat == 'LONG' ) $jsattribs['dateFormat'] = wfMsg( 'semanticformsinputs-dateformatlong' );
					else $jsattribs['dateFormat'] = $sfigSettings->datePickerDateFormat;

				} else $jsattribs['dateFormat'] = 'yy/mm/dd';

			}

		}


		// third: build HTML and JS code


		if ( $is_disabled ) {

			$attribs[ 'id' ] = "input_{$sfgFieldNum}";
			$attribs[ 'name' ] = $input_name;

			// no JS needed on a disabled datepicker, but we need to append the disabled button ourselves
			$html = Html::element( "input", $attribs )
					. Html::rawElement( "button",
					array(
					'type' => 'button',
					'class' => $userClasses,
					'disabled' => '1',
					'id' => "input_{$sfgFieldNum}_button"
					),
					Html::element( "image",
					array( 'src' => $sfigSettings->scriptPath . '/DatePickerButtonDisabled.gif'	)

					)
			);

			// append reset button if required
			if ( array_key_exists( 'show reset button', $other_args ) ||
					$sfigSettings->datePickerShowResetButton && !array_key_exists( 'hide reset button', $other_args ) ) {

				$html .= Html::rawElement( "button",
						array(
						'type' => 'button',
						'class' => $userClasses,
						'disabled' => '1',
						'id' => "input_{$sfgFieldNum}_resetbutton"
						),
						Html::element( "image",
						array( 'src' => $sfigSettings->scriptPath . '/DatePickerResetButtonDisabled.gif' )
						)
				);

			}

			// no JS needed on a disabled datepicker
			$jstext = '';

		} else {

			$attribs[ 'id' ] = "input_{$sfgFieldNum}_show";
			$attribs[ 'tabindex' ] = $sfgTabIndex;

			// start with the displayed input and
			// append the real, but hidden input that gets sent to SF;
			// it will be filled by the datepicker
			$html = Html::element( "input", $attribs )
					. Html::element( "input",
					array(
					"id" => "input_{$sfgFieldNum}",
					"name" => $input_name,
					"type" => "hidden"
					)
			);

			// append reset button if required
			if ( array_key_exists( 'show reset button', $other_args ) ||
					$sfigSettings->datePickerShowResetButton && !array_key_exists( 'hide reset button', $other_args ) ) {

				$html .= "<button "
						. Html::expandAttributes ( array(
						'type' => 'button',
						'class' => $userClasses,
						'id' => "input_{$sfgFieldNum}_resetbutton",
						) )
						. "onclick= \"document.getElementById('input_{$sfgFieldNum}').value='';document.getElementById('input_{$sfgFieldNum}_show').value='';\""
						. ">"
						. Html::element( "image", array( 'src' => $sfigSettings->scriptPath . '/DatePickerResetButton.gif' ) )
						. "</button>";

			}

			$cur_value = Xml::escapeJsString( $cur_value );


			// build JS array
			$jsattribsString = Xml::encodeJsVar( $jsattribs );

			// attach datepicker to input field
			$jstext = <<<JAVASCRIPT
		jQuery (
			function() {
				jQuery("#input_{$sfgFieldNum}_show").datepicker( $jsattribsString );
				jQuery("#input_{$sfgFieldNum}_show").datepicker( "setDate", jQuery.datepicker.parseDate("yy/mm/dd", "$cur_value", null) );

JAVASCRIPT;

			// set first date
			if ( $minDate ) {

				$minDateString = $minDate->format( 'Y-m-d' );
				$jstext .= <<<JAVASCRIPT
				jQuery("#input_{$sfgFieldNum}_show").datepicker( "option", "minDate", jQuery.datepicker.parseDate("yy/mm/dd", "$minDateString", null) );

JAVASCRIPT;
			}

			// set last date
			if ( $maxDate ) {

				$maxDateString = $maxDate->format( 'Y-m-d' );

				$jstext .= <<<JAVASCRIPT
				jQuery("#input_{$sfgFieldNum}_show").datepicker( "option", "maxDate", jQuery.datepicker.parseDate("yy/mm/dd", "$maxDateString", null) );

JAVASCRIPT;
			}


			// add user-defined class(es) to all datepicker components
			if ( array_key_exists( 'class', $other_args ) ) {

				$userClasses = Xml::encodeJsVar ( $userClasses );

				$jstext .= <<<JAVASCRIPT
				jQuery("#input_{$sfgFieldNum}_show").datepicker("widget").addClass({$userClasses});
				jQuery("#input_{$sfgFieldNum}_show + button").addClass({$userClasses});

JAVASCRIPT;

			}

			// register disabled dates
			// attach event handler to handle disabled dates
			if ( count( $disabledDates ) || count( $highlightedDates ) || count( $disabledDays ) || count( $highlightedDays ) ) {

				// register disabled dates with datepicker
				if ( count( $disabledDates ) ) {

					$disabledDatesString = '[' . implode( ',', array_map( function ( $range ) {

								$y0 = $range[0]->format( "Y" );
								$m0 = $range[0]->format( "m" ) - 1;
								$d0 = $range[0]->format( "d" );

								$y1 = $range[1]->format( "Y" );
								$m1 = $range[1]->format( "m" ) - 1;
								$d1 = $range[1]->format( "d" );

								return "[new Date({$y0}, {$m0}, {$d0}), new Date({$y1}, {$m1}, {$d1})]";
							} , $disabledDates ) ) . ']';

					$jstext .= "				jQuery(\"#input_{$sfgFieldNum}_show\").datepicker(\"option\", \"disabledDates\", $disabledDatesString);\n";

				}

				// register highlighted dates with datepicker
				if ( count( $highlightedDates ) ) {

					$highlightedDatesString = '[' . implode( ',', array_map( function ( $range ) {

								$y0 = $range[0]->format( "Y" );
								$m0 = $range[0]->format( "m" ) - 1;
								$d0 = $range[0]->format( "d" );

								$y1 = $range[1]->format( "Y" );
								$m1 = $range[1]->format( "m" ) - 1;
								$d1 = $range[1]->format( "d" );

								return "[new Date({$y0}, {$m0}, {$d0}), new Date({$y1}, {$m1}, {$d1})]";
							} , $highlightedDates ) ) . ']';

					$jstext .= "				jQuery(\"#input_{$sfgFieldNum}_show\").datepicker(\"option\", \"highlightedDates\", $highlightedDatesString);\n";

				}

				// register disabled days of week with datepicker
				if ( count( $disabledDays ) ) {
					$disabledDaysString = Xml::encodeJsVar( $disabledDays );
					$jstext .= "				jQuery(\"#input_{$sfgFieldNum}_show\").datepicker(\"option\", \"disabledDays\", $disabledDaysString);\n";
				}

				// register highlighted days of week with datepicker
				if ( count( $highlightedDays ) ) {
					$highlightedDaysString = Xml::encodeJsVar( $highlightedDays );
					$jstext .= "				jQuery(\"#input_{$sfgFieldNum}_show\").datepicker(\"option\", \"highlightedDays\", $highlightedDaysString);\n";
				}

				$jstext .= <<<JAVASCRIPT

				jQuery("#input_{$sfgFieldNum}_show").datepicker("option", "beforeShowDay", function (date) {return SFI_DP_checkDate(this, date);});

JAVASCRIPT;
			}

			// close JS code fragment
			$jstext .= <<<JAVASCRIPT
			}
		);

JAVASCRIPT;


		}

		// add span for error messages (e.g. used for mandatory inputs)
		$html .= Html::element( "span", array( "id" => "info_$sfgFieldNum", "class" => "errorMessage" ) );

		$wgOut->addScript( '<script type="text/javascript">' . $jstext . '</script>' );

		return array( $html, "" );

	}
}
