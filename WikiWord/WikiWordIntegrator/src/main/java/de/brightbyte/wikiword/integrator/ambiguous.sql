SELECT foreign_authority, foreign_id, foreign_name,
COUNT(distinct concept) as c, group_concat(concept_name separator "|") as concept_names
FROM /* wikiword_mapping_table */ as M
GROUP BY foreign_authority, foreign_id, concept
HAVING c>1