-- This script expands a list of concepts using it's subordinates (narrower concepts) an related concepts (bi-link and langmatch)
--
-- Parameters:
--  * wikiword_prefix: the global table prefix. Provided automatically
--  * wikiword_concept_table: the concept table to use as a basis. Optional, defaults to "concept".
--  * wikiword_target_table: the new table to fill
--  * concept_id_field: column containing concept IDs. Optional, defaults to "id".

-- copy table
create table if not exists /* wikiword_prefix *//* wikiword_target_table */ 
like /* wikiword_prefix *//* wikiword_concept_table | concept */;

insert ignore into /* wikiword_prefix *//* wikiword_target_table */ 
select * from /* wikiword_prefix *//* wikiword_concept_table | concept */;

-- insert narrower
insert ignore into /* wikiword_prefix *//* wikiword_concept_table | concept */ 
select C.*
from /* wikiword_prefix *//* wikiword_concept_table | concept */ as O
join /* wikiword_prefix */broader as R on O./* concept_id_field | id */ = R.broad
join /* wikiword_prefix *//* wikiword_concept_table | concept */ as C on C./* concept_id_field | id */ = R.narrow;

/*exclude people, places, names, times and numbers */
/* and C.type not in (10, 20, 40, 50, 60); */

-- insert related
insert ignore into /* wikiword_prefix *//* wikiword_concept_table | concept */ 
select C.*
from /* wikiword_prefix *//* wikiword_concept_table | concept */ as O
join /* wikiword_prefix */relation as R on O./* concept_id_field | id */ = R.concept1
join /* wikiword_prefix *//* wikiword_concept_table | concept */ as C on C./* concept_id_field | id */ = R.concept2
where (bilink>0 or langmatch>0);