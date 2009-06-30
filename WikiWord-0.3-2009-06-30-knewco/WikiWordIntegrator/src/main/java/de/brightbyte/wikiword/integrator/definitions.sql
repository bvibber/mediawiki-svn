-- This script attaches definitions to each entry of a mapping table,
-- if a definition is known.
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_mapping_table: the mapping table to process

SELECT M.*, D.definition 
FROM /* wikiword_prefix *//* wikiword_mapping_table */ as M
JOIN /* wikiword_prefix */definition as D
ON M.concept = D.concept;