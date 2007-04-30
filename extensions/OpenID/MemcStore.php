<?php
/**
 * MemcStore.php -- An OpenID store using MediaWiki's $wgMemc object
 * Copyright 2006,2007 Internet Brands (http://www.internetbrands.com/)
 * By Evan Prodromou <evan@wikitravel.org>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@wikitravel.org>
 * @addtogroup Extensions
 */

# Nonces expire in 6 hours

define('MEMCSTORE_NONCE_EXPIRY', 3600 * 6);
define('MEMCSTORE_RS', "\x1E");
define('MEMCSTORE_US', "\x1F");

if (defined('MEDIAWIKI')) {

	require_once('Auth/OpenID/Interface.php');
	require_once('Auth/OpenID/HMACSHA1.php');
	require_once('Auth/OpenID/CryptUtil.php');

	class OpenID_MemcStore extends Auth_OpenID_OpenIDStore {

		var $prefix = '';

		function OpenID_MemcStore($prefix_part = null)
		{
			global $wgMemc, $wgDBname;
			if (isset($prefix_part)) {
				$this->prefix = $prefix_part . ':';
			}

			$auth_key =
			  Auth_OpenID_CryptUtil::randomString($this->AUTH_KEY_LEN);

			$k = $this->_authKeyKey();
			$res = $wgMemc->add($k, $auth_key);
		}

		function storeAssociation($server_url, $association)
		{
			global $wgMemc;
			$h = $association->handle;
			$k = $this->_associationKey($server_url, $h);
			$assoc_s = $association->serialize();
			$res = $wgMemc->set($k, $assoc_s);
			$handles = $this->_getHandles($server_url);
			$handles[$h] = $association->issued + $association->lifetime;
			$this->_setHandles($server_url, $handles);
			return true;
		}

		function getAssociation($server_url, $handle = null)
		{
			if (isset($handle)) {
				return $this->_getKnownAssociation($server_url, $handle);
			} else { # $handle is null, get the assoc with greatest time left
				return $this->_getBestAssociation($server_url);
			}
		}

		function removeAssociation($server_url, $handle)
		{
			global $wgMemc;

			# First, delete it from the list of handles
			$handles = $this->_getHandles($server_url);
			if (array_key_exists($handle, $handles)) {
				unset($handles[$handle]);
				$this->_setHandles($server_url, $handles);
			}

			# Now, delete the association record
			$k = $this->_associationKey($server_url, $handle);
			$v = $wgMemc->get($k);

			if ($v === false || strlen($v) == 0) {
				return false;
			} else {
				$res = $wgMemc->delete($k);
				return true;
			}
		}

		function storeNonce($nonce)
		{
			$nonces = $this->_getNonces();
			$nonces[$nonce] = time() + MEMCSTORE_NONCE_EXPIRY;
			$this->_setNonces($nonces);
		}

		function useNonce($nonce)
		{
			$nonces = $this->_getNonces();
			if (!array_key_exists($nonce, $nonces)) {
				return false;
			} else {
				unset($nonces[$nonce]);
				$this->_setNonces($nonces);
				return true;
			}
		}

		function getAuthKey()
		{
			global $wgMemc;
			$k = $this->_authKeyKey();
			return $wgMemc->get($k);
		}

		function isDumb()
		{
			return false;
		}

		function _getNonces() {
			global $wgMemc;
			$nonces = array();
			$k = $this->_nonceKey();
			$v = $wgMemc->get($k);
			if ($v !== false && strlen($v) > 0) {
				$records = explode(MEMCSTORE_RS, $v);
				$now = time();
				foreach ($records as $record) {
					list($nonce, $expiry) = explode(MEMCSTORE_US, $record);
					if ($expiry > $now) {
						$nonces[$nonce] = $expiry;
					}
				}
			}
			return $nonces;
		}

		function _setNonces($nonces) {
			global $wgMemc;
			$records = array();
			foreach ($nonces as $nonce => $expiry) {
				$records[] = implode(MEMCSTORE_US, array($nonce, $expiry));
			}
			$v = implode(MEMCSTORE_RS, $records);
			$k = $this->_nonceKey();
			$wgMemc->set($k, $v);
		}

		function _getKnownAssociation($server_url, $handle) {
			global $wgMemc;
			$k = $this->_associationKey($server_url, $handle);
			$v = $wgMemc->get($k);
			if ($v !== false && strlen($v) > 0) {
				# FIXME: why is this nl getting lost?
				$v .= "\n";
				$assoc =
				  Auth_OpenID_Association::deserialize('Auth_OpenID_Association',
													   $v);
				if ($assoc->getExpiresIn() > 0) {
					return $assoc;
				} else {
					return null;
				}
			}
		}

		function _getBestAssociation($server_url) {
			$handles = $this->_getHandles($server_url);
			$maxissue = -1;
			$best = null;
			foreach ($handles as $handle => $expiry) {
				$assoc = $this->_getKnownAssociation($server_url, $handle);
				if ($assoc->issued > $maxissue) {
					$best = $assoc;
					$maxissue = $assoc->issued;
				}
			}
			return $best;
		}

		function _associationKey($url, $handle) {
			global $wgDBname;
			$uhash = sprintf("%x", crc32($url));
			$hhash = sprintf("%x", crc32($handle));
			return "$wgDBname:openid:memcstore:" . $this->prefix . "assoc:$uhash:$hhash";
		}

		function _associationNullKey($url) {
			global $wgDBname;
			$uhash = sprintf("%x", crc32($url));
			return "$wgDBname:openid:memcstore:" . $this->prefix . "assoc:$uhash";
		}

		function _nonceKey() {
			global $wgDBname;
			return "$wgDBname:openid:memcstore:" . $this->prefix . "nonces";
		}

		function _authKeyKey() {
			global $wgDBname;
			return "$wgDBname:openid:memcstore:" . $this->prefix . "authkey";
		}

		function _getHandles($server_url) {
			global $wgMemc;
			$nk = $this->_associationNullKey($server_url);
			$v = $wgMemc->get($nk);
			if ($v === false || strlen($v) == 0) {
				# XXX
				return array();
			} else {
				$handles = array();
				$records = explode(MEMCSTORE_RS, $v);
				$now = time();
				foreach ($records as $record) {
					list($handle, $expiry) = explode(MEMCSTORE_US, $record);
					if ($expiry > $now) {
						$handles[$handle] = $expiry;
					} else {
						$this->_expireHandle($server_url, $handle);
					}
				}
				return $handles;
			}
		}

		function _setHandles($server_url, $handles) {
			global $wgMemc;
			$records = array();
			foreach ($handles as $handle => $expiry) {
				$records[] = implode(MEMCSTORE_US, array($handle, $expiry));
			}
			$nv = implode(MEMCSTORE_RS, $records);
			$nk = $this->_associationNullKey($server_url);
			$wgMemc->set($nk, $nv);
		}

		# Garbage-collects expired handles

		function _expireHandle($server_url, $handles) {
			global $wgMemc;
			$k = $this->_associationKey($server_url, $handle);
			$wgMemc->delete($k);
		}
	}
}
?>
