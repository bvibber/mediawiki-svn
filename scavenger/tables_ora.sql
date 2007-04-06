CREATE SEQUENCE users_user_id_seq;
CREATE TABLE users (
  user_id	INT PRIMARY KEY,
  user_anon	INT NOT NULL,
  user_name	VARCHAR(32) UNIQUE
);

CREATE SEQUENCE page_page_id_seq;
CREATE TABLE page (
  page_id	INT PRIMARY KEY,
  page_title	VARCHAR(255) NOT NULL UNIQUE,
  page_key	VARCHAR(255) NOT NULL UNIQUE,
  page_latest	INT DEFAULT NULL
);

CREATE SEQUENCE text_text_id_seq;
CREATE TABLE text (
  text_id	INT PRIMARY KEY,
  text_content	CLOB
);

CREATE SEQUENCE revision_rev_id_seq;
CREATE TABLE revision (
  rev_id	INT PRIMARY KEY,
  rev_page	INT NOT NULL REFERENCES page(page_id) ON DELETE CASCADE,
  rev_text_id	INT NOT NULL REFERENCES text(text_id) ON DELETE CASCADE,
  rev_timestamp	TIMESTAMP WITH TIME ZONE NOT NULL,
  rev_comment	VARCHAR(255),
  rev_user	INT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE
);

ALTER TABLE page ADD FOREIGN KEY(page_latest) REFERENCES revision(rev_id) ON DELETE CASCADE;
