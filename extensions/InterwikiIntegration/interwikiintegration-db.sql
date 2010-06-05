BEGIN;

CREATE TABLE integration_db (
integration_prefix                      varchar(256) binary NOT NULL PRIMARY KEY,
integration_dbname                      varchar(256) binary NOT NULL,
integration_pwd                         tinyint unsigned NOT NULL default 0
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

COMMIT;