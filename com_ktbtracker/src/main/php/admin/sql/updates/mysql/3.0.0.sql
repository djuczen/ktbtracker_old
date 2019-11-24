-- ------------------------------------------------------------------------
-- @package		Joomla.Administrator
-- @subpackage 	com_ktbtracker
-- 
-- @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
-- @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
-- 
-- $Id$
-- ------------------------------------------------------------------------

--
-- BACKUP TABLES
--

ALTER TABLE #__ktbtracker_cycles RENAME AS #__ktbtracker_cycles_PRE300;
ALTER TABLE #__ktbtracker_candidates RENAME AS #__ktbtracker_candidates_PRE300;
ALTER TABLE #__ktbtracker_requirements RENAME AS #__ktbtracker_requirements_PRE300;
ALTER TABLE #__ktbtracker_tracking RENAME AS #__ktbtracker_tracking_PRE300;
  
-- 
-- CREATE TABLES
--

-- 
-- Table structure for table `#__ktbtracker_cycles`
-- 
--	This table contains 1 row per testing cycle; it contains the cycle dates and
--	requirement goals (NULL to use default, 0 if not required)

DROP TABLE IF EXISTS `#__ktbtracker_cycles` ;
CREATE TABLE IF NOT EXISTS `#__ktbtracker_cycles` (
  `id` 				INT(11) 		NOT NULL AUTO_INCREMENT,
  `title` 			VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL,
  `alias` 			VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL,
  `description`	 	MEDIUMTEXT 		COLLATE utf8_general_ci NOT NULL,
  `cycle_start` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cycle_finish` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cycle_prestart` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cycle_cutoff` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cycle_reqmnts` 	MEDIUMTEXT		COLLATE utf8_general_ci NOT NULL,
  `cycle_goals`		INT(11)			NOT NULL DEFAULT '0',
  `cycle_weekstart` INT(1)			NOT NULL DEFAULT '0',
  `created` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` 		INT(10) 		NOT NULL DEFAULT '0',
  `modified` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out` 	INT(10) 		NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci  AUTO_INCREMENT=1 ;

-- 
-- Table structure for table `#__ktbtracker_candidates`
-- 
--	This table contains 1 row per candidate per cycle, recording cycle-based
-- 	requirements (not daily).

DROP TABLE IF EXISTS `#__ktbtracker_candidates` ;
CREATE TABLE IF NOT EXISTS `#__ktbtracker_candidates` (
  `id` 				INT(11) 		NOT NULL AUTO_INCREMENT,
  `userid` 			INT(11) 		NOT NULL DEFAULT '0',
  `cycleid` 		INT(11) 		NOT NULL DEFAULT '0',
  `cont_cycleid` 	INT(11) 		NOT NULL DEFAULT '0',
  `cycle_goals`		INT(11)			NOT NULL DEFAULT '0',
  `letters`			INT(11)			NOT NULL DEFAULT '0',
  `essays`			INT(11)			NOT NULL DEFAULT '0',
  `tree`			INT(11)			NOT NULL DEFAULT '0',
  `test_written`	DECIMAL(10,2)	NOT NULL DEFAULT '0.00',
  `test_physical`	DECIMAL(10,2)	NOT NULL DEFAULT '0.00',
  `tract` 			SMALLINT(1) 	NOT NULL DEFAULT '0',
  `adult` 			TINYINT(1) 		NOT NULL DEFAULT '1',
  `status` 			TINYINT(1) 		NOT NULL DEFAULT '0',
  `hidden`			TINYINT(1)		NOT NULL DEFAULT '0',
  `goal_start` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `goal_finish`		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` 		INT(11) 		NOT NULL DEFAULT '0',
  `modified` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE `idx_candidate` (`userid`, `cycleid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci  AUTO_INCREMENT=1 ;

-- 
-- Table structure for table `#__ktbtracker_masters`
-- 
--	This table contains 1 row per master candidate per journey, recording journey-based
-- 	requirements (not daily).

DROP TABLE IF EXISTS `#__ktbtracker_masters` ;
CREATE TABLE IF NOT EXISTS `#__ktbtracker_masters` (
  `id` 				INT(11) 		NOT NULL AUTO_INCREMENT,
  `userid` 			INT(11) 		NOT NULL DEFAULT '0',
  `journey_start`	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `jounery_finish` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inst_camps`		INT(11)			NOT NULL DEFAULT '0',
  `lead_class`		INT(11)			NOT NULL DEFAULT '0',
  `assist_class`	INT(11)			NOT NULL DEFAULT '0',
  `pre_test`		INT(11)			NOT NULL DEFAULT '0',
  `assist_testing`	INT(11)			NOT NULL DEFAULT '0',
  `special_events`	INT(11)			NOT NULL DEFAULT '0',
  `video_trng`		INT(11)			NOT NULL DEFAULT '0',
  `lead_seminar`	INT(11)			NOT NULL DEFAULT '0',
  `tract` 			SMALLINT(1) 	NOT NULL DEFAULT '0',
  `status` 			TINYINT(1) 		NOT NULL DEFAULT '0',
  `hidden`			TINYINT(1)		NOT NULL DEFAULT '0',
  `created` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` 		INT(11) 		NOT NULL DEFAULT '0',
  `modified` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE `idx_master` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci  AUTO_INCREMENT=1 ;

-- 
-- Table structure for table `#__ktbtracker_requirements`
-- 
--	This table includes 1 row per set of cycle goals. Candidate may be assigned different cycle
--  goals depending on age group or other circumstances. ID 0 is always the default goal set.

DROP TABLE IF EXISTS `#__ktbtracker_requirements` ;
CREATE TABLE IF NOT EXISTS `#__ktbtracker_requirements` (
  `id` 				INT(11) 		NOT NULL AUTO_INCREMENT,
  `title` 			VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL,
  `alias` 			VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL,
  `miles` 			DECIMAL(10,2) 	NOT NULL DEFAULT '105.00',
  `pushups` 		INT(11) 		NOT NULL DEFAULT '10000',
  `situps` 			INT(11) 		NOT NULL DEFAULT '10000',
  `burpees` 		INT(11)		 	NOT NULL DEFAULT '1000',
  `kicks` 			INT(11)		 	NOT NULL DEFAULT '10500',
  `poomsae` 		INT(11)		 	NOT NULL DEFAULT '1500',
  `self_defense` 	INT(11)		 	NOT NULL DEFAULT '1500',
  `sparring` 		DECIMAL(10,2) 	NOT NULL DEFAULT '210.00',
  `jumps` 			DECIMAL(10,2)	NOT NULL DEFAULT '210.00',
  `pullups`			INT(11)			NOT NULL DEFAULT '350',
  `rolls_falls` 	INT(11)		 	NOT NULL DEFAULT '1000',
  `class_saturday` 	INT(11) 		NOT NULL DEFAULT '10',
  `class_weekday` 	INT(11) 		NOT NULL DEFAULT '10',
  `class_pmaa` 		INT(11) 		NOT NULL DEFAULT '10',
  `class_sparring`	INT(11) 		NOT NULL DEFAULT '15',
  `class_masterq`	INT(11) 		NOT NULL DEFAULT '10',
  `class_dreamteam`	INT(11) 		NOT NULL DEFAULT '10',
  `class_hyperpro`	INT(11) 		NOT NULL DEFAULT '10',
  `meditation`		DECIMAL(10,2)	NOT NULL DEFAULT '500.00',
  `raok`			INT(11) 		NOT NULL DEFAULT '200',
  `mentor`			INT(11) 		NOT NULL DEFAULT '10',
  `mentee`			INT(11) 		NOT NULL DEFAULT '10',
  `leadership` 		INT(11) 		NOT NULL DEFAULT '20',
  `leadership2` 	INT(11) 		NOT NULL DEFAULT '10',
  `journals`		INT(11) 		NOT NULL DEFAULT '50',
  `created` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` 		INT(11) 		NOT NULL DEFAULT '0',
  `modified` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- 
-- Table structure for table `#__ktbtracker_tracking`
-- 
--	This table includes 1 row per day per candidate, recording all requirements
--	that can be recorded on a daily basis.

DROP TABLE IF EXISTS `#__ktbtracker_tracking` ;
CREATE TABLE IF NOT EXISTS `#__ktbtracker_tracking` (
  `id` 				INT(11) 		NOT NULL AUTO_INCREMENT,
  `tracking_date` 	DATE 			NOT NULL DEFAULT '0000-00-00',
  `userid` 			INT(11) 		NOT NULL DEFAULT '0',
  `cycleid` 		INT(11) 		NOT NULL DEFAULT '0',
  `miles` 			DECIMAL(10,2) 	NOT NULL DEFAULT '0.00',
  `pushups` 		INT(11) 		NOT NULL DEFAULT '0',
  `situps` 			INT(11) 		NOT NULL DEFAULT '0',
  `burpees` 		INT(11)		 	NOT NULL DEFAULT '0',
  `kicks` 			INT(11)		 	NOT NULL DEFAULT '0',
  `poomsae` 		INT(11)		 	NOT NULL DEFAULT '0',
  `self_defense` 	INT(11)		 	NOT NULL DEFAULT '0',
  `sparring` 		DECIMAL(10,2) 	NOT NULL DEFAULT '0.00',
  `jumps` 			DECIMAL(10,2)	NOT NULL DEFAULT '0.00',
  `pullups`			INT(11)			NOT NULL DEFAULT '0',
  `rolls_falls` 	INT(11)		 	NOT NULL DEFAULT '0',
  `class_saturday` 	INT(11) 		NOT NULL DEFAULT '0',
  `class_weekday` 	INT(11) 		NOT NULL DEFAULT '0',
  `class_pmaa` 		INT(11) 		NOT NULL DEFAULT '0',
  `class_sparring`	INT(11) 		NOT NULL DEFAULT '0',
  `class_masterq`	INT(11) 		NOT NULL DEFAULT '0',
  `class_dreamteam`	INT(11) 		NOT NULL DEFAULT '0',
  `class_hyperpro`	INT(11) 		NOT NULL DEFAULT '0',
  `meditation`		DECIMAL(10,2)	NOT NULL DEFAULT '0.00',
  `raok`			INT(11) 		NOT NULL DEFAULT '0',
  `mentor`			INT(11) 		NOT NULL DEFAULT '0',
  `mentee`			INT(11) 		NOT NULL DEFAULT '0',
  `leadership` 		INT(11) 		NOT NULL DEFAULT '0',
  `leadership2` 	INT(11) 		NOT NULL DEFAULT '0',
  `created` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` 		INT(11) 		NOT NULL DEFAULT '0',
  `modified` 		DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out` 	INT(11) 		NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE `idx_daily_tracking` (`tracking_date`, `userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;


--
-- MIGRATE TABLES
--

INSERT INTO `#__ktbtracker_cycles`
  (`id`, `title`, `description`, `cycle_start`, `cycle_finish`, `cycle_prestart`, `cycle_cutoff`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`) 
  SELECT 
   `id`, `name`,  `description`, `cycle_start`, `cycle_finish`, `publish_up`,     `publish_down`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time` 
  FROM `#__ktbtracker_cycles_PRE300`;

UPDATE `#__ktbtracker_cycles`
  SET cycle_reqmnts = 'miles,pushups,situps,burpees,kicks,poomsae,self_defense,sparring,jumps,pullups,class_saturday,class_weekday,class_pmaa,class_masterq,class_dreamteam,meditation,raok,mentor,mentee,leadership,leadership2',
  cycle_goals = 0,
  cycle_weekstart = 6
  WHERE `id` <= 12;
  
UPDATE `#__ktbtracker_cycles`
  SET cycle_reqmnts = 'miles,pushups,situps,burpees,kicks,poomsae,self_defense,sparring,jumps,pullups,rolls_falls,class_saturday,class_weekday,class_pmaa,class_masterq,class_dreamteam,meditation,raok,mentor,mentee,leadership,leadership2',
  cycle_goals = 0,
  cycle_weekstart = 6
  WHERE `id` > 12;

UPDATE `#__ktbtracker_cycles`
  SET cycle_weekstart = 1
  WHERE `id` = 19;

  
INSERT INTO `#__ktbtracker_candidates`
  (`id`, `userid`, `cycleid`, `cont_cycleid`, `cycle_goals`, `tract`, `adult`,  `status`, `hidden`, `goal_start`,  `goal_finish`,  `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`) 
  SELECT 
   `id`, `userid`, `cycleid`, `cont_cycleid`, 0,             `tract`, `access`, `state`,  0,        `publish_up`,  `publish_down`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time` 
  FROM `#__ktbtracker_candidates_PRE300`;

UPDATE `#__ktbtracker_candidates`
  SET `tract` = 16
  WHERE `tract` = 0;
  
UPDATE `#__ktbtracker_candidates`
  SET `tract` = (`tract` + 15)
  WHERE `tract` BETWEEN 1 AND 15;

  
INSERT INTO `#__ktbtracker_requirements` (`title`, `alias`) VALUES('Standard Goals', 'standard-goals');

  
INSERT INTO `#__ktbtracker_tracking`
  (`id`, `tracking_date`, `userid`, `cycleid`, 
    `miles`, `pushups`, `situps`, `burpees`, `kicks`, `poomsae`, `self_defense`, `sparring`, `jumps`, `pullups`, `rolls_falls`, 
    `class_saturday`, `class_weekday`, `class_pmaa`, `class_sparring`, `class_masterq`, `class_dreamteam`, `class_hyperpro`, 
    `meditation`, `raok`, `mentor`, `mentee`, `leadership`, `leadership2`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`) 
  SELECT `id`, `tracking_date`, `userid`, `cycleid`, 
    `reqmnt0`, `reqmnt1`, `reqmnt2`, `reqmnt3`, `reqmnt4`, `reqmnt5`, `reqmnt6`, `reqmnt7`, `reqmnt8`, `reqmnt10`, 0, 
    `reqmnt11`, `reqmnt12`, `reqmnt16`, `reqmnt15`, `reqmnt13`, `reqmnt14`,  0, 
    `reqmnt9`, `reqmnt19`, `reqmnt20`, `reqmnt21`, `reqmnt17`, `reqmnt18`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`
  FROM `#__ktbtracker_tracking_PRE300` WHERE `cycleid` < 13; 
  
INSERT INTO `#__ktbtracker_tracking`
  (`id`, `tracking_date`, `userid`, `cycleid`, 
    `miles`, `pushups`, `situps`, `burpees`, `kicks`, `poomsae`, `self_defense`, `sparring`, `jumps`, `pullups`, `rolls_falls`, 
    `class_saturday`, `class_weekday`, `class_pmaa`, `class_sparring`, `class_masterq`, `class_dreamteam`, `class_hyperpro`, 
    `meditation`, `raok`, `mentor`, `mentee`, `leadership`, `leadership2`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`) 
  SELECT `id`, `tracking_date`, `userid`, `cycleid`, 
    `reqmnt0`, `reqmnt1`, `reqmnt2`, `reqmnt3`, `reqmnt4`, `reqmnt5`, `reqmnt6`, `reqmnt7`, `reqmnt8`, `reqmnt10`, `reqmnt22`, 
    `reqmnt11`, `reqmnt12`, `reqmnt16`, `reqmnt15`, `reqmnt13`, `reqmnt14`,  0, 
    `reqmnt9`, `reqmnt19`, `reqmnt20`, `reqmnt21`, `reqmnt17`, `reqmnt18`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`
  FROM `#__ktbtracker_tracking_PRE300` WHERE `cycleid` = 13;
  
INSERT IGNORE INTO `#__ktbtracker_tracking`
  (`id`, `tracking_date`, `userid`, `cycleid`, 
    `miles`, `pushups`, `situps`, `burpees`, `kicks`, `poomsae`, `self_defense`, `sparring`, `jumps`, `pullups`, `rolls_falls`, 
    `class_saturday`, `class_weekday`, `class_pmaa`, `class_sparring`, `class_masterq`, `class_dreamteam`, `class_hyperpro`, 
    `meditation`, `raok`, `mentor`, `mentee`, `leadership`, `leadership2`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`) 
  SELECT `id`, `tracking_date`, `userid`, `cycleid`, 
    `reqmnt0`, `reqmnt1`, `reqmnt2`, `reqmnt3`, `reqmnt4`, `reqmnt5`, `reqmnt6`, `reqmnt7`, `reqmnt8`, `reqmnt10`, `reqmnt30`, 
    `reqmnt11`, `reqmnt12`, `reqmnt16`, `reqmnt15`, `reqmnt13`, `reqmnt14`,  0, 
    `reqmnt9`, `reqmnt19`, `reqmnt20`, `reqmnt21`, `reqmnt17`, `reqmnt18`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`
  FROM `#__ktbtracker_tracking_PRE300` WHERE `cycleid` > 13;
