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
