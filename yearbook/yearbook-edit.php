<?
/* yearbook-edit.php - code to edit yearbook entries
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

/* edit_entry - edit an entry */
function edit_entry ($id) {
	$eresult = mysql_query ("SELECT year,month,day,etype,txt FROM facts WHERE id=\"" . $id . "\"");
	if (! $eresult)
		die ("edit entry query failed: " . mysql_error()); 
	if (mysql_num_rows($eresult) > 1)
		die ("database corruption - multiple entries with same ID");

	$entry = mysql_fetch_object ($eresult);
	
	if ($eresult == 0)
		do_error_page ("entry with ID=$id does not exist");
	else
		entry_editor ("",$id,$entry->year,$entry->month,$entry->day,$entry->etype,$entry->txt);
}

/* add_entry - add a new entry */
function add_entry () {
	global $year, $month, $day;	

	entry_editor ("",false,$year,$month,$day,"","");
}

/* save_entry - saves an edited entry */
function save_entry ($id) {
	global $year, $month, $day, $etype, $desc;

	# validate an entry
	$v = validate_entry($year,$month,$day,$etype);

	if ($v["errmsg"] != "")
		entry_editor ("<p><font color=\"#ff0000\">" . $v["errmsg"] . "</font></p>\n",
			$id, $year, $month, $day, $etype, $desc);
	else {
		# Quote $desc
		$desc = addslashes ($desc);

		# Query to save data
		$query = "UPDATE facts SET year=" . $v["year"] . ", month=" . $v["month"] . ", day=" . $v["day"] . ", " .
					"etype=\"$etype\", txt=\"$desc\" WHERE id=$id";
		$sresult = mysql_query ($query);
		if (! $sresult)
			die ("save entry facts update failed: " . mysql_error ());

		# Output success message
		display_header ("Wikipedia Yearbook: Entry saved");
	}
}

/* save_new_entry - saves a new entry */
function save_new_entry () {
	global $year, $month, $day, $etype, $desc;

	# validate an entry
	$v = validate_entry($year,$month,$day,$etype);

	if ($v["errmsg"] != "")
		entry_editor ("<p><font color=\"#ff0000\">" . $v["errmsg"] . "</font></p>\n",
			false, $year, $month, $day, $etype, $desc);
	else {
		# Quote $desc
		$desc = addslashes ($desc);

		# Query to save data
		$query = "INSERT facts SET year=" . $v["year"] . ", month=" . $v["month"] . ", day=" . $v[day] . ", " .
					"etype=\"$etype\", txt=\"$desc\"";
		$sresult = mysql_query ($query);
		if (! $sresult)
			die ("saving new entry facts update failed: " . mysql_error ());

		# Output success message
		display_header ("Wikipedia Yearbook: Entry added");
		display_footer ();
	}
}

/* delete - request confirmation for deletion of an entry */
function delete ($id) {
	display_header ("Wikipedia Yearbook: Confirm deletion");

	echo ("<p>Are you sure you really want to delete this page?</p>");
	
	echo ("<form action=\"yearbook.phtml\" method=\"get\">\n");
	echo ("<input type=\"hidden\" name=\"action\" value=\"do_delete\">\n");
	echo ("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	echo ("<input type=\"submit\" value=\"Yes\">\n");
	echo ("</form>\n");
	display_footer ();
}

/* do_delete - really delete an entry */
function do_delete ($id) {
	$res = mysql_query ("DELETE FROM facts WHERE id=$id");
	if (!$res)
		die ("deleting entry facts delete failed: " . mysql_error ());

	display_header ("Wikipedia Yearbook: Page deleted");
	display_footer ();
}

/* End of file */
?>