-- ------------------------------------------------------------------------
-- @package		Joomla.Administrator
-- @subpackage 	com_ktbtracker
-- 
-- @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
-- @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
-- 
-- $Id$
-- ------------------------------------------------------------------------

-- REQUIREMENT			COLUMN			TABLE		TYPE	GOAL (UNIT)
-- 	Physical
--		Run/Walk		miles			tracking	DEC		105.00
--		Push Ups		pushups			tracking	INT		10,000
--		Sit Ups			situps			tracking	INT		10,000
--		Burpees			burpees			tracking	INT		1,000
--		Jump Rope		jumps			tracking	DEC 	210.00 (60s rounds)
--		Basic Kicks 	kicks			tracking	INT		10,500
--		Sparring Rounds	sparring		tracking	DEC		210.00 (90s rounds)
--		Poomsae Challen	poomsae			tracking	INT		1,500
--		Self-Defense Ch	self_defense	tracking	INT		1,500
--		Pull Ups		pullups			tracking	INT		350
--		Rolls/Falls		rolls_falls		tracking	INT		1,000
--	Classes
--		Saturday BB		class_saturday	tracking	INT		10
--		Weekday BB		class_weekday	tracking	INT		10
--		PMAA 			class_pmaa		tracking	INT		10
--		Sparring		class_sparring	tracking	INT		15
-- 		Master Quest	class_masterq	tracking	INT		10
--		Dream Team		class_dreamteam	tracking	INT		10
--		Hyper Pro		class_hyperpro	tracking	INT		10
--	Other Requirements
--		Meditation		meditation		tracking	DEC		500.00 (min)
--		Daily Journal	N/A				[calculated]		50
--		RAOK			raok			tracking	INT		200
--		Mentor			mentor			tracking	INT		10
--		Mentee			mentee			tracking	INT		10
--		Leadership 		leadership		tracking	INT		20
--		Leadership 2	leadership2		tracking	INT		10
--		Recommendation	letters			candidates	INT		2
--      Essays			essays			candidates	INT		4 (possibly more)
--		Tree			tree			candidates	INT		1
--		Written Test	test_written	candidates	DEC		1	
--		Physical Test	test_pyhsical	candidates	DEC		1
--	Master's Journey
--		(in addition to 2 cycles per year)
--		Attend Instruct inst_camps		masters		INT		4
--		Lead A Class	lead_class		masters		INT		24 (per year)
--		Assist A Class	assist_class	masters		INT		24 (per year)
--		Pre-Test		pre_test		masters		INT		1 (per year)
--		Assist In Testi	assist_testing	masters		INT		2 (per year)
--		Attend Special 	special_events	masters		INT		2 (per year)
--		Video Training	video_trng		masters		INT		4 (per year)
--		Lead A Seminar	lead_seminar	masters		INT		2 (per year)
--
		
-- 
-- Table structure for table `#__ktbtracker_cycles`
-- 
--	This table contains 1 row per testing cycle; it contains the cycle dates and
--	requirement goals (NULL to use default, 0 if not required)

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
