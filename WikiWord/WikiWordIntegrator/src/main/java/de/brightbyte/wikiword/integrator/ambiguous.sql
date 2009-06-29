-- This script lists all ambiguous mappings, that is, all mappings that map a single foreign concept
-- to multiple WikiWord concepts
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_mapping_table: the mapping table to process

SELECT foreign_authority, foreign_id, foreign_name,
COUNT(distinct concept) as c, group_concat(concept_name separator "|") as concept_names
FROM /* wikiword_prefix *//* wikiword_mapping_table */ as M
GROUP BY foreign_authority, foreign_id, concept
HAVING c>1