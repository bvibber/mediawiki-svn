<?php
/**
 * Schema.php - Abstracted database schema for MediaWiki
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA
 *
 * @author Chad Horohoe <chad@anyonecanedit.org>
 * @todo FOLLOWING TABLES NEED WORK:
 *		-searchindex, hitcounter (custom table options)
 *		-externallinks, ipblocks, oldimage, job (indexes)
 *      -trackbacks (REFERENCES)
 */
class Schema {
	/**
	 * Field types
	 */
	const TYPE_INT       = 'int';
	const TYPE_VARCHAR   = 'varchar';
	const TYPE_DATETIME  = 'datetime'; // On Postgres this is DATETIME. MySQL we use binary(14)
	const TYPE_TEXT      = 'text';
	const TYPE_BLOB      = 'blob';
	const TYPE_BINARY    = 'binary';
	const TYPE_VARBINARY = 'varbinary';
	const TYPE_BOOL      = 'bool';
	const TYPE_ENUM      = 'enum';

	/**
	 * The actual database definition itself. A multi-dimensional associative
	 * array containing the tables and rows. The top-level keys are the table
	 * names (without prefixes). The value for this is a 3-tuple:
	 * 1) prefix - being the prefix for all the columns in the table
	 *    (eg: "cl" for categorylinks)
	 * 2) columns - an array of column name => definition, where definition is
	 *    an associative array of properties and their values
	 * 3) indexes - an array of index name => array of columns to index
	 *
	 */
	public static $defaultTables = array(
		'user' => array(
			'prefix' => 'user',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
					'signed'         => false,
				),
				'name' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'null'    => false,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'real_name' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'null'    => false,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'password' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'new_password' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'newpass_time' => array(
					'type' => self::TYPE_DATETIME,
				),
				'email' => array(
					'type'   => self::TYPE_TEXT,
					'length' => 'tiny',
					'null'   => false,
				),
				'options' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
				'touched' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'token' => array(
					'type'    => self::TYPE_BINARY,
					'length'  => 32,
					'null'    => false,
					'default' => '',
				),
				'email_authenticated' => array(
					'type' => self::TYPE_DATETIME,
				),
				'email_token' => array(
					'type'   => self::TYPE_BINARY,
					'length' => 32,
				),
				'email_token_expires' => array(
					'type' => self::TYPE_DATETIME,
				),
				'registration' => array(
					'type' => self::TYPE_DATETIME,
				),
				'editcount' => array(
					'type' => self::TYPE_INT,
				),
			),
			'indexes' => array(
				'user_name' => array(
					'UNIQUE', 'name',
				),
				'user_email_token' => array(
					'email_token',
				),
			)
		),
		'user_groups' => array(
			'prefix' => 'ug',
			'fields' => array(
				'user' => array(
					'type'        => self::TYPE_INT,
					'null'        => false,
					'primary-key' => true,
					'default'     => 0,
					'signed'      => false,
				),
				'group' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 16,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'ug_user_group' => array(
					'UNIQUE', 'user', 'group',
				),
				'ug_group' => array(
					'group',
				),
			),
		),
		'user_newtalk' => array(
			'prefix' => 'user',
			'fields' => array(
				'id' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'ip' => array(
					'type'    => self::TYPE_VARBINARY,
					'null'    => false,
					'length'  => 40,
					'default' => '',
				),
				'last_timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'un_user_id' => array(
					'id',
				),
				'un_user_ip' => array(
					'ip',
				),
			)
		),
		'user_properties' => array(
			'prefix' => 'up',
			'fields' => array(
				'id' => array(
					'type' => self::TYPE_INT,
					'null' => false,
				),
				'property' => array(
					'type'   => self::TYPE_VARBINARY,
					'null'   => false,
					'length' => 32,
				),
				'value' => array(
					'type' => self::TYPE_BLOB,
				),
			),
			'indexes' => array(
				'user_properties_user_property' => array(
					'UNIQUE', 'user', 'property',
				),
				'user_properties_property' => array(
					'property',
				),
			),
		),
		'page' => array(
			'prefix' => 'page',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
					'signed'         => false,
				),
				'namespace' => array(
					'type' => self::TYPE_INT,
					'null' => false,
				),
				'title' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
				'restrictions' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'counter' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'big',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'is_redirect' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'is_new' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'random' => array(
					'type' => 'real unsigned',
					'null' => false,
				),
				'touched' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'latest' => array(
					'type'   => self::TYPE_INT,
					'null'   => false,
					'signed' => false,
				),
				'len' => array(
					'type'   => self::TYPE_INT,
					'null'   => false,
					'signed' => false,
				),
			),
			'indexes' => array(
				'name_title' => array(
					'UNIQUE', 'namespace', 'title',
				),
				'page_random' => array(
					'random',
				),
				'page_len' => array(
					'len',
				),
			),
		),
		'revision' => array(
			'prefix' => 'rev',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
					'signed'         => false,
				),
				'page' => array(
					'type'   => self::TYPE_INT,
					'null'   => false,
					'signed' => false,
				),
				'text_id' => array(
					'type'   => self::TYPE_INT,
					'null'   => false,
					'signed' => false,
				),
				'comment' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'user_text' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'minor_edit' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'deleted' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'len' => array(
					'type'    => self::TYPE_INT,
					'default' => null,
					'signed'  => false,
				),
			),
			'indexes' => array(
				'rev_page_id' => array(
					'UNIQUE', 'page','id',
				),
				'rev_timestamp' => array(
					'timestamp',
				),
				'page_timestamp' => array(
					'page', 'timestamp',
				),
				'user_timestamp' => array(
					'user', 'timestamp',
				),
				'usertext_timestamp' => array(
					'user_text', 'timestamp',
				),
			),
		),
		'text' => array(
			'prefix' => 'old',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
					'signed'         => false,
				),
				'text' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
					'null'   => false,
				),
				'flags' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
			),
			'indexes' => array(),
		),
		'archive' => array(
			'prefix' => 'ar',
			'fields' => array(
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'text' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
					'null'   => false,
				),
				'comment' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'user_text' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'minor_edit' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
				),
				'flags' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'rev_id' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
				),
				'text_id' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
				),
				'deleted' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'len' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
				),
				'page_id' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
				),
				'parent_id' => array(
					'type'    => self::TYPE_INT,
					'default' => null,
					'signed'  => false,
				),
			),
			'indexes' => array(
				'name_title_timestamp' => array(
					'namespace', 'title', 'timestamp',
				),
				'ar_usertext_timestamp' => array(
					'user_text', 'timestamp',
				),
			),
		),
		'pagelinks' => array(
			'prefix' => 'pl',
			'fields' => array(
				'from' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'pl_from' => array(
					'UNIQUE', 'from','namespace', 'title',
				),
				'pl_namespace' => array(
					'UNIQUE', 'namespace','title', 'from',
				),
			),
		),
		'templatelinks' => array(
			'prefix' => 'tl',
			'fields' => array(
				'from' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'pl_from' => array(
					'UNIQUE', 'from','namespace', 'title',
				),
				'pl_namespace' => array(
					'UNIQUE', 'namespace','title', 'from',
				),
			),
		),
		'imagelinks' => array(
			'prefix' => 'il',
			'fields' => array(
				'from' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'to' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'il_from' => array(
					'UNIQUE', 'from','to',
				),
				'il_namespace' => array(
					'UNIQUE', 'to', 'from',
				),
			),
		),
		'categorylinks' => array(
			'prefix' => 'cl',
			'fields' => array(
				'from' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'to' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'sortkey' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 70,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'cl_from' => array(
					'UNIQUE', 'from','to',
				),
				'cl_sortkey' => array(
					'to', 'sortkey', 'from',
				),
				'cl_timestamp' => array(
					'to', 'timestamp',
				),
			),
		),
		'category' => array(
			'prefix' => 'cat',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
					'signed'         => false,
				),
				'title' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
				'pages' => array(
					'type'    => self::TYPE_INT,
					'signed'  => true,
					'null'    => false,
					'default' => 0,
				),
				'subcats' => array(
					'type'    => self::TYPE_INT,
					'signed'  => true,
					'null'    => false,
					'default' => 0,
				),
				'files' => array(
					'type'    => self::TYPE_INT,
					'signed'  => true,
					'null'    => false,
					'default' => 0,
				),
				'hidden' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
			),
			'prefixes' => array(
				'cat_title' => array(
					'UNIQUE', 'title'
				),
				'cat_pages' => array(
					'pages'
				)
			),
		),
		'externallinks' => array(
			'prefix' => 'el',
			'fields' => array(
				'from' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
					'null'    => false,
					'signed'  => false,
				),
				'to' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
				'index' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
			),
			'indexes' => array(

			),
		),
		'externaluser' => array(
			'prefix' => 'eu',
			'fields' => array(
				'local_id' => array(
					'type'        => self::TYPE_INT,
					'null'        => false,
					'primary-key' => true,
					'signed'      => false,
				),
				'external_id' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
			),
			'indexes' => array(
				'eu_external_id' => array(
					'UNIQUE', 'external_id'
				),
			),
		),
		'langlinks' => array(
			'prefix' => 'll',
			'fields' => array(
				'from' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
					'null'    => false,
					'signed'  => false,
				),
				'lang' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 20,
					'null'    => false,
					'default' => '',
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'll_from' => array(
					'UNIQUE', 'from', 'lang'
				),
				'll_lang' => array(
					'lang', 'title'
				),
			),
		),
		'site_stats' => array(
			'prefix' => 'ss',
			'fields' => array(
				'row_id' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
					'null'   => false,
				),
				'total_views' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'length'  => 'big',
					'default' => 0,
				),
				'total_edits' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'length'  => 'big',
					'default' => 0,
				),
				'good_articles' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'length'  => 'big',
					'default' => 0,
				),
				'total_pages' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'big',
					'default' => -1,
				),
				'users' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'big',
					'default' => -1,
				),
				'active_users' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'big',
					'default' => -1,
				),
				'admins' => array(
					'type'    => self::TYPE_INT,
					'default' => -1,
				),
				'images' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
				),
			),
			'indexes' => array(
				'ss_row_id' => array(
					'UNIQUE', 'row_id'
				)
			),
		),
		'hitcounter' => array(
			'prefix' => 'hc',
			'fields' => array(
				'id' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
					'null'   => false,
				),
			),
		),
		'ipblocks' => array(
			'prefix' => 'ipb',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
				),
				'address' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
					'null'    => false,
					'signed'  => false,
				),
				'by' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
					'null'    => false,
					'signed'  => false,
				),
				'by_text' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'reason' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'auto' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 0,
				),
				'anon_only' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 0,
				),
				'create_account' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 1,
				),
				'enable_autoblock' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 1,
				),
				'expiry' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'range_start' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'range_end' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'deleted' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 0,
				),
				'block_email' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 0,
				),
				'allow_usertalk' => array(
					'type'    => self::TYPE_BOOL,
					'null'    => false,
					'default' => 0,
				),
			),
			'indexes' => array(

			),
		),
		'image' => array(
			'prefix' => 'img',
			'fields' => array(
				'name' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'binary'      => true,
					'null'        => false,
					'default'     => '',
					'primary-key' => true,
				),
				'size' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'width' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'height' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'metadata' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
					'null'   => false,
				),
				'bits' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'media_type' => array(
					'type'   => self::TYPE_ENUM,
					'values' => array(
						"UNKNOWN", "BITMAP", "DRAWING", "AUDIO", "VIDEO", 
						"MULTIMEDIA", "OFFICE", "TEXT", "EXECUTABLE", "ARCHIVE"
					),
					'default' => null,
				),
				'major_mime' => array(
					'type'   => self::TYPE_ENUM,
					'null'   => false,
					'values' => array(
						"unknown", "application", "audio", "image", "text",
						"video", "message", "model", "multipart"
					),
					'default' => 'unknown',
				),
				'minor_mime' => array(
					'type'     => self::TYPE_VARBINARY,
					'length'   => 32,
					'null'     => false,
					'defaullt' => 'unknown',
				),
				'description' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'user_text' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'sha1' => array(
					'type'     => self::TYPE_VARBINARY,
					'length'   => 32,
					'null'     => false,
					'defaullt' => '',
				),
			),
			'indexes' => array(
				'img_usertext_timestamp' => array(
					'user_text', 'timestamp'
				),
				'img_size' => array(
					'size'
				),
				'img_timestamp' => array(
					'timestamp'
				),
				'img_sha1' => array(
					'sha1'
				),
			),
		),
		'oldimage' => array(
			'prefix' => 'oi',
			'fields' => array(
				'name' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'binary'      => true,
					'null'        => false,
					'default'     => '',
					'primary-key' => true,
				),
				'archive_name' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'binary'      => true,
					'null'        => false,
					'default'     => '',
					'primary-key' => true,
				),
				'size' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'width' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'height' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'metadata' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
					'null'   => false,
				),
				'bits' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'media_type' => array(
					'type'   => self::TYPE_ENUM,
					'values' => array(
						"UNKNOWN", "BITMAP", "DRAWING", "AUDIO", "VIDEO",
						"MULTIMEDIA", "OFFICE", "TEXT", "EXECUTABLE", "ARCHIVE"
					),
					'default' => null,
				),
				'major_mime' => array(
					'type'   => self::TYPE_ENUM,
					'null'   => false,
					'values' => array(
						"unknown", "application", "audio", "image", "text",
						"video", "message", "model", "multipart"
					),
					'default' => 'unknown',
				),
				'minor_mime' => array(
					'type'     => self::TYPE_VARBINARY,
					'length'   => 32,
					'null'     => false,
					'defaullt' => 'unknown',
				),
				'description' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
					'null'   => false,
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'user_text' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'sha1' => array(
					'type'     => self::TYPE_VARBINARY,
					'length'   => 32,
					'null'     => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'oi_usertext_timestamp' => array(
					'user_text', 'timestamp'
				),
				'oi_name_timestamp' => array(
					'name', 'timestamp'
				),
				'oi_name_archive_name' => array(
					'name', 'archive_name'
				),
				'oi_sha1' => array(
					'sha1'
				),
			),
		),
		'filearchive' => array(
			'prefix' => 'fa',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
				),
				'name' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'binary'      => true,
					'null'        => false,
					'default'     => '',
				),
				'archive_name' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'binary'      => true,
					'default'     => '',
				),
				'storage_group' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 16,
				),
				'storage_key' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 64,
					'default' => '',
				),
				'deleted_user' => array(
					'type' => self::TYPE_INT,
				),
				'deleted_timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'default' => '',
				),
				'deleted_reason' => array(
					'type' => self::TYPE_TEXT,
				),
				'size' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'default' => 0,
				),
				'width' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'height' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'metadata' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
				),
				'bits' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
				),
				'media_type' => array(
					'type'   => self::TYPE_ENUM,
					'values' => array(
						"UNKNOWN", "BITMAP", "DRAWING", "AUDIO", "VIDEO",
						"MULTIMEDIA", "OFFICE", "TEXT", "EXECUTABLE", "ARCHIVE"
					),
					'default' => null,
				),
				'major_mime' => array(
					'type'   => self::TYPE_ENUM,
					'null'   => false,
					'values' => array(
						"unknown", "application", "audio", "image", "text",
						"video", "message", "model", "multipart"
					),
					'default' => 'unknown',
				),
				'minor_mime' => array(
					'type'     => self::TYPE_VARBINARY,
					'length'   => 32,
					'defaullt' => 'unknown',
				),
				'description' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'default' => 0,
				),
				'user_text' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'default' => '',
				),
				'deleted' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'signed'  => false,
					'default' => 0,
				),
			),
			'indexes' => array(
				'fa_name' => array(
					'name', 'timestamp',
				),
				'fa_storage_group' => array(
					'storage_group', 'storage_key',
				),
				'fa_deleted_timestamp' => array(
					'deleted_timestamp',
				),
				'fa_user_timestamp' => array(
					'user_text', 'timestamp',
				),
			),
		),
		'recentchanges' => array(
			'prefix' => 'rc',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'null'           => false,
					'auto-increment' => true,
					'primary-key'    => true,
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'cur_time' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'user_text' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'comment' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'minor' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'bot' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'new' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'cur_id' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'this_oldid' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'last_oldid' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'type' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'moved_to_ns' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'moved_to_title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'patrolled' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'ip' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 40,
					'null'    => false,
					'default' => '',
				),
				'old_len' => array(
					'type' => self::TYPE_INT,
				),
				'new_len' => array(
					'type' => self::TYPE_INT,
				),
				'deleted' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'length'  => 'tiny',
					'signed'  => false,
				),
				'log_id' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
					'signed'  => false,
				),
				'log_type' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 255,
					'null'    => true,
					'default' => null,
				),
				'log_action' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 255,
					'null'    => true,
					'default' => null,
				),
				'log_params' => array(
					'type' => self::TYPE_BLOB,
					'null' => true,
				),
			),
			'indexes' => array(
				'rc_timestamp' => array(
					'timestamp',
				),
				'rc_namespace_title' => array(
					'namespace', 'title',
				),
				'rc_cur_id' => array(
					'cur_id',
				),
				'new_name_timestamp' => array(
					'new', 'namespace', 'timestamp',
				),
				'rc_ip' => array(
					'ip',
				),
				'rc_ns_usertext' => array(
					'namespace', 'user_text',
				),
				'rc_user_text' => array(
					'user_text', 'timestamp',
				),
			),
		),
		'watchlist' => array(
			'prefix' => 'wl',
			'fields' => array(
				'user' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
					'null'   => false,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'default' => 0,
					'null'    => false,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'notificationtimestamp' => array(
					'type' => self::TYPE_DATETIME,
				),
			),
			'indexes' => array(
				'wl_user' => array(
					'UNIQUE', 'user', 'namespace', 'title',
				),
				'namespace_title' => array(
					'namespace', 'title',
				),
			),
		),
		'math' => array(
			'prefix' => 'math',
			'fields' => array(
				'inputhash' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 16,
					'null'   => false,
				),
				'outputhash' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 16,
					'null'   => false,
				),
				'html_conservativeness' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'length'  => 'tiny',
				),
				'html' => array(
					'type' => self::TYPE_TEXT,
				),
				'mathml' => array(
					'type' => self::TYPE_TEXT,
				),
			),
			'indexes' => array(
				'math_inputhash' => array(
					'UNIQUE', 'inputhash',
				),
			),
		),
		'searchindex' => array(
			'prefix' => 'si',
			'fields' => array(
				'page' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
					'null'   => false,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'null'    => false,
					'default' => '',
				),
				'text' => array(
					'type'   => self::TYPE_TEXT,
					'length' => 'medium',
					'null'   => false,
				),
			),
			'indexes' => array(
				'si_page' => array(
					'UNIQUE', 'page',
				),
				'si_title' => array(
					'FULLTEXT', 'title',
				),
				'si_text' => array(
					'FULLTEXT', 'text',
				),
			),
		),
		'interwiki' => array(
			'prefix' => 'iw',
			'fields' => array(
				'prefix' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 32,
					'null'   => false,
				),
				'url' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
				'local' => array(
					'type' => self::TYPE_BOOL,
					'null' => false,
				),
				'trans' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'null'    => false,
					'default' => 0,
				),
			),
			'indexes' => array(
				'iw_prefix' => array(
					'UNIQUE', 'prefix',
				),
			),
		),
		'querycache' => array(
			'prefix' => 'qc',
			'fields' => array(
				'type' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 32,
					'null'   => false,
				),
				'value' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'qc_type' => array(
					'type', 'value',
				),
			),
		),
		'objectcache' => array(
			'prefix' => '',
			'fields' => array(
				'keyname' => array(
					'type'        => self::TYPE_VARBINARY,
					'length'      => 255,
					'null'        => false,
					'default'     => '',
					'primary-key' => true,
				),
				'value' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
				),
				'exptime' => array(
					'type' => self::TYPE_DATETIME,
				),
			),
			'indexes' => array(
				'exptime' => array(
					'exptime',
				),
			),
		),
		'transcache' => array(
			'prefix' => 'tc',
			'fields' => array(
				'url' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 255,
					'null'   => false,
				),
				'contents' => array(
					'type' => self::TYPE_TEXT,
				),
				'time' => array(
					'type' => self::TYPE_DATETIME,
					'null' => false,
				),
			),
			'indexes' => array(
				'tc_url_idx' => array(
					'UNIQUE', 'url',
				),
			),
		),
		'logging' => array(
			'prefix' => 'log',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'signed'         => false,
					'null'           => false,
					'primary-key'    => true,
					'auto-increment' => true,
				),
				'type' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 32,
					'null'    => false,
					'default' => '',
				),
				'action' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 32,
					'null'    => false,
					'default' => '',
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '19700101000000',
				),
				'user' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'user_text' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'page' => array(
					'type'   => self::TYPE_INT,
					'signed' => false,
					'null'   => true,
				),
				'comment' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'null'    => false,
					'default' => '',
				),
				'params' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
				'deleted' => array(
					'type'    => self::TYPE_INT,
					'length'  => 'tiny',
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
			),
			'indexes' => array(
				'type_time' => array(
					'type', 'timestamp',
				),
				'user_time' => array(
					'user', 'timestamp',
				),
				'page_time' => array(
					'namespace', 'title', 'timestamp',
				),
				'times' => array(
					'timestamp',
				),
				'log_user_type_time' => array(
					'user', 'type', 'timestamp',
				),
				'log_page_id_time' => array(
					'page', 'timestamp',
				),
			),
		),
		'log_search' => array(
			'prefix' => 'ls',
			'fields' => array(
				'field' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 32,
					'null'   => false,
				),
				'value' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'null'   => false,
				),
				'log_id' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
			),
			'indexes' => array(
				'ls_field_val' => array(
					'UNIQUE', 'field', 'value', 'log_id',
				),
				'ls_log_id' => array(
					'log_id',
				),
			),
		),
		'trackbacks' => array(
			'prefix' => 'tb',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'primary-key'    => true,
					'auto-increment' => true,
				),
				'page' => array(
					'type'           => self::TYPE_INT,
					/** @todo DO REST OF THIS FIELD **/
				),
				'title' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'null'   => false,
				),
				'url' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
				'ex' => array(
					'type'   => self::TYPE_TEXT,
				),
				'title' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
				),
			),
			'indexes' => array(
				'tb_page' => array(
					'page',
				)
			),
		),
		'job' => array(
			'prefix' => 'job',
			'fields' => array(
				'id' => array(
					'type'           => self::TYPE_INT,
					'signed'         => false,
					'null'           => false,
					'primary-key'    => true,
					'auto-increment' => true,
				),
				'cmd' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 60,
					'null'    => false,
					'default' => '',
				),
				'namespace' => array(
					'type' => self::TYPE_INT,
					'null' => false,
				),
				'title' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'binary' => true,
					'null'   => false,
				),
				'params' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
			),
			'indexes' => array(
				'job_cmd' => array(
					'cmd', 'namespace', 'title', 'params'
				),
			),
		),
		'querycache_info' => array(
			'prefix' => 'qci',
			'fields' => array(
				'type' => array(
					'type'    => self::TYPE_VARBINARY,
					'length'  => 32,
					'null'    => false,
					'default' => '',
				),
				'timestamp' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '19700101000000',
				),
				
			),
			'indexes' => array(
				'qci_type' => array(
					'UNIQUE', 'type'
				)
			),
		),
		'redirect' => array(
			'prefix' => 'rd',
			'fields' => array(
				'from' => array(
					'type'        => self::TYPE_INT,
					'signed'      => false,
					'null'        => false,
					'primary-key' => true,
					'default'     => 0,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'interwiki' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 32,
					'default' => null,
				),
				'fragment' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'default' => null,
				),
			),
			'indexes' => array(
				'rd_ns_title' => array(
					'namespace', 'title', 'from'
				),
			),
		),
		'querycachetwo' => array(
			'prefix' => 'qcc',
			'fields' => array(
				'type' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 32,
					'null'   => false,
				),
				'value' => array(
					'type'    => self::TYPE_INT,
					'signed'  => false,
					'null'    => false,
					'default' => 0,
				),
				'namespace' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
				'namespacetwo' => array(
					'type'    => self::TYPE_INT,
					'null'    => false,
					'default' => 0,
				),
				'titletwo' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
					'default' => '',
				),
			),
			'indexes' => array(
				'qcc_type' => array(
					'type', 'value',
				),
				'qcc_title' => array(
					'type', 'namespace', 'title'
				),
				'qcc_titletwo' => array(
					'type', 'namespacetwo', 'titletwo'
				),
			),
		),
		'page_restrictions' => array(
			'prefix' => 'pr',
			'fields' => array(
				'page' => array(
					'type' => self::TYPE_INT,
					'null' => false,
				),
				'type' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 60,
					'null'   => false,
				),
				'level' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 60,
					'null'   => false,
				),
				'cascade' => array(
					'type'   => self::TYPE_INT,
					'length' => 'tiny',
					'null'   => false,
				),
				'user' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'expiry' => array(
					'type' => self::TYPE_DATETIME,
					'null' => true,
				),
				'id' => array(
					'type'           => self::TYPE_INT,
					'signed'         => false,
					'null'           => false,
					'primary-key'    => true,
					'auto-increment' => true,
				),
			),
			'indexes' => array(
				'pr_pagetype' => array(
					'UNIQUE', 'page', 'type'
				),
				'pr_typelevel' => array(
					'type', 'level'
				),
				'pr_level' => array(
					'level'
				),
				'pr_cascade' => array(
					'cascade'
				),
			),
		),
		'protected_titles' => array(
			'prefix' => 'pt',
			'fields' => array(
				'namespace' => array(
					'type' => self::TYPE_INT,
					'null' => false,
				),
				'title' => array(
					'type'    => self::TYPE_VARCHAR,
					'length'  => 255,
					'binary'  => true,
					'null'    => false,
				),
				'namespace' => array(
					'type'   => self::TYPE_INT,
					'null'   => false,
					'signed' => false,
				),
				'reason' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'tiny',
				),
				'timestamp' => array(
					'type'   => self::TYPE_BINARY,
					'length' => 14,
					'null'   => false,
				),
				'expiry' => array(
					'type'    => self::TYPE_DATETIME,
					'null'    => false,
					'default' => '',
				),
				'create_perm' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 60,
					'null'   => false,
				),
			),
			'indexes' => array(
				'pt_namespace_title' => array(
					'UNIQUE', 'namespace', 'title'
				),
				'pt_timestamp' => array(
					'timestamp'
				),
			),
		),
		'page_props' => array(
			'prefix' => 'pp',
			'fields' => array(
				'page' => array(
					'type' => self::TYPE_INT,
					'null' => false,
				),
				'propname' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 60,
					'null'   => false,
				),
				'value' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
			),
		),
		'updatelog' => array(
			'prefix' => 'ul',
			'fields' => array(
				'key' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'null'        => false,
					'primary-key' => true,
				),
			),
		),
		'change_tag' => array(
			'prefix' => 'ct',
			'fields' => array(
				'rc_id' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'log_id' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'rev_id' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'tag' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'null'   => false,
				),
				'params' => array(
					'type' => self::TYPE_BLOB,
					'null' => true,
				),
			),
			'indexes' => array(
				'change_tag_rc_tag' => array(
					'UNIQUE', 'rc_id', 'tag'
				),
				'change_tag_log_tag' => array(
					'UNIQUE', 'log_id', 'tag'
				),
				'change_tag_rev_tag' => array(
					'UNIQUE', 'rev_id', 'tag'
				),
				'change_tag_tag_id' => array(
					'tag', 'rc_id', 'rev_id', 'log_id'
				),
			),
		),
		'tag_summary' => array(
			'prefix' => 'ts',
			'fields' => array(
				'rc_id' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'log_id' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'rev_id' => array(
					'type' => self::TYPE_INT,
					'null' => true,
				),
				'tags' => array(
					'type' => self::TYPE_BLOB,
					'null' => false,
				),
			),
			'indexes' => array(
				'tag_summary_rc_id' => array(
					'UNIQUE', 'rc_id'
				),
				'tag_summary_log_id' => array(
					'UNIQUE', 'log_id'
				),
				'tag_summary_rev_id' => array(
					'UNIQUE', 'rev_id'
				),
			),
		),
		'valid_tag' => array(
			'prefix' => 'vt',
			'fields' => array(
				'tag' => array(
					'type'        => self::TYPE_VARCHAR,
					'length'      => 255,
					'null'        => false,
					'primary-key' => true,
				),
			),
		),
		'l10n_cache' => array(
			'prefix' => 'lc',
			'fields' => array(
				'lang' => array(
					'type'   => self::TYPE_VARBINARY,
					'length' => 32,
					'null'   => false,
				),
				'key' => array(
					'type'   => self::TYPE_VARCHAR,
					'length' => 255,
					'null'   => false,
				),
				'value' => array(
					'type'   => self::TYPE_BLOB,
					'length' => 'medium',
					'null'   => false,
				),
			),
			'indexes' => array(
				'lc_lang_key' => array(
					'lang', 'key'
				)
			),
		),
	);
}
