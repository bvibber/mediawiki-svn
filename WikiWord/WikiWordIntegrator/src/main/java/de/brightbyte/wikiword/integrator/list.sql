-- This script reduces a table of mappings to those which contain
-- only one member per group. The grouping criteria are provided by an option.
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_mapping_table: the mapping table to process
--  * list_fields: the fields to group by. Optional, defaults to * (all)
--  * list_order: order clause. Optional. Must start with "ORDER BY" if given.
--  * list_limit: limit clause. Optional. Must start with "LIMIT" if given.

SELECT /* list_fields | * */ FROM /* wikiword_prefix *//* wikiword_mapping_table */ /* list_order */ /* list_limit */;