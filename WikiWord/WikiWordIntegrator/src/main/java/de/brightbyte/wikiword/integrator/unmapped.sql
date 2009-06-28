SELECT F.foreign_authority, F.foreign_id
FROM /* wikiword_prefix *//* wikiword_mapping_table */ as M
RIGHT JOIN /* wikiword_prefix *//* wikiword_foreign_table */ as F
ON M.foreign_authority = F.foreign_authority
AND M.foreign_id = F.foreign_id
WHERE M.foreign_id IS NULL
GROUP BY foreign_authority, foreign_id