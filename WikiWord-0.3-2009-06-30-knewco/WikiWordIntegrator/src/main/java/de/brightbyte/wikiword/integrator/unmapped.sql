-- List the entries from a foreign property table which are not mapped by
-- a given mapping table.
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_foreign_table: the foreign property table to process
--  * wikiword_mapping_table: the mapping table to match against

SELECT F.foreign_authority, F.foreign_id
FROM /* wikiword_prefix *//* wikiword_mapping_table */ as M
RIGHT JOIN /* wikiword_prefix *//* wikiword_foreign_table */ as F
ON M.foreign_authority = F.foreign_authority
AND M.foreign_id = F.foreign_id
WHERE M.foreign_id IS NULL
GROUP BY foreign_authority, foreign_id