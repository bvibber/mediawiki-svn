create table if not exists {collection}_{thesaurus}_resource_index (
     concept int(11) NOT NULL,
     resources MEDIUMBLOB NOT NULL,
     PRIMARY KEY ( concept )
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

truncate {collection}_{thesaurus}_resource_index;

-- collect ressources in all languages
insert into {collection}_{thesaurus}_resource_index ( concept, resources )
select concept, group_concat(distinct concat(type, ":", lang, ":", local_resource_name) separator "|" ) as resources 
from {collection}_{thesaurus}_about as A
where type > 0;
