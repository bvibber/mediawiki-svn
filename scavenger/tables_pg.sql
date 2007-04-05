CREATE TABLE users (
  user_id	SERIAL PRIMARY KEY,
  user_anon	INT NOT NULL,
  user_name	VARCHAR(32) UNIQUE
);

CREATE TABLE page (
  page_id	SERIAL PRIMARY KEY,
  page_title	VARCHAR(255) NOT NULL UNIQUE,
  page_latest	INT DEFAULT NULL
);

CREATE TABLE text (
  text_id	SERIAL PRIMARY KEY,
  text_content	TEXT
);

CREATE TABLE revision (
  rev_id	SERIAL PRIMARY KEY,
  rev_page	INT NOT NULL REFERENCES page(page_id) ON DELETE CASCADE,
  rev_text_id	INT NOT NULL REFERENCES text(text_id) ON DELETE CASCADE,
  rev_timestamp	TIMESTAMP WITH TIME ZONE NOT NULL,
  rev_comment	VARCHAR(255) NOT NULL,
  rev_user	INT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE
);

ALTER TABLE page ADD FOREIGN KEY(page_latest) REFERENCES revision(rev_id) ON DELETE CASCADE;
