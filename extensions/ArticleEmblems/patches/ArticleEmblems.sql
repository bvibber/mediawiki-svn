--
-- Schema for ArticleEmblems
--

CREATE TABLE IF NOT EXISTS /*_*/articleemblems (
	-- Article ID
	ae_article int NOT NULL,
	-- Emblem value
	ae_value blob NOT NULL
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/ae_article ON /*_*/articleemblems (ae_article);
