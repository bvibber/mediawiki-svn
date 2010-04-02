--
-- Schema for OptIn
--

CREATE TABLE IF NOT EXISTS /*_*/prefswitch_survey (
	-- User ID
	pss_user int NOT NULL,

	-- Timestamp
	pss_timestamp binary(14) NOT NULL,

	-- Survey name (out, feedback, etc.)
	pss_name varchar(16) NOT NULL,

	-- Question ID (key in $wgPrefSwitchSurvey)
	pss_question varchar(16) NOT NULL,

	-- Answer ID (key in $wgPrefSwitchSurvey[pss_question]['answers'])
	pss_answer text NULL,

	-- Optional text associated with the answer
	pss_answer_data text NULL
) /*$wgDBTableOptions*/;

CREATE UNIQUE INDEX /*i*/pss_user_timestamp_question ON /*_*/prefswitch_survey (pss_user, pss_timestamp, pss_question, pss_name);
