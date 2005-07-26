DELIMITER '//';

DROP PROCEDURE IF EXISTS update_url_count//
CREATE PROCEDURE update_url_count (site_name VARCHAR(255), path VARCHAR(255), grouped TINYINT(1))
MODIFIES SQL DATA
BEGIN
	DECLARE sid INTEGER;
	DECLARE urid INTEGER;

	INSERT IGNORE INTO sites(si_name) VALUES(site_name);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT si_id INTO sid FROM sites WHERE si_name = site_name;
	ELSE
		SET sid = LAST_INSERT_ID();
	END IF;

	INSERT IGNORE INTO url_id(ur_site, ur_path, ur_grouped) VALUES(sid, path, grouped);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT ur_id INTO urid FROM url_id WHERE ur_site = sid AND ur_path = path;
	ELSE
		SET urid = LAST_INSERT_ID();
	END IF;

	INSERT IGNORE INTO url_count(uc_url_id, uc_count) VALUES (urid, 0);
	UPDATE url_count SET uc_count = uc_count + 1 WHERE uc_url_id = urid;

	UPDATE url_touched SET ur_touched = CURRENT_TIMESTAMP WHERE ur_url_id = urid;
	COMMIT;
END;//

DROP PROCEDURE IF EXISTS update_hour_count//
CREATE PROCEDURE update_hour_count (site_name VARCHAR(255), hour INT(2))
MODIFIES SQL DATA
BEGIN
	DECLARE sid INTEGER;

	INSERT IGNORE INTO sites(si_name) VALUES(site_name);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT si_id INTO sid FROM sites WHERE si_name = site_name;
	ELSE
		SET sid = LAST_INSERT_ID();
	END IF;

	UPDATE hours SET hr_count = hr_count + 1 WHERE hr_site=sid AND hr_hour = hour;
	COMMIT;
END;//

DROP PROCEDURE IF EXISTS update_wday_count//
CREATE PROCEDURE update_wday_count (site_name VARCHAR(255), wday INT(2))
MODIFIES SQL DATA
BEGIN
	DECLARE sid INTEGER;

	INSERT IGNORE INTO sites(si_name) VALUES(site_name);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT si_id INTO sid FROM sites WHERE si_name = site_name;
	ELSE
		SET sid = LAST_INSERT_ID();
	END IF;

	UPDATE wdays SET wd_hits = wd_hits + 1 WHERE wd_site=sid AND wd_day = wday;
	COMMIT;
END;//

DROP PROCEDURE IF EXISTS update_refer//
CREATE PROCEDURE update_refer (site_name VARCHAR(255), refer VARCHAR(255), grouped TINYINT(1))
MODIFIES SQL DATA
BEGIN
	DECLARE sid INTEGER;
	DECLARE rid INTEGER;

	INSERT IGNORE INTO sites(si_name) VALUES(site_name);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT si_id INTO sid FROM sites WHERE si_name = site_name;
	ELSE
		SET sid = LAST_INSERT_ID();
	END IF;

	INSERT IGNORE INTO ref_ids(ref_site, ref_url, ref_grouped) VALUES(sid, refer, grouped);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT ref_id INTO rid FROM ref_ids WHERE ref_site = sid AND ref_url = refer;
	ELSE
		SET rid = LAST_INSERT_ID();
	END IF;

	INSERT IGNORE INTO ref_count(ref_id, ref_count, ref_touched) VALUES (rid, 0, CURRENT_TIMESTAMP);
	UPDATE ref_count SET ref_count = ref_count = 1, ref_touched = CURRENT_TIMESTAMP WHERE ref_id = rid;
	COMMIT;
END;//
	
DROP PROCEDURE IF EXISTS update_agent//
CREATE PROCEDURE update_agent (site_name VARCHAR(255), agent VARCHAR(255), grouped TINYINT(1))
MODIFIES SQL DATA
BEGIN
	DECLARE sid INTEGER;
	DECLARE aid INTEGER;

	INSERT IGNORE INTO sites(si_name) VALUES(site_name);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT si_id INTO sid FROM sites WHERE si_name = site_name;
	ELSE
		SET sid = LAST_INSERT_ID();
	END IF;

	INSERT IGNORE INTO agent_ids(ag_site, ag_name, ag_grouped) VALUES(sid, agent, grouped);
	IF LAST_INSERT_ID() = 0 THEN
		SELECT ag_id INTO aid FROM agent_ids WHERE ag_site = sid AND ag_name = agent;
	ELSE
		SET aid = LAST_INSERT_ID();
	END IF;

	INSERT IGNORE INTO agent_count(ac_id, ac_count, ac_touched) VALUES (aid, 0, CURRENT_TIMESTAMP);
	UPDATE agent_count SET ac_count = ac_count = 1, ac_touched = CURRENT_TIMESTAMP WHERE ac_id = aid;
	COMMIT;
END;//
	
DELIMITER ';'//
