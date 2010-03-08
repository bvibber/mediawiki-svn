<?php

class SchemaOracle extends SchemaBuilder {

	public function getType() {
		return 'oracle';
	}

	protected function adjustTablesForDatabase() {
		$this->tables['searchindex'] = array(
			'prefix' => 'si',
			'fields' => array(
				'page' => array(
					'type'   => 'int',
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
					'null'   => false,
				),
			),
			'indexes' => array(
				'si_page' => array(
					'UNIQUE', 'page',
				),
				'si_title' => array(
					'CTXSYS.CONTEXT', 'title',
				),
				'si_text' => array(
					'CTXSYS.CONTEXT', 'text',
				),
			),
		);
	}

	protected function createTable( $name, $def ) {
$prefix = $def['prefix'] ? $def['prefix'] . '_' : '';
$tblName = $this->tblPrefix . $name;

$sql = "CREATE TABLE $tblName (";
foreach( $def['fields'] as $field => $attribs ) {
$sql .= "\n\t{$prefix}{$field} " . $this->getFieldDefinition( $attribs );
}
$sql = rtrim( $sql, ',' );
$sql .= ");\n";

		$idx_i = 0;
		$idx_u = 0;
if( isset( $def['indexes'] ) ) {
foreach( $def['indexes'] as $idx => $idxDef ) {
$idxType = '';
if (isset($idxDef[1])) {
					$idxType = array_shift( $idxDef );
				}

if( $idxType === 'UNIQUE' ) {
$sql .= "CREATE UNIQUE INDEX {$tblName}_u".
						str_pad(++$idx_u, 2, "0", STR_PAD_LEFT).
						" ON {$tblName} (";
} elseif ($idxType !== '') {
$sql .= "CREATE INDEX {$this->tblPrefix}{$idx} ON {$tblName} (";
} else {
$sql .= "CREATE INDEX {$tblName}_u".
						str_pad(++$idx_i, 2, "0", STR_PAD_LEFT).
						" ON {$tblName} (";
				}

foreach( $idxDef as $col ) {
$sql .= "{$prefix}{$col}, ";
}
$sql = rtrim( $sql, ', ' );
$sql .= ");\n";
}
}
return $sql . "\n";
}

	protected function updateTable( $name, $definition, $db ) {}

}
