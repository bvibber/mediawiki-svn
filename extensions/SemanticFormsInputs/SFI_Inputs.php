<?php
/**
 * Input definitions for the Semantic Forms Inputs extension.
 *
 * @author Stephan Gambke
 * @author Sanyam Goyal
 * @author Yaron Koren
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point.' );
}

class SFIInputs {
/*
 * Setup for input type regexp.
 * Adds the Javascript code used by all regexp filters.
 */
function regexpSetup() {

	global $wgOut;

	static $hasRun = false;

	if ( !$hasRun ) {
		$hasRun = true;

		wfLoadExtensionMessages( 'SemanticFormsInputs' );

		$jstext = <<<END
	function validate_input_with_regexp(input_number, re, inverse, message, multiple){

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
END;

		$wgOut->addInlineScript( $jstext );
	}
}


/*
 * Definition of input type "regexp"
 */
function regexpHTML ( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {

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
	}
	else $regexp = '/.*/';

	// set regexp string
	if ( array_key_exists( 'message', $other_args ) ) {
		$message = trim( $other_args['message'] );
		unset( $other_args['message'] );
	}
	else $message = wfMsg( 'semanticformsinputs-wrongformat' );

	$message = str_replace( '\\', '\\\\', $message );
	$message = str_replace( '\'', '\\\'', $message );

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

	if ( array_key_exists( 'part_of_multiple', $other_args ) && $other_args['part_of_multiple'] == 1 ) {
		$sfgJSValidationCalls[] = "validate_input_with_regexp($sfgFieldNum, {$regexp}, {$inverseString}, '$message', true)";
	} else {
		$sfgJSValidationCalls[] = "validate_input_with_regexp($sfgFieldNum, {$regexp}, {$inverseString}, '$message', false)";
	}

	list( $htmltext, $jstext ) = call_user_func_array( $hook_values[0], $funcArgs );

	return array( $htmltext, $jstext );
}


/*
 * Setup for input type datepicker.
 * Adds the Javascript code used by all date pickers.
 */
function datePickerSetup () {
	global $wgOut;
	global $sfigSettings;

	static $hasRun = false;

	if ( !$hasRun ) {
		$hasRun = true;

		$wgOut->addLink( array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'media' => "screen",
			'href' => $sfigSettings->yuiBase . "calendar/assets/skins/sam/calendar.css"
		) );
		$wgOut->addLink( array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'media' => "screen",
			'href' => $sfigSettings->yuiBase . "calendar/assets/skins/sam/calendar-skin.css"
		) );

 		$wgOut->addScript( '<script type="text/javascript" src="' . $sfigSettings->yuiBase . 'yahoo-dom-event/yahoo-dom-event.js"></script> ' );
 		$wgOut->addScript( '<script type="text/javascript" src="' . $sfigSettings->yuiBase . 'calendar/calendar-min.js"></script> ' );
 		$wgOut->addScript( '<script type="text/javascript" src="' . $sfigSettings->yuiBase . 'datasource/datasource-min.js"></script> ' );

		$locString = "function datePickerSetLocale() {\n" .
			'YAHOO.util.DateLocale["wiki"] = YAHOO.lang.merge(YAHOO.util.DateLocale, {' . "\n" .
			'a: ["' . wfMsg( 'sun' ) . '", "' .
			wfMsg( 'mon' ) . '", "' . wfMsg( 'tue' ) . '", "' . wfMsg( 'wed' ) . '", "' .
			wfMsg( 'thu' ) . '", "' . wfMsg( 'fri' ) . '", "' . wfMsg( 'sat' ) . '"],' . "\n" .
			'A: ["' . wfMsg( 'sunday' ) . ' ", "' .
			wfMsg( 'monday' )   . '", "' . wfMsg( 'tuesday' ) . '", "' . wfMsg( 'wednesday' ) . '", "' .
			wfMsg( 'thursday' ) . '", "' . wfMsg( 'friday' )  . '", "' . wfMsg( 'saturday' )  . '"],' . "\n" .
			'b: ["' .
			wfMsg( 'jan' ) . '", "' . wfMsg( 'feb' ) .	'", "' . wfMsg( 'mar' ) .	'", "' . wfMsg( 'apr' ) . '", "' .
			wfMsg( 'may' ) . '", "' . wfMsg( 'jun' ) .	'", "' . wfMsg( 'jul' ) .	'", "' . wfMsg( 'aug' ) . '", "' .
			wfMsg( 'sep' ) . '", "' . wfMsg( 'oct' ) .	'", "' . wfMsg( 'nov' ) .	'", "' . wfMsg( 'dec' ) . '"],' . "\n" .
			'B: ["' .
			wfMsg( 'january' ) . '", "' . wfMsg( 'february' ) . '", "' . wfMsg( 'march' )     . '", "' .
			wfMsg( 'april' )   . '", "' . wfMsg( 'may-long' ) . '", "' . wfMsg( 'june' )      . '", "' .
			wfMsg( 'july' )    . '", "' . wfMsg( 'august' )   . '", "' . wfMsg( 'september' ) . '", "' .
			wfMsg( 'october' ) . '", "' . wfMsg( 'november' ) . '", "' . wfMsg( 'december' )  . '"]' . "\n" .

			"});\n" .

			'sfiElements["locale"] = new Object;' . "\n" .

			'sfiElements["locale"].month_long = YAHOO.util.DateLocale["wiki"].B' . "\n" .
			'sfiElements["locale"].month_short = YAHOO.util.DateLocale["wiki"].b' . "\n" .
			'sfiElements["locale"].week_long = YAHOO.util.DateLocale["wiki"].A' . "\n" .
			'sfiElements["locale"].week_medium = YAHOO.util.DateLocale["wiki"].a' . "\n" .
			'sfiElements["locale"].week_short = new Array(YAHOO.util.DateLocale["wiki"].a.length);' . "\n" .
			'sfiElements["locale"].week_1char = new Array(YAHOO.util.DateLocale["wiki"].a.length);' . "\n" .
			'for (i=0; i < YAHOO.util.DateLocale["wiki"].a.length;++i) {' . "\n" .
			' sfiElements["locale"].week_short[i] = YAHOO.util.DateLocale["wiki"].a[i].substr(0,2);' . "\n" .
			' sfiElements["locale"].week_1char[i] = YAHOO.util.DateLocale["wiki"].a[i].substr(0,1);' . "\n" .

			"}\n" .
			"}\n" .
			"addOnloadHook(datePickerSetLocale);";

		$jstext = <<<END
	function toggle_datepicker(toggle_button) {

		var id = toggle_button.id.replace("_button","");

		if (sfiElements[id]) {
			if (document.getElementById(id + "_calendar").style.display=="none")
				sfiElements[id].show();
			else sfiElements[id].hide();
		} else { //setup datepicker first
			var settings = id.replace(/input(_[0-9]+)?(_[0-9]+)/, "settings$2");
			//alert(settings);

			sfiElements[id] = new YAHOO.widget.Calendar(
				id + "_table",
				id + "_calendar",
				{
					navigator:true,
					START_WEEKDAY:sfiElements[settings].start_weekday,
					SHOW_WEEK_HEADER:sfiElements[settings].show_week_header,
					mindate:sfiElements[settings].first_day,
					maxdate:sfiElements[settings].last_day,
					LOCALE_MONTHS:sfiElements[settings].locale_months,
					LOCALE_WEEKDAYS:sfiElements[settings].locale_weekdays,
					MONTHS_LONG:sfiElements["locale"].month_long,
					MONTHS_SHORT:sfiElements["locale"].month_short,
					WEEKDAYS_LONG:sfiElements["locale"].week_long,
					WEEKDAYS_MEDIUM:sfiElements["locale"].week_medium,
					WEEKDAYS_SHORT:sfiElements["locale"].week_short,
					WEEKDAYS_1CHAR:sfiElements["locale"].week_1char
				});

			for (i = 0; i < sfiElements[settings].disabled_days_of_week.length; ++i)
				sfiElements[id].addWeekdayRenderer(
				   sfiElements[settings].disabled_days_of_week[i] + 1,
				   sfiElements[id].renderOutOfBoundsDate
				);

			for (i = 0; i < sfiElements[settings].highlighted_days_of_week.length; ++i)
				sfiElements[id].addWeekdayRenderer(
				   sfiElements[settings].highlighted_days_of_week[i] + 1,
				   sfiElements[id].renderCellStyleHighlight1
				);

			for (i = 0; i < sfiElements[settings].disabled_days.length; ++i)
				sfiElements[id].addRenderer(
					sfiElements[settings].disabled_days[i],
					sfiElements[id].renderOutOfBoundsDate
				);

			for (i = 0; i < sfiElements[settings].highlighted_days.length; ++i)
				sfiElements[id].addRenderer(
					sfiElements[settings].highlighted_days[i],
					sfiElements[id].renderCellStyleHighlight2
				);

			//if (sfiElements[settings].default_day) sfiElements[id].select(sfiElements[settings].default_day);

			sfiElements[id].selectEvent.subscribe(
				  function(type,arr,obj){
					  var id = this.id.replace("_table","");
					  var settings = id.replace(/input(_[0-9]+)?(_[0-9]+)/, "settings$2");
					  document.getElementById(id).value=
					  YAHOO.util.Date.format(
																 this.toDate(arr[0][0]),
																 {format:sfiElements[settings].date_format},
																 'wiki'
															 );
					 this.hide();
				  },
				  sfiElements[id],
				  true
			);


			//YAHOO.util.DOM.setXY(YAHOO.util.Dom.getX(id),YAHOO.util.Dom.getX(id));

			YAHOO.util.Event.addListener(document.getElementsByTagName("body")[0], "click", function(e, id){
				if (YAHOO.util.Event.getTarget(e).id != id + "_button")
					sfiElements[id].hide();
			}, id, false);

			YAHOO.util.Event.addListener(id + "_container", "click", function(e){YAHOO.util.Event.stopEvent(e);});

			//sfiElements[id].select(sfiElements[settings].default_day);
			sfiElements[id].render();
			sfiElements[id].show();

		}

		return false;
	}

	function reset_datepicker(toggle_button) {
		var id = toggle_button.id.replace("_resetbutton","");
		if (sfiElements[id]) sfiElements[id].clear();
			document.getElementById(id).value="";
		}
END;

		$jstext .= $locString;

		$wgOut->addInlineScript( $jstext );
	}
}

/*
 * Definition of input type "simpledatepicker".
 */
function jqDatePickerHTML( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {

	global $wgRequest, $wgUser, $wgParser, $wgOut, $wgScriptPath, $wgLanguageCode, $sfgFieldNum, $sfgScriptPath;

	static $hasRun = false;

	if ( !$hasRun ) {
		$hasRun = true;

		$wgOut->addScript( '<script type="text/javascript" src="' . $sfgScriptPath . '/libs/jquery-ui/jquery.ui.datepicker.min.js"></script> ' );
		if ( strcmp( $wgLanguageCode, "en" ) != 0 ) {
			$wgOut->addScript( '<script type="text/javascript" src="' . $sfgScriptPath . '/libs/jquery-ui/jquery-ui-i18n.js"></script> ' );
		}

		$jstext = "<script>  jQuery.noConflict(); jQuery(function(){";
		$image_path = $wgScriptPath . '/extensions/SemanticFormsInputs/calendar.gif';
		if ( strcmp( $wgLanguageCode, "en" ) != 0 ) {
			$jstext .= "jQuery(\"#input_" . $sfgFieldNum . "\").datepicker({showOn: 'both', buttonImage: '$image_path', buttonImageOnly: true , dateFormat: 'yy-mm-dd' }, jQuery.datepicker.regional['$wgLanguageCode']); } ); </script> ";
		} else {
			$jstext .= "jQuery(\"#input_" . $sfgFieldNum . "\").datepicker({showOn: 'both', buttonImage: '$image_path', buttonImageOnly: true , dateFormat: 'yy-mm-dd' }); 	} ); </script> ";
		}

		$wgOut->addScript( $jstext );

	}
	$html = '<p><input type="text" id="input_' . $sfgFieldNum . '"  name="' . $input_name . '"   value ="' . $cur_value . '" size="30"/></p>';

	return array( $html, "" );
}

/*
 * Definition of input type "datepicker".
 */
function datePickerHTML ( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {

	global $wgRequest, $wgUser, $wgParser, $wgOut, $wgScriptPath;

	global $sfgTabIndex; // used to represent the current tab index in the form
	global $sfgFieldNum; // used for setting various HTML IDs
	global $sfgJSValidationCalls; // array of Javascript calls to determine if page can be saved

	global $sfigSettings;

	if ( !( array_key_exists( 'hidden', $other_args ) || $is_disabled ) ) self::datePickerSetup();

	// set size string
	if ( array_key_exists( 'size', $other_args ) ) $sizeString = 'size="' . $other_args['size'] . '" ';
	else $sizeString = '';

	// set maxlength string
	if ( array_key_exists( 'maxlength', $other_args ) ) $maxlengthString = 'maxlength="' . $other_args['maxlength'] . '" ';
	else $maxlengthString = '';

	// set mandatory string
	if ( $is_mandatory ) $mandatoryString = 'mandatory ';
	else $mandatoryString = '';

	// set class string
	if ( array_key_exists( 'class', $other_args ) ) {
		$classString = $other_args['class'];
	} else {
		$classString = '';
	}

	// set input field disabled string
	if ( array_key_exists( 'disable input field', $other_args ) ) {
		$disableInputString = 'readonly ';
	} elseif ( array_key_exists( 'enable input field', $other_args ) ) {
		$disableInputString = '';
	} elseif ( $sfigSettings->datePickerDisableInputField ) {
		$disableInputString = 'readonly ';
	} else {
		$disableInputString = '';
	}

	// set week start string
	if ( array_key_exists( 'week start', $other_args ) ) {
		$weekStartString = $other_args['week start'];
	} else {
		$weekStartString = $sfigSettings->datePickerWeekStart;
	}

	// set show week number string
	if ( array_key_exists( 'show week numbers', $other_args ) ) {
		$weekNumberString = 'true';
	} elseif ( array_key_exists( 'hide week numbers', $other_args ) ) {
		$weekNumberString = 'false';
	} elseif ( $sfigSettings->datePickerShowWeekNumbers ) {
		$weekNumberString = 'true';
	} else {
		$weekNumberString = 'false';
	}

	// set disabled days of week
	if ( array_key_exists( 'disable days of week', $other_args ) && preg_match( '/^[0-6](,[0-6])*$/x', $other_args['disable days of week'] ) ) {
		$disabledDaysOfWeek = $other_args['disable days of week'];
	} else {
		$disabledDaysOfWeek = $sfigSettings->datePickerDisabledDaysOfWeek;
	}

	// set highlighted days of week
	if ( array_key_exists( 'highlight days of week', $other_args ) && preg_match( '/^[0-6](,[0-6])*$/x', $other_args['highlight days of week'] ) ) {
		$highlightedDaysOfWeek = $other_args['highlight days of week'];
	} else {
		$highlightedDaysOfWeek = $sfigSettings->datePickerHighlightedDaysOfWeek;
	}

	// set first date
	if ( array_key_exists( 'first date', $other_args ) ) {
		$firstDateInString = $other_args['first date'];
	} elseif ( $sfigSettings->datePickerFirstDate ) {
		$firstDateInString = $sfigSettings->datePickerFirstDate;
	} else {
		$firstDateInString = null;
	}

	if ( $firstDateInString ) {
		$parts = explode( '/', $firstDateInString );
		$firstDate = date_create( $parts[2] . '/' . $parts[1] . '/' . $parts[0] );
	} else $firstDate = null;

	// set last date
	if ( array_key_exists( 'last date', $other_args ) ) {
		$lastDateInString = $other_args['last date'];
	} elseif ( $sfigSettings->datePickerLastDate ) {
		$lastDateInString = $sfigSettings->datePickerLastDate;
	} else {
		$lastDateInString = null;
	}

	if ( $lastDateInString ) {
		$parts = explode( '/', $lastDateInString );
		$lastDate = date_create( $parts[2] . '/' . $parts[1] . '/' . $parts[0] );
	} else {
		$lastDate = null;
	}

	// set disabled days
	if ( array_key_exists( 'disable dates', $other_args ) ) {
		$disabledDates = $other_args['disable dates'];
	} else {
		$disabledDates = $sfigSettings->datePickerDisabledDates;
	}

	$disabledDatesString = '';

	if ( $disabledDates ) {

		foreach ( explode( ',', $disabledDates ) as $range ) {
			if ( strpos( $range, '-' ) === false ) {
				$dateArray = explode( '/', $range );
				$disabledDatesString .=  '"' .
					$dateArray[1] . '/' .
					$dateArray[0] . '/' .
					$dateArray[2] . '", ';
			} else {
				$dateArray = explode( '/', str_replace( '-', '/', $range ) );
				$disabledDatesString .= '"' .
					$dateArray[1] . '/' .
					$dateArray[0] . '/' .
					$dateArray[2] . '-' .
					$dateArray[4] . '/' .
					$dateArray[3] . '/' .
					$dateArray[5] . '", ';
			}
		}

	}

	if ( array_key_exists( 'possible_values', $other_args ) && $other_args['possible_values'] ) {

		$enabledDates = array();

		foreach ( $other_args['possible_values'] as $range ) {

			if ( strpos( $range, '-' ) === false )
				$enabledDates[] = array( date_create( str_replace( '/', '-', $range ) ), // need '-' to correctly parse dates
									  date_create( str_replace( '/', '-', $range ) ) );
			else
				$enabledDates[] = array_map( "date_create",
										  explode( ':', str_replace( '/', '-', str_replace( '-', ':', $range ) ) ) );

		}

		sort( $enabledDates );

		// adjust first year
		// if (array_key_exists('years from values', $other_args) && array_key_exists(0, $enabledDates))
		if ( count( $enabledDates ) > 0 && ( !$firstDate || $firstDate < $enabledDates[0][0] ) ) {
			$firstDate = $enabledDates[0][0];
		}

		$prevStartOfDisabled = $firstDate;

		while ( list( $currKey, $currRange ) = each( $enabledDates ) ) {

			$currEndOfDisabled = clone $enabledDates[$currKey][0];
			$currEndOfDisabled->modify( "-1 day" );

			$currStartOfDisabled = clone $enabledDates[$currKey][1];
			$currStartOfDisabled->modify( "+1 day" );

			if ( $currEndOfDisabled <= $prevStartOfDisabled ) {
				$prevStartOfDisabled = max( $currStartOfDisabled, $prevStartOfDisabled );
			} else {

				$disabledDatesString .= '"' .
					$prevStartOfDisabled->format( 'n' ) . '/' .
					$prevStartOfDisabled->format( 'j' ) . '/' .
					$prevStartOfDisabled->format( 'Y' ) . '-' .
					$currEndOfDisabled->format( 'n' ) . '/' .
					$currEndOfDisabled->format( 'j' ) . '/' .
					$currEndOfDisabled->format( 'Y' ) . '", ';

				$prevStartOfDisabled = $currStartOfDisabled;

			}
		}


		// adjust last date
		if ( !$lastDate || $lastDate > $prevStartOfDisabled ) {
			$lastDate = $prevStartOfDisabled;
		}

	}

	// set disabled dates string
	if ( $disabledDatesString ) {
		$disabledDatesString = rtrim( $disabledDatesString, ", " );
	}

	// set first date string and last date string
	if ( $firstDate ) {
		$firstDateString = '"' . $firstDate->format( 'n' ) . '/' . $firstDate->format( 'j' ) . '/' . $firstDate->format( 'Y' ) . '"';
	} else {
		$firstDateString = 'null';
	}

	if ( $lastDate ) {
		$lastDateString = '"' . $lastDate->format( 'n' ) . '/' . $lastDate->format( 'j' ) . '/' . $lastDate->format( 'Y' ) . '"';
	} else {
		$lastDateString = 'null';
	}

	// set highlighted dates
	if ( array_key_exists( 'highlight dates', $other_args ) ) {
		$highlightedDates = $other_args['highlight dates'];
	} else {
		$highlightedDates = $sfigSettings->datePickerHighlightedDates;
	}

	$highlightedDatesString = '';

	if ( $highlightedDates ) {

		foreach ( explode( ',', $highlightedDates ) as $range ) {
			if ( strpos( $range, '-' ) === false ) {
				$dateArray = explode( '/', $range );
				$highlightedDatesString .=  '"' .
					$dateArray[1] . '/' .
					$dateArray[0] . '/' .
					$dateArray[2] . '", ';
			} else {
				$dateArray = explode( '/', str_replace( '-', '/', $range ) );
				$highlightedDatesString .= '"' .
					$dateArray[1] . '/' .
					$dateArray[0] . '/' .
					$dateArray[2] . '-' .
					$dateArray[4] . '/' .
					$dateArray[3] . '/' .
					$dateArray[5] . '", ';
			}
		}

		$highlightedDatesString = rtrim( $highlightedDatesString, ", " );

	}

	// set date format string
	if ( array_key_exists( 'date format', $other_args ) ) {
		$dateFormatString = $other_args['date format'];
	}
	else $dateFormatString = $sfigSettings->datePickerDateFormat;

 	// set default date string
	$defaultDateString = 'null';
	$setDefaultDateString = '';

	if ( $cur_value ) {
		$parts = explode( '/', $cur_value );
		if ( count( $parts ) == 3 ) {
			$defaultDateString = '"' . $parts[1] . '/' . $parts[0] . '/' . $parts[2] . '"';

			$setDefaultDateString = <<<END
				document.getElementById("input_{$sfgFieldNum}").value=
				YAHOO.util.Date.format(
					new Date(Date.parse($defaultDateString)),
					{format:"$dateFormatString"},
					'wiki'
				);
END;

		}
	}

	// set month strings
	if ( array_key_exists( 'month names', $other_args ) ) {
		$monthNames = $other_args['month names'];
	} else {
		$monthNames = $sfigSettings->datePickerMonthNames;
	}

	// set day strings
	if ( array_key_exists( 'day names', $other_args ) ) {
		$dayNames = $other_args['day names'];
	} else {
		$dayNames = $sfigSettings->datePickerDayNames;
	}

	// set show reset button string
	if ( array_key_exists( 'show reset button', $other_args ) ) {
		$showResetButton = true;
	} elseif ( array_key_exists( 'hide reset button', $other_args ) ) {
		$showresetbutton = false;
	} else {
		$showResetButton = $sfigSettings->datePickerShowResetButton;
	}

	if ( $showResetButton && $is_disabled ) {

		$resetButtonString =
 			'<button tabindex="-1" type=button id="input_' . $sfgFieldNum . '_resetbutton" class="' . $classString . '" onclick="return false;" ' .
			'style="height:1.5em;width:1.5em; vertical-align:middle;background-image: url(' . $sfigSettings->scriptPath . '/DatePickerResetButtonDisabled.gif);' .
			'background-position: center center; background-repeat: no-repeat;" disabled ></button>';

	} elseif ( $showResetButton ) {

		$resetButtonString =
 			'<button tabindex="-1" type=button id="input_' . $sfgFieldNum . '_resetbutton" class="' . $classString . '" onclick="reset_datepicker(this);" ' .
			'style="height:1.5em;width:1.5em;vertical-align:middle;background-image: url(' . $sfigSettings->scriptPath . '/DatePickerResetButton.gif);' .
			'background-position: center center; background-repeat: no-repeat;" ></button>';
	} else {
		$resetButtonString = "";
	}

	// compose html text
	if ( array_key_exists( 'hidden', $other_args ) ) {
		$htmltext = '<input type="hidden" id="input_' . $sfgFieldNum . '" value="' . $cur_value .
			'" class="createboxInput ' .  $mandatoryString . $classString . '" name="' . $input_name . '" /><span id="info_' . $sfgFieldNum . '" class="errorMessage"></span>';
	} elseif ( $is_disabled ) {
		$htmltext =
			'<span class="yui-skin-sam">' .
			'<input type="text" ' . $sizeString . $maxlengthString . ' id="input_' . $sfgFieldNum . '" ' .
			'value="' . $cur_value . '" class="createboxInput ' . $mandatoryString . $classString . '" ' .
			'style="vertical-align:middle;" name="' . $input_name . '" readonly />' .
			'<button tabindex="-1" type=button id="input_' . $sfgFieldNum . '_button" class="' . $classString . '" onclick="return false;" ' .
			'style="height:1.5em;width:1.5em;vertical-align:middle;background-image: url(' . $sfigSettings->scriptPath . '/DatePickerButtonDisabled.gif);' .
			'background-position: center center; background-repeat: no-repeat;" disabled ></button>' .
			$resetButtonString . "\n" .
			'<span id="info_' . $sfgFieldNum . '" class="errorMessage"></span>' .
			'</span>';

	} else {
		$htmltext =
			'<span class="yui-skin-sam">' .
			'<span id="input_' . $sfgFieldNum . '_container" style="position:absolute;display:inline;margin-top:2em;"><span id="input_' . $sfgFieldNum . '_calendar"></span></span>' .
			'<input type="text" ' . $sizeString . $maxlengthString . $disableInputString . ' id="input_' . $sfgFieldNum . '" ' .
			'value="' . $cur_value . '" class="createboxInput ' . $mandatoryString . $classString . '" ' .
			'style="vertical-align:middle;" name="' . $input_name . '" />' .
			'<button tabindex="-1" type=button id="input_' . $sfgFieldNum . '_button" class="' . $classString . '" onclick="toggle_datepicker(this);" ' .
			'style="height: 1.5em; width: 1.5em;vertical-align:middle;background-image: url(' . $sfigSettings->scriptPath . '/DatePickerButton.gif);' .
			'background-position: center center; background-repeat: no-repeat;" ></button>' .
			$resetButtonString . "\n" .
			'<span id="info_' . $sfgFieldNum . '" class="errorMessage"></span>' .
			'</span>';
	}

	// compose Javascript
	if ( array_key_exists( 'hidden', $other_args ) ) {
		$jstext = '';
	} elseif ( $is_disabled ) {
		$jstext = '';
	} else {
		$jstext = <<<END
			function setup_input_{$sfgFieldNum}() {

			sfiElements['settings_$sfgFieldNum'] = new Object();
			sfiElements['settings_$sfgFieldNum'].start_weekday = $weekStartString;
			sfiElements['settings_$sfgFieldNum'].show_week_header = $weekNumberString;
			sfiElements['settings_$sfgFieldNum'].disabled_days_of_week = [$disabledDaysOfWeek];
			sfiElements['settings_$sfgFieldNum'].highlighted_days_of_week = [$highlightedDaysOfWeek];
			sfiElements['settings_$sfgFieldNum'].disabled_days = [$disabledDatesString];
			sfiElements['settings_$sfgFieldNum'].highlighted_days = [$highlightedDatesString];
			sfiElements['settings_$sfgFieldNum'].first_day = $firstDateString;
			sfiElements['settings_$sfgFieldNum'].last_day = $lastDateString;
			sfiElements['settings_$sfgFieldNum'].default_day = $defaultDateString;
			sfiElements['settings_$sfgFieldNum'].locale_months = "$monthNames";
			sfiElements['settings_$sfgFieldNum'].locale_weekdays = "$dayNames";
			sfiElements['settings_$sfgFieldNum'].date_format = "$dateFormatString";

			$setDefaultDateString;
		}

		addOnloadHook(setup_input_{$sfgFieldNum});
END;
	}

	return array( $htmltext, $jstext );
	}
}
