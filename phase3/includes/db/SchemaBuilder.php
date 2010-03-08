<?php
/**
 * SchemaBuilder - Uses definition in Schema.php to create a DBMS-specific
 * schema for MediaWiki
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
 * @todo Handle lengths on indexes, eg: el_from, el_to(40)
 * @toto Handle REFERENCES/ON DELETE CASCADE
 */
abstract class SchemaBuilder {
	// Final SQL to be output
	private $outputSql = '';

	// If at any point we fail, set this to false
	protected $isOk = true;

	// The prefix used for all tables
	protected $tblPrefix = '';

	// Any options for the table creation. Things like ENGINE=InnoDB
	protected $tblOptions = array();

	/**
	 * Pieces accessible to extensions
	 */

	// Our table definition
	public $tables = array();

	// Old tables that should be deleted if they're still present
	public $tablesToDelete = array();
	
	/**
	 * End externally-visible fields
	 */

	/**
	 * Constructor. We hide it so people don't try to construct their own schema
	 * classes. Use a sane entry point, like newFromType()
	 *
	 * @param $schema Array See Schema::$defaultTables for more information
	 */
	private final function __construct( $schema ) {
		wfRunHooks( 'LoadExtensionSchemaUpdates', array( $this ) );
		$this->tables = $schema;
		$this->adjustTablesForDatabase();
	}

	/**
	 * Get a brand new Mediawiki schema for a given DB type
	 *
	 * @param $type String A database type (eg: mysql, postgres, sqlite)
	 * @return SchemaBuilder subclass
	 */
	public static function newFromType( $type ) {
		$class = 'Schema' . ucfirst( strtolower( $type ) );
		if ( !class_exists( $class ) ) {
			throw new Exception( "No such database class $class" );
		} else {
			return new $class( Schema::$defaultTables );
		}
	}

	/**
	 * Top-level create method. Loops the tables and passes them to the child
	 * classes for implementation
	 * 
	 * @return boolean
	 */
	public function createAllTables() {
		foreach( $this->tables as $name => $definition ) {
			$this->outputSql .= $this->createTable( $name, $definition );
		}
		return $this->isOk;
	}

	/**
	 * Similar to generateTables(), but only generates SQL for tables that do not exist
	 *
	 * @param $db Database object
	 * @return boolean
	 */
	public function updateAllTables( DatabaseBase $db ) {
		$this->setTablePrefix( $db->tablePrefix() );
		foreach( $this->tables as $name => $definition ) {
			if( $db->tableExists( $name ) ) {
				$this->outputSql .= $this->updateTable( $name, $definition, $db );
			} else {
				$this->outputSql .= $this->createTable( $name, $definition );
			}
		}
		return $this->isOk;
	}

	/**
	 * Get the final DBMS-specific SQL
	 * 
	 * @return string
	 */
	public function getSql() {
		return $this->outputSql;
	}

	/**
	 * Set the prefix for all tables, usually $wgDBprefix
	 * 
	 * @param $prefix String The prefix to use for all table names
	 */
	public function setTablePrefix( $prefix ) {
		$this->tblPrefix = $prefix;
	}

	/**
	 * Set the default table options for all tables
	 * @param $opts Array of table options, like 'engine' => 'InnoDB', etc
	 */
	public function setTableOptions( $opts ) {
		$this->tblOptions = $opts;
	}

	/**
	 * Returns database type
	 */
	abstract public function getType();

	/**
	 * Given an abstract table definition, return a DBMS-specific command to
	 * create it.
	 * @param $name The name of the table, like 'page' or 'revision'
	 * @param $definition Array An abstract table definition
	 * @return String
	 */
	abstract protected function createTable( $name, $definition );

	/**
	 * Given an abstract table definition, check the current table and see if
	 * it needs updating, returning appropriate update queries as needed.
	 * @param $name The name of the table, like 'page' or 'revision'
	 * @param $definition Array An abstract table definition
	 * @param $db DatabaseBase object, referring to current wiki DB
	 * @return String
	 */
	abstract protected function updateTable( $name, $definition, $db );

	/**
	 * Makes database-specific changes to the schema. No-op by default.
	 * @return Nothing
	 */
	protected function adjustTablesForDatabase() {}
}
