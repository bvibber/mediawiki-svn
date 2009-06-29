-- This script reduces a table of mappings to those which contain
-- only one member per group. The grouping criteria are provided by an option.
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_mapping_table: the mapping table to process
--  * group_fields: the fields to group by

SELECT * FROM /* wikiword_prefix *//* wikiword_mapping_table */
GROUP BY /* group_fields */
HAVING count(*) = 1
ORDER BY /* group_fields */;