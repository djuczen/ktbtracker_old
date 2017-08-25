-- ------------------------------------------------------------------------
-- @package		Joomla.Administrator
-- @subpackage 	com_ktbtracker
-- 
-- @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
-- @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
-- 
-- $Id$
-- ------------------------------------------------------------------------

-- 
-- Table structure for table `#__ktbtracker_cycles`
-- 
--	This table contains 1 row per testing cycle; it contains the cycle dates and
--	requirement goals (-1 if not required)

DROP TABLE IF EXISTS `#__ktbtracker_cycles` ;

-- 
-- Table structure for table `#__ktbtracker_candidates`
-- 
--	This table contains 1 row per candidate per cycle, recording cycle-based
-- 	requirements (not daily).

DROP TABLE IF EXISTS `#__ktbtracker_candidates` ;

-- 
-- Table structure for table `#__ktbtracker_masters`
-- 
--	This table contains 1 row per master candidate per journey, recording journey-based
-- 	requirements (not daily).

DROP TABLE IF EXISTS `#__ktbtracker_masters` ;

-- 
-- Table structure for table `#__ktbtracker_requirements`
-- 
--	This table includes 1 row per set of cycle goals. Candidate may be assigned different cycle
--  goals depending on age group or other circumstances. ID 0 is always the default goal set.

DROP TABLE IF EXISTS `#__ktbtracker_requirements` ;

-- 
-- Table structure for table `#__ktbtracker_tracking`
-- 
--	This table includes 1 row per day per candidate, recording all requirements
--	that can be recorded on a daily basis.

DROP TABLE IF EXISTS `#__ktbtracker_tracking` ;


ALTER TABLE #__ktbtracker_cycles_PRE300 RENAME AS #__ktbtracker_cycles;
ALTER TABLE #__ktbtracker_candidates_PRE300 RENAME AS #__ktbtracker_candidates;
ALTER TABLE #__ktbtracker_requirements_PRE300 RENAME AS #__ktbtracker_requirements;
ALTER TABLE #__ktbtracker_tracking_PRE300 RENAME AS #__ktbtracker_tracking;