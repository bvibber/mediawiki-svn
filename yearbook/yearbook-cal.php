<?
/* yearbook-cal.php - calendar-related utilities
 * Copyright (C) 2001  Simon James Kissane
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/* get_ay - convert AD/BC year to astronomical year */
function get_ay ($era,$year) {
	if ($era == "AD")
		return $year;
	elseif ($era == "BC")
		return 1 - $year;
}

/* get_century - given a year, return the century */
function get_century ($year) {
	if ($year > 0)
		return ((int) (($year - 1)/100)) + 1;
	else
		/* i'm amazed how hard its been to get this right: */
		return - ((int) (( - $year)/100));
}

/* ord_th - add correct English ending for ordinal */
function ord_th ($ord) {
	if ($ord % 10 == 1)
		return $ord . "st";
	elseif ($ord % 10 == 2)
		return $ord . "nd";
	elseif ($ord % 10 == 3)
		return $ord . "rd";
	else
		return $ord . "th";
}

/* year_id - given a year, return the associated page title */
function year_id ($year) {
	if ($year > 0)
		return $year;
	else
		return (1 - $year) . "_BC";
}

/* fmt_year - given a year, format it for output */
function fmt_year ($year) {
	if ($year > 0)
		return $year;
	else
		return (1 - $year) . " BC";
}


/* century_id - given a century, return the associated page title */
function century_id ($century) {
	if ($century > 0)
		return ord_th ($century) . "_century";
	else
		return ord_th (1 - $century) . "_century_BC";
}

/* fmt_century - given a century, format it for output */
function fmt_century ($century) {
	if ($century > 0)
		return ord_th ($century) . " century";
	else
		return ord_th (1 - $century) . " century BC";
}

/* month names */
$month_name = array (
	1 => "January",
	2 => "February",
	3 => "March",
	4 => "April",
	5 => "May",
	6 => "June",
	7 => "July",
	8 => "August",
	9 => "September",
	10 => "October",
	11 => "November",
	12 => "December");

/* month lengths (non-leap) */
$month_length = array (
	1 => 31,
	2 => 28,
	3 => 31,
	4 => 30,
	5 => 31,
	6 => 30,
	7 => 31,
	8 => 31,
	9 => 30,
	10 => 31,
	11 => 30,
	12 => 31);


/* fmt_month - return name of given month */
function fmt_month ($month) {
	global $month_name;

	return $month_name[$month];
}

/* day_id - return id for given month and day */
function day_id ($month,$day) {
	global $month_name;

	return $month_name[$month] . "_" . $day;
}

/* fmt_day - return title for given month and day */
function fmt_day ($month,$day) {
	global $month_name;

	return $month_name[$month] . " " . $day;
}

/* parse_month - given a month name, return month number */
function parse_month ($month) {
	if ($month == "January")
		return 1;
	elseif ($month == "February")
		return 2;
	elseif ($month == "March")
		return 3;
	elseif ($month == "April")
		return 4;
	elseif ($month == "May")
		return 5;
	elseif ($month == "June")
		return 6;
	elseif ($month == "July")
		return 7;
	elseif ($month == "August")
		return 8;
	elseif ($month == "September")
		return 9;
	elseif ($month == "October")
		return 10;
	elseif ($month == "November")
		return 11;
	elseif ($month == "December")
		return 12;
	else
		return 0;
}

/* month_length - return length of given month */
function month_length ($month, $is_leap) {
	global $month_length;

	if ($month == 2 && $is_leap)
		return 29;
	else
		return $month_length[$month];
}

/* valid_date - check validity of a date */
function valid_date ($month, $day, $is_leap) {
	return $month >= 1 && $month <= 12 && $day >= 1 && $day <= month_length($month,$is_leap);
}

/* get_doy - return number of a given day in the year */
function get_doy ($month, $day, $is_leap) {
	$doy = $day;

	for ($i = 1; $i < $month; $i++)
		$doy += month_length ($i, $is_leap);
	
	return $doy;
}

/* get_month - return the month in which a given day of the year belongs */
function get_month ($doy, $is_leap) {
	$m_cumul = 0;

	for ($m = 1; $m_cumul < $doy && $m <= 12; $m++)
		$m_cumul += month_length ($m, $is_leap);
	
	return $m - 1;
}

/* get_day - return the day in which a given day of the year belongs */
function get_day ($doy, $is_leap) {
	$month = get_month($doy, $is_leap);

	$m_cumul = 0;
	for ($m = 1; $m < $month; $m++)
		$m_cumul += month_length ($m, $is_leap);
	
	return $doy - $m_cumul;
}

	
/* mod - modulus operation */
function mod ($num,$mod) {
	if ($num > 0)
		return 1 + (($num - 1) % $mod);
	elseif ($num == 0)
		return $mod;
	else
		return $mod + ($num % ($mod + 1));
}

/* is_leap - is given year a leap year?
 * FIXME: currently we use the Julian leap-year rule for all dates
 * we should use the Gregorian for all Gregorian dates, but that
 * raises the issue -- when should we take the changeover to have
 * occured? We could take it from the original introduction of the
 * calendar, but then people would have to make sure they convert
 * dates from sources which converted later. And that raises
 * another issue -- dates like -43-3-15 possibly aren't very accurate,
 * since the Julian calendar was still in a bit of flux back then.
 */
function is_leap ($year) {
	return ($year % 4 == 0);
}

/* End of file */
?>