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
 * @todo Handle custom table options, eg: MyISAM for searchindex, MAX_ROWS, etc
 * @todo Handle lengths on indexes, eg: el_from, el_to(40)
 * @toto Handle REFERENCES
 */
abstract class SchemaBuilder {
	// Final SQL to be output
	private $outputSql = '';

	// If at any point we fail, set this to false
	protected $isOk = true;

	// The prefix used for all tables
	protected $tblPrefix = '';

	// Any options for the table creation. Things like ENGINE=InnoDB
	protected $tblOptions = '';

	// Our table definition
	protected $tables = array();

	/**
	 * Constructor. We hide it so people don't try to construct their own schema
	 * classes. Use a sane entry point, like newFromType() or newFromCustomSchema()
	 *
	 * @param $schema Array See Schema::$tables for more information
	 */
	private final function __construct( $schema ) {
		$this->tables = $schema;
	}

	/**
	 * Get a brand new Mediawiki schema for a given DB type
	 *
	 * @param $type String A database type (eg: mysql, postgres, sqlite)
	 * @return SchemaBuilder subclass
	 */
	public static function newFromType( $type ) {
		return self::newFromCustomSchema( $type, Schema::$defaultTables );
	}

	/**
	 * Given an array-based abstract schema, return a DBMS-specific SchemaBuilder object
	 * 
	 * @param $type String A database type (eg: mysql, postgres, sqlite)
	 * @param $schema Array See Schema::$tables for more information
	 * @return SchemaBuilder subclass
	 */
	public static function newFromCustomSchema( $dbType, $schema ) {
		$class = ucfirst( strtolower( $dbType ) ) . 'Schema';
		if ( !class_exists( $class ) ) {
			throw new Exception( "No such database class $class" );
		} elseif( !is_array( $schema ) ) {
			throw new Exception( '$schema is not a valid schema' );
		}
		else {
			return new $class( $schema );
		}
	}

	/**
	 * Top-level create method. Loops the tables and passes them to the child
	 * classes for implementation
	 * 
	 * @return boolean
	 */
	public function generateTables() {
		foreach( $this->tables as $name => $definition ) {
			$this->outputSql .= $this->defineTable( $name, $definition );
		}
		return $this->isOk;
	}

	/**
	 * Similar to generateTables(), but only generates SQL for tables that do not exist
	 *
	 * @param $db Database object
	 * @return boolean
	 */
	public function generateMissingTables( DatabaseBase $db ) {
		foreach( $this->tables as $name => $definition ) {
			if( $db->tableExists( $name ) ) {
				continue;
			}
			$this->outputSql .= $this->defineTable( $name, $definition );
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
	 * Set the prefix for all tables (unless they override this)
	 * 
	 * @param $opts String The options for all tables, like ENGINE=InnoDB
	 */
	public function setTableOptions( $opts ) {
		$this->tblOptions = $opts;
	}

	/**
	 * Get table options
	 * 
	 * @return String
	 */
	protected function getTableOptions() {
		return $this->tblOptions;
	}

	/**
	 * Given an abstract table definition, return a DBMS-specific command to
	 * create it. All child classes need to implement this
	 * @param $name The name of the table, like 'page' or 'revision'
	 * @param $definition Array An abstract table definition
	 * @return String
	 */
	abstract protected function defineTable( $name, $definition );

	/**
	 * Given an abstract field definition, return a DBMS-specific definition.
	 * All child classes need to implement this
	 * @param $attribs Array An abstract table definition
	 * @return String
	 */
	abstract protected function getFieldDefinition( $attribs );
}

class MysqlSchema extends SchemaBuilder {
	/**
	 * @see SchemaBuilder::getFieldDefinition(
	 */
	protected function defineTable( $name, $def ) {
		$prefix = $def['prefix'] ? $def['prefix'] . '_' : '';
		$tblName = $this->tblPrefix . $name;
		$sql = "CREATE TABLE `$tblName` (";
		foreach( $def['fields'] as $field => $attribs ) {
			$sql .= "\n\t{$prefix}{$field} " . $this->getFieldDefinition( $attribs );
		}
		$sql = rtrim( $sql, ',' );
		$sql .= "\n) " . $this->getTableOptions() . ";\n";
		if( isset( $def['indexes'] ) ) {
			foreach( $def['indexes'] as $idx => $idxDef ) {
				if( $idxDef[0] === 'UNIQUE' ) {
					array_shift( $idxDef );
					$sql .= "CREATE UNIQUE INDEX ";
				} elseif( $idxDef[0] == 'FULLTEXT' ) {
					array_shift( $idxDef );
					$sql .= "CREATE FULLTEXT INDEX ";
				} else {
					$sql .= "CREATE INDEX ";
				}
				$sql .= "{$prefix}{$idx} ON $tblName (";
				foreach( $idxDef as $col ) {
					$sql .= "{$prefix}{$col},";
				}
				$sql = rtrim( $sql, ',' );
				$sql .= ");\n";
			}
		}
		return $sql . "\n";
	}

	/**
	 * @see SchemaBuilder::getFieldDefinition()
	 */
	protected function getFieldDefinition( $attribs ) {
		if( !isset( $attribs['type'] ) ) {
			$this->isOk = false;
			throw new Exception( "No type specified for field" );
		}
		$fieldType = $attribs['type'];
		$def = '';
		switch( $fieldType ) {
			case Schema::TYPE_INT:
				$def = 'int';
				if( isset( $attribs['length'] ) ) {
					$def = $attribs['length'] . $def;
				}
				if( isset( $attribs['signed'] ) ) {
					$def .= $attribs['signed'] ? ' signed' : ' unsigned';
				}
				break;
			case Schema::TYPE_VARCHAR:
				$def = 'varchar(' . $attribs['length'] . ')';
				break;
			case Schema::TYPE_DATETIME:
				$def = 'binary(14)';
				break;
			case Schema::TYPE_TEXT:
				$def = 'text';
				if( isset( $attribs['length'] ) ) {
					$def = $attribs['length'] . $def;
				}
				break;
			case Schema::TYPE_BLOB:
				$def = 'blob';
				if( isset( $attribs['length'] ) ) {
					$def = $attribs['length'] . $def;
				}
				break;
			case Schema::TYPE_BINARY:
				$def = 'binary(' . $attribs['length'] . ')';
				break;
			case Schema::TYPE_VARBINARY:
				$def = 'varbinary(' . $attribs['length'] . ')';
				break;
			case Schema::TYPE_BOOL:
				$def = 'bool';
				break;
			case Schema::TYPE_ENUM:
				$def = 'ENUM("' . implode( '", "', $attribs['values'] );
				$def = rtrim( $def, ', "' ) . '")';
				break;
			default:
				$this->isOk = false;
		}
		if( isset( $attribs['binary'] ) && $attribs['binary'] ) {
			$def = $def . ' binary';
		}
		if( isset( $attribs['null'] ) ) {
				$def .= $attribs['null'] ? ' NULL ' : ' NOT NULL';
		}
		// Use array_key_exists() since 'default' might be set to null
		if( array_key_exists( 'default', $attribs ) ) {
			if( $attribs['default'] === null ) {
				$def .= ' default NULL';
			} else {
				$def .= " default '" . $attribs['default'] . "'";
			}
		}
		if( isset( $attribs['primary-key'] ) && $attribs['primary-key'] ) {
			$def .= " PRIMARY KEY";
		}
		if( isset( $attribs['auto-increment'] ) && $attribs['auto-increment'] ) {
			$def .= " AUTO_INCREMENT";
		}
		return $def . ",";
	}
}
