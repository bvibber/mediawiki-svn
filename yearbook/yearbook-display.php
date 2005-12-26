<?
/* yearbook-display.php - utilities for displaying pages
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

/* fmt_title - format a page title */
function fmt_title ($title) {
	return strtr ($title, "_", " ");
}

/* id_title - turn a formatted page title into an id */
function id_title ($title) {
	return strtr (ucfirst($title), " ", "_");
}

/* fmt_errmsg - format an error message */
function fmt_errmsg ($msg) {
	return "<font color=\"#ff0000\">" . $msg . "</font><br>\n";
}

/* display_header - display the page header */
function display_header ($title) {
	global $bodyattrs;

	echo ("<html>\n");
	echo ("<head>\n");
	echo ("<title>" . $title . "</title>\n");
	echo ("</head>\n");
	echo ("<body " . $bodyattrs . " >\n");
	echo ("<h1>" . $title . "</h1>\n");
}

/* display_footer - display the page footer */
function display_footer () {
	echo("<p><hr>\n");
	echo("Data: &copy; Wikipedia Contributors 2001<br>\n");
	echo("Code: &copy; Simon J. Kissane 2001<br>\n");
	echo("See the <a href=\"./license.html\">licenses</a> for further details.</p>\n");
	echo("</body>\n");
	echo("</html>\n");
}

/* show_century_bar - show a bar for current, next and previous century */
function show_century_bar ($century, $is_century_page) {
	/* link to "Centuries" */
	echo ("<p><a href=\"yearbook.phtml?title=Centuries\">Centuries</a>: ");

	/* previous century */
	echo ("<a href=\"yearbook.phtml?title=" . century_id ($century - 1) . "\">");
	echo (fmt_century ($century - 1) . "</a> |");

	/* current century */
	if ($is_century_page)
		echo ("<b>" . fmt_century ($century) . "</b> | ");
	else {
		echo ("<a href=\"yearbook.phtml?title=" . century_id ($century) . "\">");
		echo (fmt_century ($century) . "</a> |");
	}

	/* next century */
	echo ("<a href=\"yearbook.phtml?title=" . century_id ($century + 1) . "\">");
	echo (fmt_century ($century + 1) . "</a></p>\n");
}

/* show_year_bar - show a bar for five years before and after current year */
function show_year_bar ($year) {
	echo ("<p>");
	for ($i = $year - 5; $i <= $year + 5; $i++) {
		if ($i == $year)
			echo ("<b>" . fmt_year ($i) . "</b>");
		else {
			echo ("<a href=\"yearbook.phtml?title=" . year_id ($i) . "\">");
			echo (fmt_year ($i) . "</a>");
		}
		if ($i != $year + 5)
			echo (" | ");
	}
	echo ("</p>\n");
}

/* show_month_bar - show a bar for the twelve months of the year */
function show_month_bar ($month, $is_month_page) {
	/* link to "Months" */
	echo ("<p><a href=\"yearbook.phtml?title=Months\">Months</a>: ");

	/* links for each month */
	for ($i = 1; $i <= 12; $i ++) {
		if ($is_month_page && $i == $month)
			echo ("<b>" . fmt_month ($i) . "</b>");
		else {
			echo ("<a href=\"yearbook.phtml?title=" . fmt_month ($i) . "\">");
			echo (fmt_month($i) . "</a>");
		}

		if ($i != 12)
			echo (" | ");
	}
}

/* show_day_bar - show a bar for the previous and future five days */
function show_day_bar ($month, $day) {
	$start = mod(get_doy ($month, $day, true) - 5, 366);
	$end = mod(get_doy ($month, $day, true) + 5, 366);

	echo ("<p>");
	for ($i = $start; $i != $end + 1; $i = mod($i + 1, 366)) {
	
		$month_i = get_month ($i, true);
		$day_i = get_day ($i, true);

		if ($month_i == $month && $day_i == $day)
			echo ("<b>" . fmt_day ($month_i,$day_i) . "</b>");
		else {
			echo ("<a href=\"yearbook.phtml?title=" . day_id ($month_i, $day_i) . "\">");
			echo (fmt_day ($month_i, $day_i) . "</a>");
		}

		if ($i != $end)
			echo (" | ");
	}		
}

/* show_doy - display the day of year */
function show_doy ($month, $day)
{
	echo ("<p> " . fmt_day ($month, $day) . " is the ");
	if ($month != 2 || $day != 29) {
		echo (ord_th(get_doy($month,$day,false)) . " day of the year in an ");
		echo ("ordinary year, and the " . ord_th(get_doy($month,$day,true)) . " day of the year in a <a ");
		echo ("href=\"http://www.wikipedia.com/wiki.cgi?Leap_year\">leap year</a></p>\n");
	}
	else {
		echo (ord_th(get_doy($month,$day,true)) . " day of the year in a <a ");
		echo ("href=\"http://www.wikipedia.com/wiki.cgi?Leap_year\">leap year</a>, and does not exist ");
		echo ("in an ordinary year.</p>\n");
	}
}

/* display_month_selector - display drop down box for months */
function display_month_selector ($month) {
	echo ("<select name=\"month\">\n");
	echo ("<option value=\"0\"");
	if ($month==0)
		echo (" selected");
	echo (">N/A</option>\n");

	for ($i = 1; $i <= 12; $i ++) {
		echo ("<option value=\"$i\"");
		if ($i==$month)
			echo (" selected");
		echo (">" . fmt_month ($i) . "</option>\n");
	}
	echo ("</select>");
} 


/* display_day_selector - display drop down box for days */
function display_day_selector ($day) {
	echo ("<select name=\"day\">\n");
	echo ("<option value=\"0\"");
	if ($day==0)
		echo (" selected");
	echo (">N/A</option>\n");

	for ($i = 1; $i <= 31; $i ++) {
		echo ("<option value=\"$i\"");
		if ($i==$day)
			echo (" selected");
		echo (">" . $i . "</option>\n");
	}
	echo ("</select>");
} 

/* display_etype_selector - display drop down box for entry types */
function display_etype_selector ($etype) {
	/* Get the headings */ 
	$hresult = mysql_query ("SELECT etype,name FROM headings ORDER BY prio DESC");
	if (! $hresult)
		die ("year headings query failed: " . mysql_error());

	echo ("<select name=\"etype\">\n");
	while ($heading = mysql_fetch_object($hresult)) {
		echo ("<option value=\"" . $heading->etype . "\"");
		if ($etype==$heading->etype)
			echo (" selected");
		echo (">" . $heading->name . "</option>\n");
	}
	echo ("</select>\n");
}

/* entry_editor - display page to edit an entry, optionally with an error message */
function entry_editor ($msg,$id,$year,$month,$day,$etype,$desc) {
	if (!$id)
		display_header ("Wikipedia Yearbook: Adding new entry");
	else {
		display_header ("Wikipedia Yearbook: Editing entry, ID=$id");
		echo ("<p><a href=\"yearbook.phtml?action=delete&id=$id\">Delete this entry</a></p>\n");
	}

	if ($msg != "")
		echo ($msg);

	echo ("<form action=\"yearbook.phtml\" method=\"get\">\n");
	if (!$id) {
		echo ("<input type=\"hidden\" name=\"action\" value=\"save_new_entry\">\n");
		echo ("<p>Entry ID: New entry<br>\n");
	}
	else {
		echo ("<input type=\"hidden\" name=\"action\" value=\"save_entry\">\n");
		echo ("<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">\n");
		echo ("<p>Entry ID: $id<br>\n");
	}
	echo ("Year: <input type=\"text\" name=\"year\" value=\"" . ($year == "" ? "" : fmt_year ($year)) . "\"><br>\n");
	echo ("Month:");
	display_month_selector ($month);
	echo ("<br>\n");
	echo ("Day:");
	display_day_selector ($day);
	echo ("<br>\n");
	echo ("Entry type: ");
	display_etype_selector ($etype);
	echo ("<br>\n");
	echo ("Text:<br> <textarea name=\"desc\" rows=\"3\" cols=\"60\">");
	echo ($desc);
	echo ("</textarea></p>\n");
	echo ("<input type=\"submit\" value=\"Save\">\n");
	echo ("</form>");

	display_footer ();
}

/* validate_entry - validate an entry */
function validate_entry ($year,$month,$day,$etype) {
	# collect error messages
	$errmsg = "";

	# have we got values for these?
	$setyear = false;
	$setmonth = false;

	# verify year
	if (ereg("^ *([0-9]+) *$",$year,$regs)) {
		$setyear = true;
		$i_year = $regs[1];
	}
	elseif (ereg("^ *([0-9]+) +[Bb][Cc] *$",$year,$regs)) {
		$setyear = true;
		$i_year = 1 - $regs[1];
	}
	else
		$errmsg .= "Invalid year: '$year'<br>\n";

	# verify month
	if (ereg("^ *([0-9]+) *$",$month,$regs) &&
		$regs[1] >= 0 && $regs[1] <= 12)
	{
		$setmonth = true;
		$i_month = $regs[1];
	}
	else
		$errmsg .= "Invalid month<br>\n";

	# verify day
	if (ereg("^ *([0-9]+) *$",$day,$regs) &&
		$regs[1] >= 0 && $regs[1] <= 31 &&
		(!$setmonth ||
		 (!$setyear && $regs[1] <= month_length($i_month,true)) ||
		 ($regs[1] <= month_length($i_month,is_leap($i_year)))))
			$i_day = $regs[1];
	else
		$errmsg .= "Invalid day<br>\n";

	# verify etype
	$hresult = mysql_query ("SELECT etype FROM headings WHERE etype=\"$etype\"");
	if (! $hresult)
		die ("save entry headings query failed: " . mysql_error());
	if (mysql_num_rows($hresult) > 1)
		die ("database corruption - multiple headings with same etype");
	if (mysql_num_rows($hresult) == 0)
		$errmsg .= "Invalid event type<br>\n";

	# return result
	return array("errmsg" => $errmsg, "year" => $i_year, "month" => $i_month, "day" => $i_day);
}

/* End of file */
?>