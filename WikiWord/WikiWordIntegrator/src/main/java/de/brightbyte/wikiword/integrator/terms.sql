SELECT M.*, T.term_text, T.freq, T.rule 
FROM /* wikiword_prefix *//* wikiword_mapping_table */ as M
JOIN /* wikiword_prefix */meaning as T
ON M.concept = T.concept;
