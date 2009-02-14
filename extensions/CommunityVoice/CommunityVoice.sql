-- 
-- SQL for CommunityVoice Extension
-- 
-- Table for ratings
DROP TABLE IF EXISTS /*$wgDBprefix*/cv_ratings_votes;
CREATE TABLE /*$wgDBPrefix*/cv_ratings_votes (
    -- Category of item being rated
    vot_category VARBINARY(255) NOT NULL default '',
    -- Title of item being rated
    vot_title VARBINARY(255) NOT NULL default '',
    -- User who made the rating, 0 for anons (however it shoudn't be allowed)
    vot_user INTEGER NOT NULL default 0,
    -- Value of rating
    vot_rating INTEGER NOT NULL default 0,
    -- 
    INDEX vot_category_title ( vot_category, vot_title ),
    INDEX vot_category_title_user ( vot_category, vot_title, vot_user ),
    INDEX vot_category_title_rating ( vot_category, vot_title, vot_rating )
) /*$wgDBTableOptions*/;
-- 
-- Table for articles which include ratings
DROP TABLE IF EXISTS /*$wgDBprefix*/cv_ratings_usage;
CREATE TABLE /*$wgDBPrefix*/cv_ratings_usage (
    -- Category of item being rated
    usg_category VARBINARY(255) NOT NULL default '',
    -- Title of item being rated
    usg_title VARBINARY(255) NOT NULL default '',
    -- Title of article which includes the rating
    usg_article VARBINARY(255) NOT NULL default '',
    -- 
    INDEX rat_category_title ( usg_category, usg_title ),
    INDEX rat_category_title_article ( usg_category, usg_title, usg_article )
) /*$wgDBTableOptions*/;
