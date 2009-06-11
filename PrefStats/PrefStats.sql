--
-- Schema for PrefStats
--

CREATE TABLE IF NOT EXISTS prefstats (
	ps_user int NOT NULL,
	ps_pref varbinary(32) NOT NULL,
	ps_value blob NOT NULL,
	ps_start binary(14) NOT NULL,
	ps_end binary(14) NULL,
	ps_duration unsigned int NOT NULL,
);

CREATE UNIQUE INDEX ps_user_pref ON prefstats (ps_user, ps_pref);
CREATE INDEX ps_duration_start ON prefstats (ps_duration, ps_start);
