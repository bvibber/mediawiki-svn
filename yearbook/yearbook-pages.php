<?
/* yearbook-pages.php - code to display yearbook pages
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

/* do_error_page - displays error messages */
function do_error_page ($error_msgs) {
	display_header ("Wikipedia Yearbook: Error");

	echo ("<p>\n");
	echo ($error_msgs);
	echo ("</p>\n");

	display_footer ();
}

/* view_date_page - view the date page */
function view_date_page ($month, $day) {
	global $title, $edit;

	display_header ("Wikipedia Yearbook: " . fmt_title ($title));
	show_month_bar ($month, false);
	show_day_bar ($month, $day);
	show_doy ($month, $day);

	/* display add entry link */
	echo ("<p><a href=\"yearbook.phtml?action=add_entry&year=" . $year . "\">(add entry)</a></p>\n");

	/* Get the headings */ 
	$hresult = mysql_query ("SELECT etype,heading FROM headings ORDER BY prio DESC");
	if (! $hresult)
		die ("date headings query failed: " . mysql_error());

	/* flag: Have we displayed any data yet? */
	$putdata = false;

	/* check each heading for facts */
	while ($heading = mysql_fetch_object($hresult)) {
		$fresult = mysql_query ("SELECT id,year,txt FROM facts WHERE etype =\""
			. $heading->etype . "\" AND month = " . $month . " AND day = " . $day . " ORDER BY year");
		if (! $fresult)
			die ("date facts query failed: " . mysql_error()); 

		/*  any facts to be displayed under this heading? */
		$heading_empty = (mysql_num_rows ($fresult) == 0);
		$putdata = $putdata || !$heading_empty;

		/* only display heading if it has facts */
		if (!$heading_empty)
			/* display the heading */
			echo ("<h2>" . $heading->heading . "</h2>\n");

		if (!$heading_empty) {
			echo ("<ul>\n");

			/* display each fact for this heading */
			while ($fact = mysql_fetch_object ($fresult)) {
				echo ("<li>");

				/* if edit mode on, display edit link */
				if ($edit==1)
					echo ("<a href=\"yearbook.phtml?action=edit_entry&id=" . $fact->id .
						"\">(edit)</a> ");

				/* year in which event occured */
				echo ("<a href=\"./yearbook.phtml?edit=" . $edit . "&title=" . year_id ($fact->year));
				echo ("\">" . fmt_year ($fact->year) . "</a> - ");

				/* apply wiki formatting to entry text */
				echo (fmt_wiki ($fact->txt));
				echo ( "</li>\n");
			}

			echo ("</ul>\n");
		}
	}

	/* if we didn't have any information to display, inform the user */
	if (! $putdata)
		echo ("<p>Sorry, Wikipedia Yearbook has no information on this date.</p>\n");

	/* link to toggle editing mode; finish page off */
	if ($edit==1)
		echo ("<p><a href=\"yearbook.phtml?edit=0&title=" . $title . "\">Switch off editing mode</a></p>\n");
	else
		echo ("<p><a href=\"yearbook.phtml?edit=1&title=" . $title . "\">Switch on editing mode</a></p>\n");
	display_footer ();
}

/* view_month_page - view a month page */
function view_month_page ($month) {
	display_header ("Wikipedia Yearbook: " . fmt_month ($month));

	show_month_bar ($month, true);

	echo ("<table><tr>\n");
	for ($i = 1; $i <= month_length ($month, true); $i ++) {
		if (($i-1) % 7 == 0)
			echo ("</tr><tr>\n");
		echo ("<td><a href=\"yearbook.phtml?title=" . day_id($month,$i) . "\">" . $i . "</a></td>\n");
	}
	echo ("</tr></table>\n");

	display_footer ();
}

/* view_year_page - view a year page */
function view_year_page ($year) {
	global $title, $edit;

	# Normalize edit
	if ($edit != 1)
		$edit = 0;

	display_header ("Wikipedia Yearbook: " . fmt_title ($title));
	show_century_bar (get_century ($year), false);
	show_year_bar ($year);

	/* display add entry link */
	echo ("<p><a href=\"yearbook.phtml?action=add_entry&year=" . $year . "\">(add entry)</a></p>\n");

	/* Get the headings */ 
	$hresult = mysql_query ("SELECT etype,heading FROM headings ORDER BY prio DESC");
	if (! $hresult)
		die ("year headings query failed: " . mysql_error());
	
	/* flag: Have we displayed any data yet? */
	$putdata = false;

	/* check each heading for facts */
	while ($heading = mysql_fetch_object($hresult)) {
		$fresult = mysql_query ("SELECT id,month,day,txt FROM facts WHERE etype =\""
			. $heading->etype . "\" AND year = " . $year . " ORDER BY month, day");
		if (! $fresult)
			die ("year facts query failed: " . mysql_error()); 

		/*  any facts to be displayed under this heading? */
		$heading_empty = (mysql_num_rows ($fresult) == 0);
		$putdata = $putdata || !$heading_empty;

		/* only display heading if it has facts */
		if (!$heading_empty)
			/* display the heading */
			echo ("<h2>" . $heading->heading . "</h2>\n");

		if (!$heading_empty) {
			echo ("<ul>\n");

			/* display each fact for this heading */
			while ($fact = mysql_fetch_object ($fresult)) {
				echo ("<li>");

				/* if edit mode on, display edit link */
				if ($edit==1)
					echo ("<a href=\"yearbook.phtml?action=edit_entry&id=" . $fact->id .
						"\">(edit)</a> ");

				/* display month / month-day if information available */
				if ($fact->month != 0 && $fact->day != 0) {
					echo ("<a href=\"./yearbook.phtml?edit=" . $edit . "&title=" . day_id ($fact->month,$fact->day));
					echo ("\">" . fmt_day ($fact->month,$fact->day) . "</a> - ");
				}
				else if ($fact ->month != 0) {
					echo ("<a href=\"./yearbook.phtml?title=" . fmt_month ($fact->month));
					echo ("\">" . fmt_month ($fact->month) . "</a> - ");
				}

				/* apply wiki formatting to entry text */
				echo (fmt_wiki ($fact->txt));
				echo ( "</li>\n");
			}

			echo ("</ul>\n");
		}
	}

	/* if we didn't have any information to display, inform the user */
	if (!$putdata)
		echo ("<p>Sorry, Wikipedia Yearbook has no information on this year.</p>\n");

	/* link to toggle editing mode; finish page off */
	if ($edit==1)
		echo ("<p><a href=\"yearbook.phtml?edit=0&title=" . $title . "\">Switch off editing mode</a></p>\n");
	else
		echo ("<p><a href=\"yearbook.phtml?edit=1&title=" . $title . "\">Switch on editing mode</a></p>\n");
	display_footer ();
}

/* view_century_page - view the century page */
function view_century_page ($century) {
	display_header ("Wikipedia Yearbook: " . fmt_century ($century));
	show_century_bar ($century, true);

	echo ("<table><tr>\n");
	for ($i = ($century-1)*100 + 1; $i <= $century*100; $i++) {
		echo ("<td><a href=\"yearbook.phtml?title=" . year_id ($i) . "\">" . fmt_year ($i) . "</a></td>\n");
		if ($i % 10 == 0)
			echo ("</tr><tr>\n");
	}
	echo ("</tr></table>\n");

	display_footer ();
}

/* view_centuries_page - view the centuries page */
function view_centuries_page () {
	display_header ("Wikipedia Yearbook: Centuries");

	echo ("<ul>\n");
	for ($i = -14; $i <= 21; $i++) 
		echo ("<li><a href=\"yearbook.phtml?title=" . century_id($i) . "\">" . fmt_century($i) . "</a></li>\n");
	echo ("</ul>\n");
	
	display_footer ();
}

/* view_months_page - view the months page */
function view_months_page () {
	display_header ("Wikipedia Yearbook: Months");

	echo ("<ul>\n");
	for ($i = 1; $i <= 12; $i++) 
		echo ("<li><a href=\"yearbook.phtml?title=" . fmt_month($i) . "\">" . fmt_month($i) . "</a></li>\n");
	echo ("</ul>\n");
	
	display_footer ();
}

/* view_welcome_page - view the welcome page */
function view_welcome_page () {
	$current_time = getdate(time ());
	$year = $current_time["year"];
	$month = $current_time["mon"];
	$day = $current_time["mday"];

	display_header ("Wikipedia Yearbook: Welcome");
	show_month_bar ($month, false);

	echo ("<p><a href=\"yearbook.phtml?title=Centuries\">Centuries</a></p>\n");

	echo ("<p>Today is <a href=\"yearbook.phtml?title=" . day_id($month,$day) . "\">");
	echo (fmt_day($month,$day) . "</a>, <a href=\"yearbook.phtml?title=" . year_id($year) . "\">");
	echo ($year . "</a></p>\n");

	echo ("<p>This 'Wikipedia Yearbook' is experimental. It is most likely buggy and insecure, and lacks\n" .
	      "essential features (e.g. a version history). For more information, or to comment, see\n" .
	      "<a href=\"http://meta.wikipedia.com/wiki.cgi?title=Wikipedia_Yearbook_Software\">here</a></p>\n");

	display_footer ();
}

/* End of file */
?>