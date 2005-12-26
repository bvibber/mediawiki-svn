<?
/* yearbook-wiki.php - wiki formatting functions
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

/* fmt_wiki - apply wiki formatting to text */
function fmt_wiki ($text) {
	while (ereg ("\[\[([^[|]*)(\|[^]|]*|)\]\]",$text,$regs)) {
		$repl = "<a href=\"http://www.wikipedia.com/wiki.cgi?" . id_title($regs[1]) . "\">";
		if (substr ($regs[2],0,1) == "|")
			$repl .= substr ($regs[2],1);
		else
			$repl .= $regs[1];
		$repl .= "</a>";

		$text = str_replace ("[[" . $regs[1] . $regs[2] . "]]",$repl,$text);
	}
	return $text;
}

/* End of file */
?>