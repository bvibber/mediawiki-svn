-- Searchindex table definition for cases when no full-text search SQLite module is present
-- (currently, only FTS3 is supported).
-- Use it if you are moving your database from environment with FTS support
-- to environment without it.

DROP TABLE IF EXISTS /*_*/searchindex;
CREATE TABLE /*_*/searchindex (
  -- Key to page_id
  si_page INTEGER NOT NULL,

  -- Munged version of title
  si_title TEXT,
  
  -- Munged version of body text
  si_text TEXT
);