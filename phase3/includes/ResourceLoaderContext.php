<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @author Trevor Parscal
 * @author Roan Kattouw
 */

/**
 * Object passed around to modules which contains information about the state of a specific loader request
 */
class ResourceLoaderContext {
	
	protected $request;
	protected $server;
	protected $lang;
	protected $skin;
	protected $debug;
	protected $only;
	
	public function __construct( WebRequest $request, $server, $lang, $skin, $debug, $only ) {
		$this->request = $request;
		$this->server = $server;
		$this->lang = $lang;
		$this->skin = $skin;
		$this->debug = $debug;
		$this->only = $only;
	}
	public function getRequest() {
		return $this->request;
	}
	public function getServer() {
		return $this->server;
	}
	public function getLanguage() {
		return $this->lang;
	}
	public function getSkin() {
		return $this->request;
	}
	public function getDebug() {
		return $this->debug;
	}
	public function getOnly() {
		return $this->only;
	}
}