DROP TABLE IF EXISTS sites;
CREATE TABLE sites (
	si_id		INTEGER PRIMARY KEY AUTO_INCREMENT,
	si_name		VARCHAR(64) NOT NULL
);
CREATE INDEX site_name_idx ON sites(si_name);

DROP TABLE IF EXISTS url_id;
CREATE TABLE url_id (
	ur_id		INTEGER PRIMARY KEY AUTO_INCREMENT,
	ur_site		INTEGER NOT NULL REFERENCES sites(si_id) ON DELETE CASCADE,
	ur_path		VARCHAR(255) NOT NULL,
	ur_grouped	TINYINT(1) NOT NULL
);
CREATE INDEX url_url_idx ON url_id(ur_site, ur_path);

DROP TABLE IF EXISTS url_count;
CREATE TABLE url_count (
	uc_url_id	INTEGER PRIMARY KEY REFERENCES url_id(ur_id) ON DELETE CASCADE,
	uc_count	INTEGER NOT NULL
);
CREATE INDEX url_uc_count_idx ON url_count(uc_count);

DROP TABLE IF EXISTS url_touched;
CREATE TABLE url_touched (
	ur_url_id	INTEGER PRIMARY KEY REFERENCES url_id(ur_id) ON DELETE CASCADE,
	ur_touched	TIMESTAMP NOT NULL
);

DROP TABLE IF EXISTS agent_ids;
CREATE TABLE agent_ids (
	ag_id		INTEGER PRIMARY KEY AUTO_INCREMENT,
	ag_site		INTEGER NOT NULL REFERENCES sites(si_id) ON DELETE CASCADE,
	ag_name		VARCHAR(255) NOT NULL,
	ag_grouped	TINYINT(1) NOT NULL
);
CREATE INDEX agent_name_idx ON agent_ids(ag_site, ag_name);

DROP TABLE IF EXISTS agent_count;
CREATE TABLE agent_count (
	ac_id		INTEGER PRIMARY KEY REFERENCES agent_ids(ag_id) ON DELETE CASCADE,
	ac_count	INTEGER,
	ac_touched	TIMESTAMP NOT NULL
);
CREATE INDEX agent_count_idx ON agent_count(ac_count);

DROP TABLE IF EXISTS ref_ids;
CREATE TABLE ref_ids (
	ref_id		INTEGER PRIMARY KEY AUTO_INCREMENT,
	ref_site	INTEGER NOT NULL REFERENCES sites(si_id) ON DELETE CASCADE,
	ref_url		VARCHAR(255) NOT NULL,
	ref_grouped	TINYINT(1) NOT NULL
);

DROP TABLE IF EXISTS ref_count;
CREATE TABLE ref_count (
	ref_id		INTEGER PRIMARY KEY REFERENCES ref_ids(ref_id) ON DELETE CASCADE,
	ref_count	INTEGER,
	ref_touched	TIMESTAMP NOT NULL
);
CREATE INDEX ref_count_idx ON ref_count(ref_count);

DROP TABLE IF EXISTS langs;
CREATE TABLE langs (
	lang_id		INTEGER PRIMARY KEY AUTO_INCREMENT,
	lang_site	INTEGER NOT NULL REFERENCES sites(si_id) ON DELETE CASCADE,
	lang_name	VARCHAR(32) NOT NULL
);
CREATE INDEX lang_site_name_idx ON langs(lang_site, lang_name);

DROP TABLE IF EXISTS lang_count;
CREATE TABLE lang_count (
	lc_id		INTEGER NOT NULL REFERENCES langs(lang_id) ON DELETE CASCADE,
	lc_count	INTEGER NOT NULL
);
CREATE INDEX lang_touched_idx ON lang_count(lc_count);

DROP TABLE IF EXISTS lang_touched;
CREATE TABLE lang_touched (
	lt_id		INTEGER NOT NULL REFERENCES langs(lang_id) ON DELETE CASCADE,
	lt_touched	TIMESTAMP NOT NULL
);
CREATE INDEX lang_touched_idx ON lang_touched(lt_touched);

DROP TABLE IF EXISTS hours;
CREATE TABLE hours (
	hr_id		INTEGER PRIMARY KEY AUTO_INCREMENT,
	hr_site		INTEGER NOT NULL REFERENCES sites(si_id) ON DELETE CASCADE,
	hr_hour		INTEGER(2) NOT NULL,
	hr_count	INTEGER NOT NULL
);
CREATE INDEX hours_site_count ON hours(hr_site, hr_count);
CREATE INDEX hr_hour_all_idx ON hours(hr_hour,hr_count);

