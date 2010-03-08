<?php

class SchemaSqlite extends SchemaBuilder {
	static $typeMapping = array(
		'int'       => 'INTEGER',
		'varchar'   => 'TEXT',
		'datetime'  => 'TEXT',
		'text'      => 'TEXT',
		'blob'      => 'BLOB',
		'binary'    => 'BLOB',
		'varbinary' => 'BLOB',
		'bool'      => 'INTEGER',
		'enum'      => 'BLOB',
		'float'     => 'REAL',
		'real'      => 'REAL',
		'char'      => 'TEXT',
		'none'      => '',
	);

	public function getType() {
		return 'sqlite';
	}

	/**
	 * @todo: update updatelog with fts3
	 */
	protected function adjustTablesForDatabase() {
		$db = new DatabaseSqliteStandalone( ':memory:' );
		if ( $db->getFulltextSearchModule() == 'FTS3' ) {
			$this->tables['searchindex'] = array(
				'prefix' => 'si',
				'virtual' => 'FTS3',
				'fields' => array(
					'title' => array(
						'type' => 'none',
					),
					'text' => array(
						'type' => 'none',
					),
				)
			);
		} else {
			$this->tables['searchindex'] = array(
				'prefix' => 'si',
				'fields' => array(
					'title' => array(
						'type' => 'text',
					),
					'text' => array(
						'type' => 'text',
					),
				)
			);
			$this->tablesToDelete = array_merge( $this->tablesToDelete,
				array( 'searchindex_content', 'searchindex_segdir', 'searchindex_segments' )
			);
		}
		$db->close();
	}

	protected function createTable( $name, $def ) {
		$prefix = $def['prefix'] ? $def['prefix'] . '_' : '';
		$tblName = $this->tblPrefix . $name;
		$virtual = isset ( $def['virtual'] ) ? $def['virtual'] : false;
		if ( $virtual ) {
			$sql = "CREATE VIRTUAL TABLE `$tblName` USING $virtual (";
		} else {
			$sql = "CREATE TABLE `$tblName` (";
		}
		foreach( $def['fields'] as $field => $attribs ) {
			$sql .= "\n\t{$prefix}{$field} " . $this->getFieldDefinition( $attribs );
		}
		$sql = rtrim( $sql, ',' );
		$sql .= "\n);\n";
		if( isset( $def['indexes'] ) ) {
			foreach( $def['indexes'] as $idx => $idxDef ) {
				if( $idxDef[0] === 'UNIQUE' ) {
					array_shift( $idxDef );
					$sql .= "CREATE UNIQUE INDEX ";
				} elseif( $idxDef[0] == 'FULLTEXT' ) {
					continue; // no thanks
				} else {
					$sql .= "CREATE INDEX ";
				}
				$sql .= "{$this->tblPrefix}{$idx} ON $tblName (";
				foreach( $idxDef as $col ) {
					$sql .= "{$prefix}{$col}, ";
				}
				$sql = rtrim( $sql, ', ' );
				$sql .= ");\n";
			}
		}
		return $sql . "\n";
	}

	/**
	 * Given an abstract field definition, return a MySQL-specific definition.
	 * @param $attribs Array An abstract table definition
	 * @return String
	 */
	private function getFieldDefinition( $attribs ) {
		$type = $attribs['type'];
		if ( !isset( self::$typeMapping[$type] ) ) {
			throw new MWException( "Unknown type $type" );
		}
		$def = self::$typeMapping[$type];
		if( isset( $attribs['null'] ) ) {
				$def .= $attribs['null'] ? ' NULL' : ' NOT NULL';
		}
		// Use array_key_exists() since 'default' might be set to null
		if( array_key_exists( 'default', $attribs ) ) {
			if( $attribs['default'] === null ) {
				$def .= ' default NULL';
			} else {
				$def .= " DEFAULT '" . $attribs['default'] . "'";
			}
		}		if( isset( $attribs['primary-key'] ) && $attribs['primary-key'] ) {
			$def .= ' PRIMARY KEY';
		}
		if( isset( $attribs['auto-increment'] ) && $attribs['auto-increment'] ) {
			$def .= ' AUTOINCREMENT';
		}
		return $def . ',';
	}

	protected function updateTable( $name, $definition, $db ) {
		return '';
	}
}
