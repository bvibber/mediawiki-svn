CREATE OR REPLACE FUNCTION inet_aton(text) RETURNS bigint AS '
SELECT
split_part($1,''.'',1)::int8*(256*256*256)+
split_part($1,''.'',2)::int8*(256*256)+
split_part($1,''.'',3)::int8*256+
split_part($1,''.'',4)::int8;
' LANGUAGE 'SQL';

ALTER TABLE `mw_cu_changes` ADD `cuc_ip_int` INT UNSIGNED NULL AFTER `cuc_ip_hex` 
ALTER TABLE `mw_cu_changes` ADD `cuc_rdns` VARCHAR( 255 ) NULL 

UPDATE mw_cu_changes SET `cuc_ip_int` = INET_ATON( `cuc_ip` );

ALTER TABLE `mw_cu_changes` DROP INDEX `cuc_user_ip_time` ,
ADD INDEX `cuc_user_ip_time` ( `cuc_user` , `cuc_ip` , `cuc_timestamp` , `cuc_ip_int` ) 

ALTER TABLE `mw_cu_log` ADD `cul_api` TINYINT( 1 ) NOT NULL AFTER `cul_type` 