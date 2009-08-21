--
-- Schema for ClickTracking
--

CREATE TABLE IF NOT EXISTS /*_*/click_tracking (
	-- Timestamp
	action_time timestamp NOT NULL default CURRENT_TIMESTAMP,

	-- session id
	session_id varbinary(255) NOT NULL,

	-- true if the user is logged in
	is_logged_in boolean NOT NULL,

	-- total user contributions
	user_total_contribs integer,

	-- user contributions over a specified timespan
	user_contribs_span integer,

	-- namespace being edited
	namespace integer NOT NULL,

	-- event ID (not unique)
	event_id integer NOT NULL
) /*$wgDBTableOptions*/;