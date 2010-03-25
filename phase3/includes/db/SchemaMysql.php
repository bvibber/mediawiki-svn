<?php

class SchemaMysql extends SchemaBuilder {
	/**
	 * BLOB or TEXT fields on MySQL require a length to be specified in
	 * index declarations, otherwise CREATE INDEX will fail with error 1170.
	 */
	var $indexLengths = array(
		'el_from' => array(
			'el_to' => 40,
		),
		'el_to' => array(
			'el_to' => 60,
		),
		'el_index' => array(
			'el_index' => 60,
		),
		'ipb_address' => array(
			'ipb_address' => 255,
		),
		'ipb_range' => array(
			'ipb_range_start' => 8,
			'ipb_range_end' => 8,
		),
		'oi_name_archive_name' => array(
			'oi_name_archive_name' => 14,
		),
		'job_cmd' => array(
			'job_params' => 128,
		),
	);

	public function getType() {
		return 'mysql';
	}

	protected function adjustTablesForDatabase() {
		$this->tables['searchindex'] = array(
			'prefix' => 'si',
			'fields' => array(
				'page' => array(
					'type'   => 'int',
					'signed' => false,
					'null'   => false,
				),
				'title' => array(
					'type'    => 'varchar',
					'length'  => 255,
					'null'    => false,
					'default' => '',
				),
				'text' => array(
					'type'   => 'text',
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
			'options' => array(
				'engine' => 'MyISAM',
			),
		);

		$this->tables['revision']['options'] = array(
			'max_rows' => 10000000,
			'avg_row_length' => 1024,
		);

		$this->tables['text']['options'] = array(
			'max_rows' => 10000000,
			'avg_row_length' => 10240,
		);

		$this->tables['hitcounter']['options'] = array(
			'max_rows' => 25000,
			'engine' => 'HEAP',
		);
	}

	/**
	 * @see SchemaBuilder::createTable()
	 */
	protected function createTable( $name, $def ) {
		$prefix = $def['prefix'] ? $def['prefix'] . '_' : '';
		$tblName = $this->tblPrefix . $name;
		$opts = isset( $def['options'] ) ? $def['options'] : array();
		$sql = "CREATE TABLE `$tblName` (";
		foreach( $def['fields'] as $field => $attribs ) {
			$sql .= "\n\t{$prefix}{$field} " . $this->getFieldDefinition( $attribs );
		}
		$sql = rtrim( $sql, ',' );
		$sql .= "\n) " . $this->getTableOptions( $opts ) . ";\n";
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
				$sql .= "{$this->tblPrefix}{$idx} ON $tblName (";
				foreach( $idxDef as $col ) {
					$field = "{$prefix}{$col}";
					if ( isset( $this->indexLengths[$idx] ) && isset( $this->indexLengths[$idx][$field] ) ) {
						$field .= "({$this->indexLengths[$idx][$field]})";
					}
					$sql .= "$field, ";
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
		if( !isset( $attribs['type'] ) ) {
			$this->isOk = false;
			throw new Exception( "No type specified for field" );
		}
		$fieldType = $attribs['type'];
		$def = '';
		switch( $fieldType ) {
			case 'int':
				$def = 'int';
				if( isset( $attribs['length'] ) ) {
					$def = $attribs['length'] . $def;
				}
				break;
			case 'varchar':
				$def = 'varchar(' . $attribs['length'] . ')';
				break;
			case 'char':
				$def = 'char(' . $attribs['length'] . ')';
				break;
			case 'datetime':
				$def = 'binary(14)';
				break;
			case 'text':
				$def = 'text';
				if( isset( $attribs['length'] ) ) {
					$def = $attribs['length'] . $def;
				}
				break;
			case 'blob':
				$def = 'blob';
				if( isset( $attribs['length'] ) ) {
					$def = $attribs['length'] . $def;
				}
				break;
			case 'binary':
				$def = 'binary(' . $attribs['length'] . ')';
				break;
			case 'varbinary':
				$def = 'varbinary(' . $attribs['length'] . ')';
				break;
			case 'bool':
				$def = 'bool';
				break;
			case 'enum':
				$def = 'ENUM("' . implode( '", "', $attribs['values'] );
				$def = rtrim( $def, ', "' ) . '")';
				break;
			case 'float':
				$def = 'float';
				break;
			case 'real':
				$def = 'real';
				break;
			default:
				$this->isOk = false;
		}
		if( isset( $attribs['signed'] ) ) {
			$def .= $attribs['signed'] ? ' signed' : ' unsigned';
		}
		if( isset( $attribs['binary'] ) && $attribs['binary'] ) {
			$def = $def . ' binary';
		}
		if( isset( $attribs['null'] ) ) {
				$def .= $attribs['null'] ? ' NULL' : ' NOT NULL';
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

	private function getTableOptions( $opts ) {
		$opts = array_merge( $this->tblOptions, $opts );
		$ret = array();
		foreach( $opts as $name => $value ) {
			$ret[] = strtoupper( $name ) . "=$value";
		}
		return implode( ', ', $ret );
	}

	protected function updateTable( $name, $definition, $db ) {
		return '';
	}
}
