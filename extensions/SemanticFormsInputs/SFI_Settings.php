<?php
/**
 * Settings for the Semantic Forms Inputs extension.
 *
 * @author Stephan Gambke
 * @version 0.2
 * @date 06-Nov-2009
 *
 * To change the default settings you can uncomment (or copy) the
 * examples here and adjust them to your needs. You may as well
 * include them in your LocalSettings.php.
 */



###
# This is the path to your installation of Semantic Forms as
# seen from the web. No final slash.
##
$sfigSettings->scriptPath = $wgScriptPath . '/extensions/SemanticFormsInputs';

###
# This is the base URL for the YUI (Yahoo! User Interface) files
# used by the 'datepicker' input, and possibly other input types
# in the futre.
##
$sfigSettings->yuiBase = "http://yui.yahooapis.com/2.7.0/build/";


### Date Picker Settings

###
# This is the first selectable date (format dd/mm/yyyy)
# Sample value: '01/01/2005'
##
$sfigSettings->datePickerFirstDate = null;

###
# This is the last selectable date (format dd/mm/yyyy)
# Sample value: '31/12/2015'
##
$sfigSettings->datePickerLastDate = null;

###
# The date format string. It is used for the input and for the date sent back with the form. Use the following keys: 
# 
#  %a - abbreviated weekday name according to the current locale 
#  %A - full weekday name according to the current locale 
#  %b - abbreviated month name according to the current locale 
#  %B - full month name according to the current locale 
#  %c - preferred date and time representation for the en locale 
#  %C - century number (the year divided by 100 and truncated to an integer, range 00 to 99) 
#  %d - day of the month as a decimal number (range 01 to 31) 
#  %D - same as %m/%d/%y 
#  %e - day of the month as a decimal number, a single digit is preceded by a space (range ' 1' to '31') 
#  %F - same as %Y-%m-%d (ISO 8601 date format) 
#  %g - like %G, but without the century 
#  %G - The 4-digit year corresponding to the ISO week number 
#  %h - same as %b 
#  %H - hour as a decimal number using a 24-hour clock (range 00 to 23) 
#  %I - hour as a decimal number using a 12-hour clock (range 01 to 12) 
#  %j - day of the year as a decimal number (range 001 to 366) 
#  %k - hour as a decimal number using a 24-hour clock (range 0 to 23); single digits are preceded by a blank. (See also %H.) 
#  %l - hour as a decimal number using a 12-hour clock (range 1 to 12); single digits are preceded by a blank. (See also %I.) 
#  %m - month as a decimal number (range 01 to 12) 
#  %M - minute as a decimal number 
#  %n - newline character 
#  %p - either `AM' or `PM' according to the given time value 
#  %P - like %p, but lower case 
#  %r - time in a.m. and p.m. notation equal to %I:%M:%S %p 
#  %R - time in 24 hour notation equal to %H:%M 
#  %s - number of seconds since the Epoch, ie, since 1970-01-01 00:00:00 UTC 
#  %S - second as a decimal number 
#  %t - tab character 
#  %T - current time, equal to %H:%M:%S 
#  %u - weekday as a decimal number [1,7], with 1 representing Monday 
#  %U - week number of the current year as a decimal number, starting with the first Sunday as the first day of the first week 
#  %V - The ISO 8601:1988 week number of the current year as a decimal number, range 01 to 53,
#       where week 1 is the first week that has at least 4 days in the current year, and with Monday as the first day of the week. 
#  %w - day of the week as a decimal, Sunday being 0 
#  %W - week number of the current year as a decimal number, starting with the first Monday as the first day of the first week 
#  %x - preferred date representation for the en locale without the time 
#  %X - preferred time representation for the en locale without the date 
#  %y - year as a decimal number without a century (range 00 to 99) 
#  %Y - year as a decimal number including the century 
#  %z - numerical time zone representation 
#  %Z - time zone name or abbreviation 
#  %% - a literal `%' character 
##
if ( $wgAmericanDates )
	$sfigSettings->datePickerDateFormat = '%m/%d/%Y';
else
	$sfigSettings->datePickerDateFormat = '%d/%m/%Y';

###
# This determines the start of the week in the display
# Set it to: 0 (Zero) for Sunday, 1 (One) for Monday etc.
# Sample value: 1
##
$sfigSettings->datePickerWeekStart = 0;

###
# This determines if the number of the week shall be shown.
##
$sfigSettings->datePickerShowWeekNumbers = false;

###
# This determines if the input field shall be disabled. The user can
# only set the date via the datepicker in this case.
##
$sfigSettings->datePickerDisableInputField = false;

###
# This determines if a reset button shall be shown. This is the only
# way to erase the input field if it is disabled for direct input.
##
$sfigSettings->datePickerShowResetButton = false;

###
# This is a string of disabled days of the week, i.e. days the user can not
# pick. The days must be given as comma-separated list of numbers starting
# with 0 for Sunday.
# Sample value: "6,0"
##
$sfigSettings->datePickerDisabledDaysOfWeek = "";

###
# This is a string of highlighted days of the week. The days must be given as
# comma-separated list of numbers starting with 0 for Sunday.
# Sample value: "6,0"
##
$sfigSettings->datePickerHighlightedDaysOfWeek = "";

###
# This is a string of disabled dates, i.e. days the user cannot pick. The
# days must be given as comma-separated list of dates or date ranges. The
# format for days is DD/MM/YYYY, for date ranges use DD/MM/YYYY-DD/MM/YYYY.
# Spaces are permissible.
# Sample value: "25/12/2009 - 06/01/2010, 01/05/2010"
##
$sfigSettings->datePickerDisabledDates = "";

###
# This is a string of highlighted dates. The days must be given as
# comma-separated list of dates or date ranges. The format for days is
# DD/MM/YYYY, for date ranges use DD/MM/YYYY-DD/MM/YYYY.  Spaces are
# permissible.
# Sample value: "25/12/2009 - 06/01/2010, 01/05/2010"
##
$sfigSettings->datePickerHighlightedDates = "";

###
# This determines if long or short month names are shown. The month
# names are the wiki month names according to the current locale.
##
$sfigSettings->datePickerMonthNames = "long";

###
# This determines the format of the names of the days of the
# week. Possible values are 'long', 'medium', 'short', '1char'. The long
# names are the wiki month names according to the current locale. Medium
# names are the wiki short names, short names are the first 2
# characters of the wiki short names and 1char are the initials of the
# wiki short names.
##
$sfigSettings->datePickerMonthNames = "short";
