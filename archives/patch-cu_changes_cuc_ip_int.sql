ALTER TABLE `mw_cu_changes` ADD `cuc_ip_int` INT UNSIGNED NULL AFTER `cuc_ip_hex` 
ALTER TABLE `mw_cu_changes` ADD `cuc_rdns` VARCHAR( 255 ) NULL 

UPDATE mw_cu_changes SET `cuc_ip_int` = INET_ATON( `cuc_ip` );

ALTER TABLE `testwiki`.`mw_cu_changes` DROP INDEX `cuc_user_ip_time` ,
ADD INDEX `cuc_user_ip_time` ( `cuc_user` , `cuc_ip` , `cuc_timestamp` , `cuc_ip_int` ) 