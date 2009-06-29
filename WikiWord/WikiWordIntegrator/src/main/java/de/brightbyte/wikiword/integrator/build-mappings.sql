-- This script groups associations by foreign concept id and target concept id,
-- this providing a list of candidate mappings.
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_mapping_table: the mapping table to process

SELECT foreign_authority, foreign_id, foreign_name, concept, concept_name, 
	sum(weight) as weight, 
	group_concat(concat(concept_property, "=", value) separator "|") as annotation
FROM /* wikiword_prefix *//* wikiword_mapping_table */ 
GROUP BY foreign_authority, foreign_id, concept;