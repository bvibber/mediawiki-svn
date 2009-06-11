--
-- Grabs current data from the user_properties table
-- and processes it into the prefstats table
--
-- Meant to be run by a cron job on the toolserver,
-- for if tracking changes directly isn't gonna fly

-- VECTOR

-- Update existing users
UPDATE prefstats
SET ps_end = IF(ISNULL(
	(SELECT up_user
	FROM enwiki_p.user_properties
	WHERE up_user=ps_user AND up_property='skin' AND up_value='vector'
	)), NOW(), NULL),
ps_duration = TIMESTAMPDIFF(DAY, NOW(), ps_start)
WHERE ps_pref='vector' AND ps_end IS NULL;

-- Insert new users
INSERT IGNORE INTO prefstats (ps_user, ps_pref, ps_start, ps_end, ps_duration)
SELECT up_user, up_value, NOW(), NULL, 0
FROM enwiki_p.user_properties
WHERE up_property='skin' AND up_value='vector';

-- TOOLBAR

-- Update existing users
UPDATE prefstats
SET ps_end = IF(ISNULL(
	(SELECT up_user
	FROM enwiki_p.user_properties
	WHERE up_user=ps_user AND up_property='usebetatoolbar' AND up_value='1'
	)), NOW(), NULL),
ps_duration = TIMESTAMPDIFF(DAY, NOW(), ps_start)
WHERE ps_pref='vector' AND ps_end IS NULL;

-- Insert new users
INSERT IGNORE INTO prefstats (ps_user, ps_pref, ps_start, ps_end, ps_duration)
SELECT up_user, up_value, NOW(), NULL, 0
FROM enwiki_p.user_properties
WHERE up_property='usebetatoolbar' AND up_value='1';
