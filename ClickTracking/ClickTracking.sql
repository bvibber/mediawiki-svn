--
-- Schema for ClickTracking
--

CREATE TABLE IF NOT EXISTS /*_*/click_tracking (
	-- Timestamp
	action_time timestamp NOT NULL default CURRENT_TIMESTAMP,

    --true if the user is logged in
    is_logged_in boolean NOT NULL,
	
	-- contributions
	user_contribs integer,
	
	-- namespace being edited
	namespace integer NOT NULL,

	-- event ID (not unique) 
	event_id integer NOT NULL

) /*$wgDBTableOptions*/;
