<?php
/**
 * testMemcStore.php -- Command-line test tool for MemcStore
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
 *
 * Based in part on Tests/Auth/OpenID/StoreTest.php from PHP-openid package
 * From JanRain, Inc.
 *
 * @package OpenID
 * @author JanRain, Inc. <openid@janrain.com>
 * @copyright 2005 Janrain, Inc.
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */

/**
 * To use this test file, you must have PHPUnit (the old one for
 * PHP4!) installed. Copy this file to the "maintenance" subdirectory
 * of your MediaWiki source directory and run it from the command line.
 *
 */

require_once('commandLine.inc');
ini_set( "include_path", "/usr/share/php:" . ini_get("include_path"));

require_once("$IP/extensions/OpenID/MemcStore.php");
require_once('Auth/OpenID/Association.php');
require_once('Auth/OpenID/CryptUtil.php');
require_once('PHPUnit.php');

class Tests_OpenID_MemcStore extends PHPUnit_TestCase {

    function Tests_OpenID_MemcStore($name) {
		$this->PHPUnit_TestCase($name);
	}

    /**
     * Prepares for the SQL store tests.
     */
    function setUp()
    {
        $this->letters = Auth_OpenID_letters;
        $this->digits = Auth_OpenID_digits;
        $this->punct = Auth_OpenID_punct;
        $this->allowed_nonce = $this->letters . $this->digits;
        $this->allowed_handle = $this->letters . $this->digits . $this->punct;
    }

    /**
     * Generates a nonce value.
     */
    function generateNonce()
    {
        return Auth_OpenID_CryptUtil::randomString(8, $this->allowed_nonce);
    }

    /**
     * Generates an association with the specified parameters.
     */
    function genAssoc($now, $issued = 0, $lifetime = 600)
    {
        $sec = Auth_OpenID_CryptUtil::randomString(20);
        $hdl = Auth_OpenID_CryptUtil::randomString(128, $this->allowed_handle);
        return new Auth_OpenID_Association($hdl, $sec, $now + $issued,
                                          $lifetime, 'HMAC-SHA1');
    }

    /**
     * @access private
     */
    function _checkRetrieve(&$store, $url, $handle, $expected, $name = null)
    {
        $retrieved_assoc = $store->getAssociation($url, $handle);
        if (($expected === null) || ($store->isDumb())) {
            $this->assertNull($retrieved_assoc, "Retrieved association " .
                              "was non-null");
        } else {
            if ($retrieved_assoc === null) {
                $this->fail("$name: Got null when expecting " .
                            $expected->serialize());
            } else {
                $this->assertEquals($expected->serialize(),
                                    $retrieved_assoc->serialize(), $name);
            }
        }
    }

    function _checkRemove(&$store, $url, $handle, $expected, $name = null)
    {
        $present = $store->removeAssociation($url, $handle);
        $expectedPresent = (!$store->isDumb() && $expected);
        $this->assertTrue((!$expectedPresent && !$present) ||
                          ($expectedPresent && $present),
                          $name);
    }

    /**
     * Make sure a given store has a minimum of API compliance. Call
     * this function with an empty store.
     *
     * Raises AssertionError if the store does not work as expected.
     *
     * OpenIDStore -> NoneType
     */
    function _testStore($store)
    {

        // Association functions
        $now = time();

        $server_url = 'http://www.myopenid.com/openid';

        $assoc = $this->genAssoc($now);

        $this->_checkRetrieve($store, $server_url, null, null,
            'Make sure that a missing association returns no result');

        $store->storeAssociation($server_url, $assoc);
        $this->_checkRetrieve($store, $server_url, null, $assoc,
            'Check that after storage, getting returns the same result');

        $this->_checkRetrieve($store, $server_url, null, $assoc,
            'more than once');

        $store->storeAssociation($server_url, $assoc);
        $this->_checkRetrieve($store, $server_url, null, $assoc,
            'Storing more than once has no ill effect');

        // Removing an association that does not exist returns not present
        $this->_checkRemove($store, $server_url, $assoc->handle . 'x', false,
                            "Remove nonexistent association (1)");

        // Removing an association that does not exist returns not present
        $this->_checkRemove($store, $server_url . 'x', $assoc->handle, false,
                            "Remove nonexistent association (2)");

        // Removing an association that is present returns present
        $this->_checkRemove($store, $server_url, $assoc->handle, true,
                            "Remove existent association");

        // but not present on subsequent calls
        $this->_checkRemove($store, $server_url, $assoc->handle, false,
                            "Remove nonexistent association after removal");

        // Put assoc back in the store
        $store->storeAssociation($server_url, $assoc);

        // More recent and expires after assoc
        $assoc2 = $this->genAssoc($now, $issued = 1);
        $store->storeAssociation($server_url, $assoc2);

        $this->_checkRetrieve($store, $server_url, null, $assoc2,
            'After storing an association with a different handle, but the same $server_url, the handle with the later expiration isreturned.');

        $this->_checkRetrieve($store, $server_url, $assoc->handle, $assoc,
            'We can still retrieve the older association');

        $this->_checkRetrieve($store, $server_url, $assoc2->handle, $assoc2,
            'Plus we can retrieve the association with the later expiration explicitly');

        $assoc3 = $this->genAssoc($now, $issued = 2, $lifetime = 100);
        $store->storeAssociation($server_url, $assoc3);

        // More recent issued time, so assoc3 is expected.
        $this->_checkRetrieve($store, $server_url, null, $assoc3, "(1)");

        $this->_checkRetrieve($store, $server_url, $assoc->handle,
                              $assoc, "(2)");

        $this->_checkRetrieve($store, $server_url, $assoc2->handle,
                              $assoc2, "(3)");

        $this->_checkRetrieve($store, $server_url, $assoc3->handle,
                              $assoc3, "(4)");

        $this->_checkRemove($store, $server_url, $assoc2->handle, true, "(5)");

        $this->_checkRetrieve($store, $server_url, null, $assoc3, "(6)");

        $this->_checkRetrieve($store, $server_url, $assoc->handle,
                              $assoc, "(7)");

        $this->_checkRetrieve($store, $server_url, $assoc2->handle,
                              null, "(8)");

        $this->_checkRetrieve($store, $server_url, $assoc3->handle,
                              $assoc3, "(9)");

        $this->_checkRemove($store, $server_url, $assoc2->handle,
                            false, "(10)");

        $this->_checkRemove($store, $server_url, $assoc3->handle,
                            true, "(11)");

        $this->_checkRetrieve($store, $server_url, null, $assoc, "(12)");

        $this->_checkRetrieve($store, $server_url, $assoc->handle,
                              $assoc, "(13)");

        $this->_checkRetrieve($store, $server_url, $assoc2->handle,
                              null, "(14)");

        $this->_checkRetrieve($store, $server_url, $assoc3->handle,
                              null, "(15)");

        $this->_checkRemove($store, $server_url, $assoc2->handle,
                            false, "(16)");

        $this->_checkRemove($store, $server_url, $assoc->handle,
                            true, "(17)");

        $this->_checkRemove($store, $server_url, $assoc3->handle,
                            false, "(18)");

        $this->_checkRetrieve($store, $server_url, null, null, "(19)");

        $this->_checkRetrieve($store, $server_url, $assoc->handle,
                              null, "(20)");

        $this->_checkRetrieve($store, $server_url, $assoc2->handle,
                              null, "(21)");

        $this->_checkRetrieve($store, $server_url,$assoc3->handle,
                              null, "(22)");

        $this->_checkRemove($store, $server_url, $assoc2->handle,
                            false, "(23)");

        $this->_checkRemove($store, $server_url, $assoc->handle,
                            false, "(24)");

        $this->_checkRemove($store, $server_url, $assoc3->handle,
                            false, "(25)");
    }

    function _checkUseNonce(&$store, $nonce, $expected, $msg=null)
    {
        $actual = $store->useNonce($nonce);
        $expected = $store->isDumb() || $expected;
        $this->assertTrue(($actual && $expected) || (!$actual && !$expected),
                          "_checkUseNonce failed: $msg");
    }

    function _testNonce(&$store)
    {
        // Nonce functions

        // Random nonce (not in store)
        $nonce1 = $this->generateNonce();

        // A nonce is not present by default
        $this->_checkUseNonce($store, $nonce1, false, 1);

        // Storing once causes useNonce to return true the first, and
        // only the first, time it is called after the $store->
        $store->storeNonce($nonce1);
        $this->_checkUseNonce($store, $nonce1, true, 2);
        $this->_checkUseNonce($store, $nonce1, false, 3);
        $this->_checkUseNonce($store, $nonce1, false, 4);

        // Storing twice has the same effect as storing once.
        $store->storeNonce($nonce1);
        $store->storeNonce($nonce1);
        $this->_checkUseNonce($store, $nonce1, true, 5);
        $this->_checkUseNonce($store, $nonce1, false, 6);
        $this->_checkUseNonce($store, $nonce1, false, 7);

        // Auth key functions

        // There is no key to start with, so generate a new key and
        // return it.
        $key = $store->getAuthKey();

        // The second time around should return the same as last time.
        $key2 = $store->getAuthKey();
        $this->assertEquals($key, $key2, "Auth keys differ");
        $this->assertEquals(strlen($key), $store->AUTH_KEY_LEN,
                            "Key length not equals AUTH_KEY_LEN");
    }

	function testMemcStore() {
		# Unique prefix for this test
		$prefix = sprintf("test-%x", time());
		$store = new OpenID_MemcStore($prefix);
		$this->_testStore($store);
		$this->_testNonce($store);
	}
}

$suite = new PHPUnit_TestSuite();
$suite->addTest(new Tests_OpenID_MemcStore('testMemcStore'));

$result = PHPUnit::run($suite);
print $result->toString();

?>