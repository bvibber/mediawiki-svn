-- 
-- SQL for SacredText Extension
-- 
-- Table for SacredText verses
DROP TABLE IF EXISTS /*_*/sacredtext_verses;
CREATE TABLE /*_*/sacredtext_verses (
    -- Primary key
    st_verse_index int(11) NOT NULL auto_increment,
    -- Religious text
    st_religious_text ENUM('Christian Bible', 'Hebrew Bible', 'Quran') NOT NULL default 'Christian Bible',
    -- Book (ex. John)
    st_book VARCHAR(255) NOT NULL,
    -- Chapter
    st_chapter_num SMALLINT NOT NULL,
    -- Verse
    st_verse_num SMALLINT NOT NULL,
    -- Language
    st_language CHAR(2) default 'en' COLLATE utf8_unicode_ci NOT NULL,
    -- Translation
    st_translation VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
    -- Actual text
    st_text TEXT character set utf8 collate utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`st_verse_index`)
) /*$wgDBTableOptions*/;
