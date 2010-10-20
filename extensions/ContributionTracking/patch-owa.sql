--
-- create contribution_tracking.owa_session and owa_ref
--
ALTER TABLE /*_*/contribution_tracking ADD owa_session varbinary(255);
ALTER TABLE /*_*/contribution_tracking ADD owa_ref INTEGER;
