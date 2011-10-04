
CREATE TABLE `benchmark_fields` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `type` enum('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year') DEFAULT NULL,
  `required` enum('_YES','_NO') DEFAULT '_NO',
  `primarykey` enum('_YES','_NO') DEFAULT '_NO',
  `key` varchar(35) DEFAULT NULL,
  `create_sql` varchar(128) DEFAULT NULL,
  `alter_sql` varchar(128) DEFAULT NULL,
  `size` int(5) DEFAULT NULL,
  `recipricol` int(5) DEFAULT NULL,
  `alter_type` enum('bigint','mediumint','smallint','int','tinyint','float','decimal','double','enum_varchar','enum_int','varchar','mediumtext','text','date','time','datetime','year') DEFAULT NULL,
  `created` int(13) unsigned DEFAULT '0',
  `updated` int(13) unsigned DEFAULT '0',
  `actioned` int(13) unsigned DEFAULT '0',
  PRIMARY KEY (`fid`),
  KEY `common` (`fid`,`type`,`required`,`primarykey`,`alter_type`,`key`(10))
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (1,'id','int','_YES','_YES','common','INT(20) UNSIGNED NOT NULL AUTO_INCREMENT','INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',20,0,'int',1316464298,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (2,'tid','int','_YES','_NO','common','INT(10) UNSIGNED DEFAULT NULL','INT(20) UNSIGNED DEFAULT NULL',10,0,'int',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (3,'boolean','tinyint','_NO','_NO',NULL,'TINYINT(1) DEFAULT NULL','INT(1) DEFAULT NULL',1,0,'int',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (4,'bigint','bigint','_NO','_NO',NULL,'BIGINT(20) DEFAULT NULL','INT(35) DEFAULT NULL',20,0,'int',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (5,'mediumint','mediumint','_NO','_NO',NULL,'MEDIUMINT(20) DEFAULT NULL','BIGINT(15) DEFAULT NULL',20,0,'bigint',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (6,'smallint','smallint','_NO','_NO',NULL,'SMALLINT(20) DEFAULT NULL','INT(15) DEFAULT NULL',20,0,'int',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (7,'int','int','_NO','_NO',NULL,'INT(20) DEFAULT NULL','MEDIUMINT(16) DEFAULT NULL',20,0,'mediumint',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (8,'tinyiny','tinyint','_NO','_NO',NULL,'TINYINT(20) DEFAULT NULL','SMALLINT(14) DEFAULT NULL',20,0,'smallint',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (9,'float','float','_NO','_NO',NULL,'REAL(15,5) DEFAULT NULL','DECIMAL(16,8) DEFAULT NULL',10,5,'double',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (10,'decimal','decimal','_NO','_NO',NULL,'DECIMAL(15,5) DEFAULT NULL','REAL(16,8) DEFAULT NULL',10,5,'float',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (11,'double','double','_NO','_NO',NULL,'DOUBLE(15,5) DEFAULT NULL','DECIMAL(16,8) DEFAULT NULL',10,5,'decimal',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (12,'enum_int','enum_int','_NO','_NO',NULL,'ENUM(%numbersassymbols%) DEFAULT NULL','SMALLINT(5) DEFAULT NULL',2,0,'smallint',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (13,'enum_varchar','enum_varchar','_NO','_NO',NULL,'ENUM(%numbersaswords%) DEFAULT NULL','VARCHAR(35) DEFAULT NULL',15,0,'varchar',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (14,'mediumtext','mediumtext','_NO','_NO',NULL,'MEDIUMTEXT','VARCHAR(2000) DEFAULT NULL',2000,0,'varchar',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (15,'text','text','_NO','_NO',NULL,'TEXT','VARCHAR(2000) DEFAULT NULL',2000,0,'varchar',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (16,'date','date','_NO','_NO',NULL,'DATE DEFAULT NULL','VARCHAR(12) DEFAULT NULL',12,0,'varchar',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (17,'time','time','_NO','_NO',NULL,'TIME DEFAULT NULL','VARCHAR(8) DEFAULT NULL',8,0,'varchar',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (18,'datetime','datetime','_NO','_NO',NULL,'DATETIME DEFAULT NULL','VARCHAR(20) DEFAULT NULL',20,0,'varchar',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (19,'varchar','varchar','_NO','_NO',NULL,'VARCHAR(255) DEFAULT NULL','MEDIUMTEXT',255,0,'mediumtext',1316464299,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (20,'created','int','_YES','_NO','made','INT(12) DEFAULT NULL','MEDIUMINT(12) DEFAULT NULL',12,0,'mediumint',1316464300,0,0);
insert  into `benchmark_fields` (`fid`,`name`,`type`,`required`,`primarykey`,`key`,`create_sql`,`alter_sql`,`size`,`recipricol`,`alter_type`,`created`,`updated`,`actioned`) values (21,'year','year','_NO','_NO',NULL,'YEAR DEFAULT NULL','VARCHAR(4) DEFAULT NULL',4,0,'varchar',1316464300,0,0);

CREATE TABLE `benchmark_results` (
  `rid` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT '0',
  `tbid` int(10) unsigned DEFAULT '0',
  `test` enum('_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY') DEFAULT NULL,
  `engine` enum('INNODB','MYISAM') DEFAULT 'INNODB',
  `session` int(12) DEFAULT '0', 
  `number` int(10) unsigned DEFAULT '0',
  `length` int(10) unsigned DEFAULT '0',
  `fields` int(10) unsigned DEFAULT '0',
  `records` int(10) unsigned DEFAULT '0',
  `started` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `ended` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `difference` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `common_started` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `common_ended` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `common_difference` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `boot_started` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `boot_ended` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `boot_difference` decimal(30,15) unsigned DEFAULT '0.000000000000000',
  `created` int(13) unsigned DEFAULT '0',
  `updated` int(13) unsigned DEFAULT '0',
  `actioned` int(13) unsigned DEFAULT '0',
  PRIMARY KEY (`rid`),
  KEY `common` (`rid`,`tid`,`test`,`number`,`tbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `benchmark_tables` (
  `tbid` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT '0',
  `fids` mediumtext,
  `name` varchar(128) DEFAULT NULL,
  `index` int(12) unsigned DEFAULT '0',
  `test` enum('_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY') DEFAULT NULL,
  `altered` enum('_YES','_NO') DEFAULT '_NO',
  `engine` enum('INNODB','MYISAM') DEFAULT 'INNODB',
  `charset` varchar(35) DEFAULT 'utf8',
  `fields` int(12) unsigned DEFAULT '0',
  `create` int(12) unsigned DEFAULT '0',
  `prepared` int(12) DEFAULT '0',
  `truncated` int(12) DEFAULT '0',
  `dropped` int(12) DEFAULT '0',
  `created` int(13) unsigned DEFAULT '0',
  `updated` int(13) unsigned DEFAULT '0',
  `actioned` int(13) unsigned DEFAULT '0',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `benchmark_tests` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stage` enum('_MI_BENCHMARK_WAIT','_MI_BENCHMARK_PREPARED','_MI_BENCHMARK_TESTING','_MI_BENCHMARK_CLEANUP','_MI_BENCHMARK_FINISHED') DEFAULT '_MI_BENCHMARK_WAIT',
  `test` enum('_MI_BENCHMARK_WAIT','_MI_BENCHMARK_CREATE','_MI_BENCHMARK_SELECT','_MI_BENCHMARK_INSERT','_MI_BENCHMARK_UPDATE','_MI_BENCHMARK_UPDATEALL','_MI_BENCHMARK_DELETE','_MI_BENCHMARK_DELETEALL','_MI_BENCHMARK_ALTER','_MI_BENCHMARK_RENAME','_MI_BENCHMARK_SMARTY','_MI_BENCHMARK_FINISHED') DEFAULT '_MI_BENCHMARK_WAIT',
  `name` varchar(128) DEFAULT NULL,
  `platform` enum('_MI_BENCHMARK_XOOPS24','_MI_BENCHMARK_XOOPS25','_MI_BENCHMARK_XOOPS26','_MI_BENCHMARK_XOOPS27','_MI_BENCHMARK_XOOPS28','_MI_BENCHMARK_XOOPS29','_MI_BENCHMARK_XOOPS30','_MI_BENCHMARK_XOOPS31','_MI_BENCHMARK_XOOPSCUBE','_MI_BENCHMARK_ICMS') DEFAULT '_MI_BENCHMARK_XOOPS25',
  `testing` varchar(128) DEFAULT NULL,
  `note` mediumtext,
  `tbids_create` mediumtext,
  `tbids_select` mediumtext,
  `tbids_insert` mediumtext,
  `tbids_update` mediumtext,
  `tbids_updateall` mediumtext,
  `tbids_delete` mediumtext,
  `tbids_deleteall` mediumtext,
  `tbids_alter` mediumtext,
  `tbids_rename` mediumtext,
  `tbids_smarty` mediumtext,
  `avg_create_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_select_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_insert_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_update_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_updateall_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_delete_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_deleteall_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_alter_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_rename_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_smarty_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_common_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `avg_boot_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_create_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_select_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_insert_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_update_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_updateall_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_delete_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_deleteall_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_alter_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_rename_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_smarty_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_common_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `max_boot_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_create_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_select_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_insert_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_update_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_updateall_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_delete_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_deleteall_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_alter_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_rename_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_smarty_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_common_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `min_boot_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `sum_create_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_select_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_insert_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_update_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_updateall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_delete_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_deleteall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_alter_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_rename_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_smarty_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_create_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_select_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_insert_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_update_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_updateall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_delete_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_deleteall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_alter_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_rename_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_smarty_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_create_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_select_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_insert_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_update_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_updateall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_delete_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_deleteall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_alter_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_rename_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_smarty_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_create_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_select_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_insert_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_update_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_updateall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_delete_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_deleteall_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_alter_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_rename_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_smarty_data_length` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_create_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_select_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_insert_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_update_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_updateall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_delete_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_deleteall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_alter_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_rename_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_smarty_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_create_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_select_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_insert_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_update_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_updateall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_delete_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_deleteall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_alter_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_rename_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_smarty_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_create_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_select_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_insert_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_update_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_updateall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_delete_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_deleteall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_alter_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_rename_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_smarty_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_create_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_select_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_insert_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_update_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_updateall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_delete_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_deleteall_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_alter_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_rename_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_smarty_fields` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_create_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_select_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_insert_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_update_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_updateall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_delete_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_deleteall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_alter_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_rename_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `sum_smarty_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_create_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_select_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_insert_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_update_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_updateall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_delete_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_deleteall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_alter_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_rename_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `min_smarty_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_create_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_select_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_insert_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_update_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_updateall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_delete_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_deleteall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_alter_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_rename_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `max_smarty_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_create_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_select_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_insert_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_update_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_updateall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_delete_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_deleteall_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_alter_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_rename_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `avg_smarty_records` decimal(16,8) unsigned DEFAULT '0.00000000',
  `took_create_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_select_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_insert_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_update_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_updateall_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_delete_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_deleteall_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_alter_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_rename_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_smarty_time` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_common_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `took_boot_seconds` decimal(16,14) unsigned DEFAULT '0.00000000000000',
  `tests_ran_create` int(10) unsigned DEFAULT '0',
  `tests_ran_select` int(10) unsigned DEFAULT '0',
  `tests_ran_insert` int(10) unsigned DEFAULT '0',
  `tests_ran_update` int(10) unsigned DEFAULT '0',
  `tests_ran_updateall` int(10) unsigned DEFAULT '0',
  `tests_ran_delete` int(10) unsigned DEFAULT '0',
  `tests_ran_deleteall` int(10) unsigned DEFAULT '0',
  `tests_ran_alter` int(10) unsigned DEFAULT '0',
  `tests_ran_rename` int(10) unsigned DEFAULT '0',
  `tests_ran_smarty` int(10) unsigned DEFAULT '0',
  `begin` int(13) unsigned DEFAULT '0',
  `started` int(13) unsigned DEFAULT '0',
  `ended` int(13) unsigned DEFAULT '0',
  `created` int(13) unsigned DEFAULT '0',
  `updated` int(13) unsigned DEFAULT '0',
  `actioned` int(13) unsigned DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `common` (`stage`,`test`,`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
