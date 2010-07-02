-- Adds tables for CommunityHiring
CREATE TABLE community_hiring_application (
	ch_id int unsigned auto_increment,
	ch_data LONGBLOB,
	primary key (ch_id)
);
