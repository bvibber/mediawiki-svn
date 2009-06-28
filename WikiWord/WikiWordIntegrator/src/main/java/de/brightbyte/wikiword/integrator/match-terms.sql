SELECT foreign_authority, foreign_id, 
	concept, concept_name, 
	F.property as foreign_property, "_TERM_" as concept_property,
	M.term_text, M.rule,	M.freq, M.rule * M.freq as weight
FROM /* wikiword_prefix *//* foreign_properties_table */ as F
JOIN /* wikiword_prefix */meaning as M
ON F.value = M.term_text
AND F.foreign_authority = "/* foreign_authority_name */"
AND F.property = "/* foreign_property_name */"
GROUP BY foreign_authority, foreign_id, concept, F.value;