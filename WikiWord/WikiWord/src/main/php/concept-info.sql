SET SESSION group_concat_max_len = 262144; -- 1024*256

create table if not exists {collection}_{thesaurus}_concept_info (
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

truncate {collection}_{thesaurus}_concept_info;

insert into {collection}_{thesaurus}_concept_info ( concept, lang, name ) 
select global_concept, lang, local_concept_name 
from {collection}_{thesaurus}_origin;
