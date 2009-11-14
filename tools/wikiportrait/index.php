<?php
/*
    Wikiportrait - upload, tag and manage uploads of media with a free license, specific for Wikimedia projects
    Copyright (C) 2008-2009 Hay Kranen (hay@bykr.org) / [[User:Husky]]
    Originally developed for Wikimedia Nederland: <http://nl.wikimedia.org>
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
    This software contains (pieces of) software from other people under a free license,
    see doc/readme.txt for more information    
    
    See doc/gpl.txt for the full GPLv3 license
*/
    
	include '_header.php';
?>
	<div id="questions">
		<?php
			show_page( 'welcome' );
		?>

		<p>
			<a href="<?php echo GE_WIZARD; ?>?question=first" class="question"><?php echo disp( 'CLICK_TO_BEGIN' ); ?></a>
		</p>
		
		<?php
			show_page( 'welcome_disclaimer' );
		?>
	</div> <!-- /questions -->
<?php
	include '_footer.php';
