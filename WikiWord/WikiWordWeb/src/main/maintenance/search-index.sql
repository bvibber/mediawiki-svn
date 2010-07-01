create table if not exists {collection}_{thesaurus}_search_index (
     concept int(11) NOT NULL,
     concept_name varbinary(255) NOT NULL,
     type int(11) NOT NULL,
     `lang` varbinary(10) NOT NULL,
     `term` varchar(255) character set utf8 collate utf8_general_ci NOT NULL,
     `score` int NOT NULL,
     `norm` int NOT NULL,
     PRIMARY KEY ( lang, term, concept ),
     KEY ( concept, lang )
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

truncate {collection}_{thesaurus}_search_index;

-- collect definitions
insert into {collection}_{thesaurus}_search_index ( 
     concept, concept_name, `type`, `lang`, `term`, `score`, `norm` )
select C.id, O.local_concept_name, C.type, M.lang,  
      REPLACE( LCASE( CAST(M.term_text as CHAR CHARACTER SET utf8) COLLATE utf8_general_ci ), "-", "" ), 
      M.rule * M.freq, 1
from {collection}_{thesaurus}_meaning as M
join {collection}_{thesaurus}_concept as C on C.id = M.concept
join {collection}_{thesaurus}_origin as O on O.global_concept = M.concept and O.lang = M.lang -- FIXME: remove this once the global menaing table contains the local concept name
where (M.rule not in (10, 30) OR M.freq > 1) and C.type > 0
on duplicate key update 
  score = if (score > values(score), score, values(score)),
  norm = if (norm < values(norm), score, values(norm));

-- FIXME: normalization levels! 0=none, 1=case-and-dash (+translit?), 2=whitespace-and-punctuation, 4=soundex