BEGIN;

CREATE TABLE integration_namespace (
integration_namespace_id                int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
integration_dbname                      varchar(256) binary NOT NULL,
integration_namespace_index             int NOT NULL,
integration_namespace_title             varchar(256) binary NOT NULL
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

COMMIT;