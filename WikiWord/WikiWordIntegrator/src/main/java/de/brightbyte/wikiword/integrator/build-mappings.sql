SELECT foreign_authority, foreign_id, foreign_name, concept, concept_name, 
	sum(weight) as weight, 
	group_concat(concat(concept_property, "=", value) separator "|") as annotation
FROM /* wikiword_prefix *//* wikiword_mapping_table */ 
GROUP BY foreign_authority, foreign_id, concept;