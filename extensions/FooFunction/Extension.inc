<?php
/**#@+
 * FooFunction
 * -A test for parserfunctions using the new extension class.
 * 
 * @addtogroup Extensions
 * @project FooFunction
 * @author Daniel Friesen <dan_the_man@telus.net>
 * @copyright Copyright © 2008 Daniel Friesen
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @version 1.0a
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

class FooFunction extends Extension {
	
	/**
	 * Return extension credit data
	 * 
	 * @param String $credit Type of credit to return
	 * @param String lang Language code for language to return information in
	 * @return mixed Credit data of the asked type.
	 */
	static function getExtensionCredit( $credit, $lang = null ) {
		switch( $credit ) {
			case 'type':        return 'parserhook';
			case 'name':        return __CLASS__;
			case 'author':      return array(
				array( 'name'  => 'Daniel Friesen',
				       'url'   => 'http://wiki-tools.com/wiki/User:Dantman',
				       'email' => 'dan_the_man@telus.net' ) );
			case 'license':     return 'GPLv2+';
			case 'version':     return '1.0a';
			case 'svn-date':    return '$LastChangedDate$';
			case 'svn-revision':return '$LastChangedRevision$';
			case 'url':         return 'http://wiki-tools.com/wiki/WikiCode';
			case 'description':
				require self::getExtensionFile('i18n.description.inc');
				return $description[$lang];
				break;
		}
	}
	
	/**
	 * Return extension message data
	 * 
	 * @param String $type Type of data to return
	 * @param String lang Language code for language to return information in
	 * @return mixed Message data of the asked type.
	 */
	static function getMessageData( $type, $lang = null ) {
		switch( $type ) {
			case 'id':    return __CLASS__;
			case 'file':  return self::getExtensionFile('i18n.inc');
			case 'prefix':return 'foofunc';
		}
	}
	
	/**
	 * Return path to extension file, or install path for extension
	 * 
	 * @param String $file Name of file to return path for
	 * @return String Path to file, or install path
	 */
	static function getExtensionFile( $file = null ) {
		static $eIP = dirname(__FILE__);
		if(isset($file)) return "$eIP/$file";
		return $eIP;
	}
	
}