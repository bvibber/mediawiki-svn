-- This script attaches terms (synonyms) to each entry of a mapping table.
-- Mapping rows will be duplicated for each term.
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_mapping_table: the mapping table to process

SELECT M.*, T.term_text, T.freq, T.rule 
FROM /* wikiword_prefix *//* wikiword_mapping_table */ as M
JOIN /* wikiword_prefix */meaning as T
ON M.concept = T.concept;
