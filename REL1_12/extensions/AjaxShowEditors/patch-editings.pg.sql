BEGIN;
SET client_min_messages = 'ERROR';
DROP TABLE /*$wgDBprefix*/editings cascade;
CREATE TABLE /*$wgDBprefix*/editings (
  editings_page    INTEGER NOT NULL,
  editings_user    TEXT    NOT NULL,
  editings_started TIMESTAMP NOT NULL,
  editings_touched TIMESTAMP NOT NULL
);
ALTER TABLE /*$wgDBprefix*/editings 
      ADD CONSTRAINT editings_pk 
          PRIMARY KEY (editings_page,editings_user);
CREATE INDEX editings_page_key 
      ON /*$wgDBprefix*/editings (editings_page);
CREATE INDEX editings_page_started_key 
      ON /*$wgDBprefix*/editings (editings_page,editings_user,editings_started);
COMMIT;
