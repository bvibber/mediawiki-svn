SET SESSION group_concat_max_len = 262144; -- 1024*256

-- collect definitions
update {collection}_{thesaurus}_concept_info as I
join {collection}_{thesaurus}_origin as O on I.lang = O.lang and I.concept = O.global_concept
join {collection}_{lang}_definition as D on O.local_concept = D.concept and O.lang = "{lang}"
set I.definition = D.definition
where I.lang = "{lang}";

-- collect wiki pages
update {collection}_{thesaurus}_concept_info as I
join ( select O.global_concept as concept, O.lang as lang, 
	      group_concat(distinct concat(R.type, ":", R.name) separator "|" ) as pages 
      from {collection}_{thesaurus}_origin as O 
      join {collection}_{lang}_about as A on A.concept = O.local_concept and O.lang = "{lang}"
      join {collection}_{lang}_resource as R on R.id = A.resource
      where O.lang = "{lang}" and R.type IN (10, 50)
      group by O.global_concept, O.lang
    ) as X
on I.concept = X.concept and I.lang = X.lang
set I.pages = X.pages
where I.lang = "{lang}";

-- collect broader concepts
update {collection}_{thesaurus}_concept_info as I
join ( select narrow as concept, group_concat(distinct concat(broad, ":", if (local_concept_name is null, "", local_concept_name)) separator "|") as broader 
from {collection}_{thesaurus}_broader 
	left join {collection}_{thesaurus}_origin as O on O.global_concept = broad and O.lang = "{lang}"
	group by narrow ) as X
on X.concept = I.concept and I.lang = "{lang}"
set I.broader = X.broader;

-- collect narrower concepts
update {collection}_{thesaurus}_concept_info as I
join ( select broad as concept, group_concat(distinct concat(narrow, ":", if (local_concept_name is null, "", local_concept_name)) separator "|") as narrower 
from {collection}_{thesaurus}_broader 
	left join {collection}_{thesaurus}_origin as O on O.global_concept = narrow and O.lang = "{lang}"
	group by broad ) as X
on X.concept = I.concept and I.lang = "{lang}"
set I.narrower = X.narrower;

-- collect similar concepts
update {collection}_{thesaurus}_concept_info as I
join ( select concept1 as concept, group_concat(distinct concat(concept2, ":", if (local_concept_name is null, "", local_concept_name)) separator "|") as similar 
from {collection}_{thesaurus}_relation
	left join {collection}_{thesaurus}_origin as O on O.global_concept = concept2 and O.lang = "{lang}"
	where langmatch >= 1 or langref >= 1
	group by concept1 ) as X
on X.concept = I.concept and I.lang = "{lang}"
set I.similar = X.similar;

-- collect related concepts
update {collection}_{thesaurus}_concept_info as I
join ( select concept1 as concept, group_concat(distinct concat(concept2, ":", if (local_concept_name is null, "", local_concept_name)) separator "|") as related 
from {collection}_{thesaurus}_relation
	left join {collection}_{thesaurus}_origin as O on O.global_concept = concept2 and O.lang = "{lang}"
	where bilink >= 1
	group by concept1 ) as X
on X.concept = I.concept and I.lang = "{lang}"
set I.related = X.related;

-- collect features
update {collection}_{thesaurus}_concept_info as I
join ( select concept1 as concept, group_concat(distinct concat(concept2, ":", if (local_concept_name is null, "", local_concept_name)) separator "|") as related 
from {collection}_{thesaurus}_features
	left join {collection}_{thesaurus}_origin as O on O.global_concept = concept2 and O.lang = "{lang}"
	where bilink >= 1
	group by concept1 ) as X
on X.concept = I.concept and I.lang = "{lang}"
set I.features = X.features;

