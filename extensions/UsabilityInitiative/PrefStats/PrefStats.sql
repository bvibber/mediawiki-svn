--
-- Schema for PrefStats
--

CREATE TABLE IF NOT EXISTS /*_*/prefstats (
	ps_user int NOT NULL,
	ps_pref varbinary(32) NOT NULL,
	ps_value blob NOT NULL,
	ps_start binary(14) NOT NULL,
	ps_end binary(14) NULL,
	ps_duration int unsigned
) /*$wgDBTableOptions*/;

CREATE UNIQUE INDEX /*i*/ps_user_pref_start ON /*_*/prefstats (ps_user, ps_pref, ps_start);
CREATE INDEX /*i*/ps_duration_start ON /*_*/prefstats (ps_duration, ps_start);
