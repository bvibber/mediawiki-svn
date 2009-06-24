SELECT M.*, D.definition 
FROM /* wikiword_mapping_table */ as M
JOIN /* wikiword_prefix */definition as D
ON M.concept = D.concept;