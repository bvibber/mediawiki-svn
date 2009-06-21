--
-- SQL schema update for OpenID extension to make the uoi_user field not unique
--

ALTER TABLE user_openid DROP INDEX uoi_user;
CREATE INDEX user_openid_user ON user_openid(uoi_user);