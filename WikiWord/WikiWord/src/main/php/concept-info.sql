SET SESSION group_concat_max_len = 262144; -- 1024*256

create table full_all_local_concept_info (
     concept int(11) NOT NULL,
     `lang` varbinary(10) NOT NULL,
     `name` varbinary(255) NOT NULL,
     `pages` MEDIUMBLOB DEFAULT NULL,
     `definition` MEDIUMBLOB DEFAULT NULL,
     `broader` MEDIUMBLOB DEFAULT NULL,
     `narrower` MEDIUMBLOB DEFAULT NULL,
     `similar` MEDIUMBLOB DEFAULT NULL,
     `related` MEDIUMBLOB DEFAULT NULL,
     PRIMARY KEY ( concept, lang )
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

insert into full_all_local_concept_info ( concept, lang, name ) 
select global_concept, lang, local_concept_name 
from full_all_origin;

update full_all_local_concept_info as I
join full_all_origin as O on I.lang = O.lang and I.concept = O.global_concept
join full_{lang}_definition as D on O.local_concept = D.concept and O.lang = "{lang}"
set I.definition = D.definition
where I.lang = "{lang}";

update full_all_local_concept_info as I
join ( select O.global_concept as concept, O.lang as lang, 
	      group_concat( concat(R.type, ":", R.name) separator "|" ) as pages 
      from full_all_origin as O 
      join full_{lang}_about as A on A.concept = O.local_concept and O.lang = "{lang}"
      join full_{lang}_resource as R on R.id = A.resource
      where O.lang = "{lang}" and R.type IN (10, 50)
      group by O.global_concept, O.lang
    ) as X
on I.concept = X.concept and I.lang = X.lang
set I.pages = X.pages
where I.lang = "{lang}";


update full_all_local_concept_info as I
join ( select narrow as concept, group_concat(concat(broad, ":", local_concept_name) separator "|") as broader from full_all_broader 
	join full_all_origin as O on O.global_concept = broad and O.lang = "{lang}"
	group by narrow ) as X
on X.concept = I.concept, I.lang = "{lang}"
set I.broader = X.broader;

update full_all_local_concept_info as I
join ( select broad as concept, group_concat(concat(narrow, ":", local_concept_name) separator "|") as narrower from full_all_broader 
	join full_all_origin as O on O.global_concept = narrow and O.lang = "{lang}"
	group by broad ) as X
on X.concept = I.concept, I.lang = "{lang}"
set I.narrower = X.narrower;

update full_all_local_concept_info as I
join ( select concept1 as concept, group_concat(concat(concept2, ":", local_concept_name) separator "|") as similar from full_all_relation
	join full_all_origin as O on O.global_concept = concept2 and O.lang = "{lang}"
	where langmatch >= 1 or langref >= 1
	group by concept1 ) as X
on X.concept = I.concept, I.lang = "{lang}"
set I.similar = X.similar;

update full_all_local_concept_info as I
join ( select concept1 as concept, group_concat(concat(concept2, ":", local_concept_name) separator "|") as related from full_all_relation
	join full_all_origin as O on O.global_concept = concept2 and O.lang = "{lang}"
	where bilink >= 1
	group by concept1 ) as X
on X.concept = I.concept, I.lang = "{lang}"
set I.related = X.related;

