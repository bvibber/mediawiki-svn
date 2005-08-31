-- MySQL dump 9.11
--
-- Host: localhost    Database: wikidata
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-10-log

INSERT INTO `namespace` VALUES (-2,'NS_MEDIA',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (-1,'NS_SPECIAL',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (1,'NS_TALK',0,0,NULL,0,NULL);
INSERT INTO `namespace` VALUES (2,'NS_USER',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (3,'NS_USER_TALK',0,0,NULL,2,NULL);
INSERT INTO `namespace` VALUES (4,'NS_PROJECT',1,0,'User',NULL,NULL);
INSERT INTO `namespace` VALUES (5,'NS_PROJECT_TALK',0,0,NULL,4,NULL);
INSERT INTO `namespace` VALUES (6,'NS_IMAGE',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (7,'NS_IMAGE_TALK',0,0,NULL,6,NULL);
INSERT INTO `namespace` VALUES (8,'NS_MEDIAWIKI',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (9,'NS_MEDIAWIKI_TALK',0,0,NULL,8,NULL);
INSERT INTO `namespace` VALUES (10,'NS_TEMPLATE',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (11,'NS_TEMPLATE_TALK',0,0,NULL,10,NULL);
INSERT INTO `namespace` VALUES (12,'NS_HELP',0,1,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (13,'NS_HELP_TALK',0,0,NULL,12,NULL);
INSERT INTO `namespace` VALUES (14,'NS_CATEGORY',0,0,NULL,NULL,NULL);
INSERT INTO `namespace` VALUES (15,'NS_CATEGORY_TALK',0,0,NULL,14,NULL);
INSERT INTO `namespace` VALUES (0,'NS_ARTICLE',1,1,NULL,NULL,NULL);

INSERT INTO `namespace_names` VALUES (-2,'Media',1,1);
INSERT INTO `namespace_names` VALUES (-1,'Special',1,1);
INSERT INTO `namespace_names` VALUES (1,'Talk',1,0);
INSERT INTO `namespace_names` VALUES (2,'User',1,0);
INSERT INTO `namespace_names` VALUES (3,'User_talk',1,0);
INSERT INTO `namespace_names` VALUES (4,'Supermeta',1,0);
INSERT INTO `namespace_names` VALUES (5,'Supermeta_talk',1,0);
INSERT INTO `namespace_names` VALUES (6,'File',1,0);
INSERT INTO `namespace_names` VALUES (7,'File_talk',1,0);
INSERT INTO `namespace_names` VALUES (8,'MediaWiki',1,0);
INSERT INTO `namespace_names` VALUES (9,'MediaWiki_talk',1,0);
INSERT INTO `namespace_names` VALUES (10,'Template',1,0);
INSERT INTO `namespace_names` VALUES (11,'Template_talk',1,0);
INSERT INTO `namespace_names` VALUES (12,'Help',1,0);
INSERT INTO `namespace_names` VALUES (13,'Help_talk',1,0);
INSERT INTO `namespace_names` VALUES (14,'Category',1,0);
INSERT INTO `namespace_names` VALUES (15,'Category_talk',1,0);
INSERT INTO `namespace_names` VALUES (4,'Project',0,1);
INSERT INTO `namespace_names` VALUES (5,'Project_talk',0,0);
INSERT INTO `namespace_names` VALUES (6,'Image',0,0);
INSERT INTO `namespace_names` VALUES (7,'Image_talk',0,0);
INSERT INTO `namespace_names` VALUES (6,'Video',0,0);
INSERT INTO `namespace_names` VALUES (7,'Video_talk',0,0);
INSERT INTO `namespace_names` VALUES (6,'Sound',0,0);
INSERT INTO `namespace_names` VALUES (7,'Sound_talk',0,0);
INSERT INTO `namespace_names` VALUES (-2,'Direct',0,0);
