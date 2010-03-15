-- collect definitions
insert into {collection}_{thesaurus}_search_index ( 
     concept, concept_name, `type`,
     `lang`, `term`, `score`, `norm` )
select O.global_concept, M.concept_name, C.type, "{lang}", 
      REPLACE( LCASE( CAST(M.term_text as CHAR CHARACTER SET utf8) COLLATE utf8_general_ci ), "-", "" ), 
      M.rule * M.freq, 1
from {collection}_{lang}_meaning as M
join {collection}_{thesaurus}_origin as O on O.lang = "{lang}" and O.local_concept = M.concept
join {collection}_{thesaurus}_concept as C on C.id = O.global_concept
where (M.rule not in (10, 30) OR M.freq > 1) and C.type > 0
on duplicate key update 
  score = if (score > values(score), score, values(score)),
  norm = if (norm < values(norm), score, values(norm));

-- FIXME: normalization levels! 0=none, 1=case-and-dash (+translit?), 2=whitespace-and-punctuation, 4=soundex