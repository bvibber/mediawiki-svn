--
-- Schema patch for prefswitch_user to make it a varbinary	
--

ALTER TABLE /*_*/prefswitch_survey modify pss_user varbinary(255);