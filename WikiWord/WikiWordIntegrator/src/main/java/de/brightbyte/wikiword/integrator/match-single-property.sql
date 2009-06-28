SELECT foreign_authority, foreign_id, 
	concept, concept_name, 
	F.property as foreign_property, P.property as concept_property,
	F.value as value,
	count(*) as concept_property_freq
FROM /* wikiword_prefix *//* foreign_properties_table */ as F
JOIN /* wikiword_prefix */property as P
ON F.value = P.value
AND F.foreign_authority = "/* foreign_authority_name */"
AND F.property = "/* foreign_property_name */"
AND P.property = "/* concept_property_name */"
GROUP BY foreign_authority, foreign_id, concept, F.value;