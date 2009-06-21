
-- Schema for the OpenID extension (Postgres version)

CREATE TABLE /*$wgDBprefix*/user_openid (
  uoi_openid VARCHAR(255) NOT NULL PRIMARY KEY,
  uoi_user INTEGER NOT NULL REFERENCES mwuser(user_id)
) /*$wgDBTableOptions*/;

CREATE INDEX user_openid_user ON user_openid(uoi_user);