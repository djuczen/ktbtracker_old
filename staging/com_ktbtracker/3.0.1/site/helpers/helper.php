<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

// Register RSMembership helper class
//JLoader::register('RSMembershipHelper', JPATH_ADMINISTRATOR . '/components/com_rsmembership/helpers/helper.php');

//jimport('Twilio.Services.Twilio');

JLoader::import('ktbtracker', JPATH_ADMINISTRATOR . '/components/com_ktbtracker/models');


/**
 * KTBTracker Component Helper class
 * 
 * @since	1.0.0
 */
 class KTBTrackerHelper
 {
	/**
	 * @var string The name of the component which the helper belongs.
	 */
	protected static $option = 'com_ktbtracker';

	/**
	 * @var    JObject  A cache for the available actions.
	 * @since  1.6
	 */
	protected static $actions;

	/**
	 * @var		JModelAdmin	An instance of the KTBTrackerModelCandidate.
	 * 
	 */
	protected static $candidate_model;
	
	/**
	 * @var		JModelAdmin	An instance of the KTBTrackerModelCycle.
	 *
	 */
	protected static $cycle_model;
	
	protected static $physical_requirements = array(
			'miles',
			'pushups',
			'situps',
			'burpees',
			'kicks',
			'poomsae',
			'self_defense',
			'sparring',
			'jumps',
			'pullups',
			'rolls_falls',
	);
	
	protected static $class_requirements = array(
			'class_saturday',
			'class_weekday',
			'class_sparring',
			'class_pmaa',
			'class_masterq',
			'class_dreamteam',
			'class_hyperpro',
	);
	
	protected static $other_requirements = array(
			'meditation',
			'raok',
			'mentor',
			'mentee',
			'leadership',
			'leadership2',
			'journals',
	);
	
	protected static $weekDays = array(
			'SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'
	);
	
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
				JText::_('COM_KTBTRACKER_DASHBOARD_SUBMENU_TITLE'),
				'index.php?option=com_ktbtracker',
				$vName == 'dashboard'
		);
		JHtmlSidebar::addEntry(
				JText::_('COM_KTBTRACKER_CYCLES_SUBMENU_TITLE'),
				'index.php?option=com_ktbtracker&view=cycles',
				$vName == 'cycles'
		);
		JHtmlSidebar::addEntry(
				JText::_('COM_KTBTRACKER_CANDIDATES_SUBMENU_TITLE'),
				'index.php?option=com_ktbtracker&view=candidates',
				$vName == 'candidates'
		);
		JHtmlSidebar::addEntry(
				JText::_('COM_KTBTRACKER_TRACKING_SUBMENU_TITLE'),
				'index.php?option=com_ktbtracker&view=tracking',
				$vName == 'tracking'
		);
		JHtmlSidebar::addEntry(
				JText::_('COM_KTBTRACKER_MASTERS_SUBMENU_TITLE'),
				'index.php?option=com_ktbtracker&view=masters',
				$vName == 'masters'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 *
	 * @since   1.6
	 * @todo    Refactor to work with notes
	 */
	public static function getActions()
	{
		if (empty(self::$actions))
		{
			$user = JFactory::getUser();
			self::$actions = new JObject;

			//dump(RSMembershipHelper::getUserSubscriptions($user->id), 'RSMembershipHelper::getUserSubscriptions');
			
			$actions = JAccess::getActions('com_ktbtracker');

			foreach ($actions as $action)
			{
				self::$actions->set($action->name, $user->authorise($action->name, 'com_ktbtracker'));
			}
		}

		return self::$actions;
	}

	/**
	 * A simple proxy for the getItem method of the KTBTrackerModleCandidate class.
	 *
	 * @return	JObject	the result from KTBTrackerModelCandidate::getItem
	 */
	public static function getCandidate($id)
	{
		if (empty($id)) return null; // short-circuit failure
		
		if (empty(self::$candidate_model)) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ktbtracker/models');
			self::$candidate_model = JModelAdmin::getInstance('Candidate', 'KTBTrackerModel', array('ignore_request' => true));
		}
	
		$candidate = self::$candidate_model->getItem($id);
	
		return $candidate;
	}
	
	/**
	 * A simple proxy for the getItem method of the KTBTrackerModleCycle class.
	 *
	 * @return	JObject	the result from KTBTrackerModelCycle::getItem
	 */
	public static function getCycle($id)
	{
		if (empty($id)) return null; // short-circuit failure
		
		if (empty(self::$cycle_model)) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ktbtracker/models');
			self::$cycle_model = JModelAdmin::getInstance('Cycle', 'KTBTrackerModel', array('ignore_request' => true));
		}
	
		$cycle = self::$cycle_model->getItem($id);
	
		return $cycle;
	}
	
	public static function getTrackingUser($trackingUser = null, $cycleid = null)
	{
		if (empty($trackingUser)) {
			$trackingUser = JFactory::getApplication()->input->get('trackingUser', null);
		}
		
		if (is_null($cycleid)) {
			$cycleid = self::getCycleId();
		}
		
		$user = JFactory::getUser($trackingUser);
		
		// See if the tracking user is on a cycle
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select($db->qn('id'));
		$query->from($db->qn('#__ktbtracker_candidates'));
		$query->where($db->qn('userid') . ' = ' . (int) $user->id);
		if (!empty($cycleid)) {
			$query->where($db->qn('cycleid') . ' = ' . (int) $cycleid);
		}
		$query->order($db->qn('cycleid') . ' DESC');
		$db->setQuery($query, 0, 1);

		return self::getCandidate($db->loadResult());
	}

	public static function getCycleCandidate($cycle, $trackingUser)
	{
		
	}
	
	public static function getCycleId($trackingDate = 'now')
	{
		$date = new JDate($trackingDate, JFactory::getConfig()->get('offset'));
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id');
		$query->from('#__ktbtracker_cycles AS a');
		$query->where($db->quote($date->toSql()) . ' BETWEEN ' .
				'  CASE WHEN a.cycle_prestart > ' . $db->quote($db->getNullDate()) .
				'   THEN a.cycle_prestart ELSE a.cycle_start END ' .
				' AND ' .
				'  CASE WHEN a.cycle_cutoff > ' . $db->quote($db->getNullDate()) .
				'   THEN a.cycle_cutoff ELSE a.cycle_finish END ' .
				' + INTERVAL 1 DAY', 'OR');
		// Make the current or next first in the list
		$query->order('a.cycle_start ASC');
		$db->setQuery($query, 0, 1);
	
		return $db->loadResult();
	}
	
	public static function getCurrentCycleId()
	{
		$cycle = self::getCurrentCycle();
		
		if (!empty($cycle)) {
			return $cycle->id;
		}
		
		return null;
	}
	
	public static function getCurrentCycle()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select($db->qn('id'));
		$query->from($db->qn('#__ktbtracker_cycles'));
		$query->where('CURDATE() BETWEEN ' .
				'  CASE WHEN ' . $db->qn('cycle_prestart') . ' > ' . $db->q($db->getNullDate()) .
				'   THEN ' .$db->qn('cycle_prestart') . ' ELSE ' . $db->qn('cycle_start') . ' END ' .
				' AND ' .
				'  CASE WHEN ' . $db->qn('cycle_cutoff') . ' > ' . $db->q($db->getNullDate()) .
				'   THEN ' .$db->qn('cycle_cutoff') . ' ELSE ' . $db->qn('cycle_finish') . ' END ' .
				' + INTERVAL 1 DAY', 'OR');
		$db->setQuery($query);
		
		return self::getCycle($db->loadResult());
	}
	
	public static function getLatestCycle($userid = null)
	{
		$user = JFactory::getUser($userid);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select($db->qn('MAX(cycleid)'));
		$query->from($db->qn('#__ktbtracker_candidates'));
		$query->where($db->qn('userid') . ' = ' . (int) $user->id);
		$db->setQuery($query);
		
		return self::getCycle($db->loadResult());
	}
	
	public static function getUserStatistics($candidate = null, $cycleId = null)
	{
		$trackingDate = JFactory::getApplication()->input->get('trackingDate', 'now');
		$trackingUser = JFactory::getApplication()->input->get('trackingUser', null);
		
		$user = JFactory::getUser($trackingUser);
		$cycle = null;
		$interval = null;
		
		$stats = new stdClass();
		
		
		// Attempt to get a candiate if one not presented
		if (empty($candidate)) {
			$canId = JFactory::getApplication()->input->get('canid', 0);
			
			if (!empty($canId)) {
				$candidate = self::getCandidate($canId);
			} else {
				$candidate = self::getTrackingUser();
			}
		}
		
		// If presented with a candidate, we know the user and the cycle
		if (!empty($candidate)) {
			$user = JFactory::getUser($candidate->userid);
			$cycle = self::getCycle($candidate->cycleid);
		}
		
		// Attempt to get a cycle if not presented
		if (empty($cycle)) {
			if (is_null($cycleId)) {
				$cycleId = JFactory::getApplication()->input->get('cycleid', self::getCycleId());
			}
			
			$cycle = self::getCycle($cycleId);
		}
		
		// If presented with a cycle, determine the interval data
		if (!empty($cycle)) {
			$interval = self::getCycleInterval($cycle, $trackingDate, $candidate);
		}
		
		$stats->candidate = $candidate;
		$stats->cycle = $cycle;
		$stats->interval = $interval;
		$stats->requirements = self::getCycleRequirements();
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// -------------------------------------------------------------------
		// Calculate the requirements tracking
		// -------------------------------------------------------------------
		
		$stats->tracking = self::getUserTotals($candidate);
		
		// -------------------------------------------------------------------
		// Gather the requirments goals
		// -------------------------------------------------------------------
		
		$stats->goals = self::getCycleGoalSet($candidate);
		
		// -------------------------------------------------------------------
		// Now calculate the progress
		// -------------------------------------------------------------------
		
		$progress = new stdClass();
		
		$progress_marker = min(($interval->day < 0) ? 0.0 : (($interval->day / $interval->days) * 100.0), 100.0);
		
		foreach ($stats->requirements as $requirement) {
		    $progress->$requirement = new stdClass();
		    
			// Assume the goal has NOT been met (or not tracked)
			$progress->$requirement->goal = $stats->goals->$requirement;
			$progress->$requirement->progress = 0.0;
			$progress->$requirement->remaining = $stats->goals->$requirement;
			$progress->$requirement->daily = 0;
			$progress->$requirement->target = 0;
			$progress->$requirement->help = '';
			$progress->$requirement->class = '';
			
			$total = $stats->tracking->$requirement;
			$goal  = $stats->goals->$requirement;
			
			// Determine the total progress towards the goal
			//if (!empty($goal) && !empty($total)) {
			$progress->$requirement->goal = $goal;
		    $progress->$requirement->progress = min((empty($goal)) ? 100.0 : (($total / $goal) * 100.0), 100.0);
		    $progress->$requirement->remaining = ($total >= $goal) ? 0 : ($goal - $total);
			$progress->$requirement->daily = ceil($goal / $interval->days);
			//$progress->$requirement->target = ($interval->day > 0) ? ($progress->$requirement->daily * $interval->day) : 0;
			$progress->$requirement->target = ($interval->day > 0) ? ceil(($progress_marker / 100.0) * $goal) : 0;
			
			$catchup = ($requirement == 'journals') ? 1 : ceil(($goal - $total) / (($interval->days - $interval->day)));
		    $within = empty($progress_marker) ? 1.0 : ($progress->$requirement->progress / $progress_marker);
		    
		    if ($within >= 0.90) {
		        $progress->$requirement->class = 'success';  // within 90%
		        $progress->$requirement->color = '#38b775';
		        $progress->$requirement->help = JText::sprintf(
		            ($requirement == 'journals') ? 'COM_KTBTRACKER_TOOLTIP_JOURNALS_SUCCESS' : 'COM_KTBTRACKER_TOOLTIP_PROGRESS_SUCCESS',
		            $progress->$requirement->progress, 
		            $progress->$requirement->color,
		            $progress_marker, 
		            number_format($progress->$requirement->target, 0), 
		            number_format($progress->$requirement->daily, 0));
		    } else if ($within >= 0.80 && $within < 0.90) {
		        $progress->$requirement->class = 'warning';  // within 80%
		        $progress->$requirement->color = '#ff5722';
		        $progress->$requirement->help = JText::sprintf(
		            ($requirement == 'journals') ? 'COM_KTBTRACKER_TOOLTIP_JOURNALS_WARNING' : 'COM_KTBTRACKER_TOOLTIP_PROGRESS_WARNING',
		            $progress->$requirement->progress, 
		            $progress->$requirement->color,
		            $progress_marker, 
		            number_format($progress->$requirement->target, 0), 
		            number_format($progress->$requirement->daily, 0), 
		            number_format($catchup, 0));
		    } else {
		        $progress->$requirement->class = 'danger';   // below 80%
		        $progress->$requirement->color = '#f44336';
		        $progress->$requirement->help = JText::sprintf(
		            ($requirement == 'journals') ? 'COM_KTBTRACKER_TOOLTIP_JOURNALS_DANGER' : 'COM_KTBTRACKER_TOOLTIP_PROGRESS_DANGER',
		            $progress->$requirement->progress, 
		            $progress->$requirement->color,
		            $progress_marker, 
		            number_format($progress->$requirement->target, 0), 
		            number_format($progress->$requirement->daily, 0), 
		            number_format($catchup, 0));
		    }
			//}
		}
		
		$stats->progress = $progress;
		
		dump($stats, 'KTBTrackerHelper::getUserStatistics');
		return $stats;
	}
	
	public static function getDateInterval($start_date = null, $finish_date = null, $reference_date = null, $date = 'now', $week_offset = 6)
	{
		$interval = new stdClass();
		
		if (is_null($start_date) || is_null($finish_date)) {
			return null;
		}
		
		if (is_null($reference_date)) {
			$reference_date = $start_date;
		}
		
		$full_intvl = date_diff(date_create($start_date), date_create($finish_date));
		$today_intvl = date_diff(date_create($reference_date), date_create($date));
		
		$interval->start_date = $start_date;
		$interval->finish_date = $finish_date;
		$interval->days = $full_intvl->days;
		$interval->week = ($today_intvl->invert) ? intval((($today_intvl->days + $week_offset) / 7) * -1) : intval((($today_intvl->days + $week_offset) / 7) + 1);
		$interval->day = ($today_intvl->invert) ? ($today_intvl->days * -1) : ($today_intvl->days + 1);
		
		if ($interval->week == 0) {
		    $interval->week = 1;
		}
		
		if ($interval->day >= 0) {
		    $interval->day += 1;
		}
		
		return $interval;
	}
	
	public static function getCycleInterval($cycle, $date = 'now', $candidate = null)
	{
		$interval = new stdClass();
		
		if (strtotime($cycle->cycle_prestart) > 0) {
		    $cycle_start = $cycle->cycle_prestart;
		} else {
		    $cycle_start = $cycle->cycle_start;
		}
		if (!empty($candidate) && strtotime($candidate->goal_start) > 0) {
			$cycle_start = $candidate->goal_start;
		}
		
		if (strtotime($cycle->cycle_cutoff) > 0) {
		    $cycle_finish = $cycle->cycle_cutoff;
		} else {
		    $cycle_finish = $cycle->cycle_finish;
		}
		$cycle_finish = $cycle->cycle_finish;  // HACK! - Don't include past actual cycle end (for now...)
		if (!empty($candidate) && strtotime($candidate->goal_finish) > 0) {
			$cycle_finish = $candidate->goal_finish;
		}
		
		return self::getDateInterval($cycle_start, $cycle_finish, $cycle->cycle_start, $date, $cycle->cycle_weekstart);
	}
	
	
	public static function getDate($date = 'now')
	{
		$calcDate = strtotime(JFactory::getDate((($date == null || is_numeric($date)) ? 'now' : $date),
				JFactory::getConfig()->get('offset'))->format('Y-m-d'));
		
		return $calcDate;
	}
	
	public static function getThisDate($date = 'now')
	{
		$thisDate = date('Y-m-d', strtotime(self::getDate($date)));
		
		return $thisDate;
	}
	
	public static function getPrevDate($date = 'now')
	{
		$prevDate = date('Y-m-d', strtotime('-1 days', self::getDate($date)));
	
		return $prevDate;
	}
	
	public static function getNextDate($date = 'now')
	{
		$nextDate = date('Y-m-d', strtotime('+1 days', self::getDate($date)));
		
		return $nextDate;
	}
	
	public static function getThisSunday($date = 'now')
	{
	    dump("KTBTrackerHelper::getThisSunday($date)...");
	    // WARNING: Weeks start on Monday (Sunday is PREVIOUS week)!
	    $thisDate = self::getDate($date);
		
		if (date('w', $thisDate) == 0) {
			$thisSunday = $thisDate;
		} else {
			$thisSunday = strtotime('last sunday', $thisDate);
		}
		$thisSunday = strtotime('-' . date('w', $thisDate) . ' days', $thisDate);
		
		dump(date('Y-m-d', $thisSunday), "KTBTrackerHelper::getThisSunday");
		return $thisSunday;
	}
	
	public static function getThisWeek($date = 'now', $weekStart = 6)
	{
	    dump("KTBTrackerHelper::getThisWeek($date, $weekStart)...");
	    // Find the current (Sunday-based) week the "cycle week" falls into
	    if (date('w', self::getDate($date)) >= $weekStart) {
		    $thisWeek = self::getThisSunday($date);
		} else {
			$thisWeek = strtotime('-' . (7 - $weekStart) . ' days', self::getThisSunday($date));
		}
		
		// Adjust the week to the "cycle week" start day
		$thisWeek = strtotime('+' . $weekStart . ' days', $thisWeek);
		
		dump(date('Y-m-d', $thisWeek), "KTBTrackerHelper::getThisWeek");
		return $thisWeek;
	}
	
	public static function getThisWeekDate($date = 'now', $weekStart = 6)
	{
		$thisDate = date('Y-m-d', self::getThisWeek($date, $weekStart));
		
		return $thisDate;
	}
	
	public static function getPrevWeekDate($date = 'now', $weekStart = 6)
	{
	    $prevDate = date('Y-m-d', strtotime('-1 week', self::getDate($date)));
	    
	    return $prevDate;
	}
	
	public static function getNextWeekDate($date = 'now', $weekStart = 6)
	{
	    $nextDate = date('Y-m-d', strtotime('+1 week', self::getDate($date)));
	    
	    return $nextDate;
	}
	
	public static function getWeekDates($date = 'now', $weekStart = 6)
	{    
	    dump("KTBTrackerHelper::getWeekDates($date, $weekStart)...");
	    $weekDates = array();
	    
	    $start = self::getThisWeek($date, $weekStart);

		$weekDates[0] = date('Y-m-d', $start);
		$weekDates[1] = date('Y-m-d', strtotime('+1 days', $start));
		$weekDates[2] = date('Y-m-d', strtotime('+2 days', $start));
		$weekDates[3] = date('Y-m-d', strtotime('+3 days', $start));
		$weekDates[4] = date('Y-m-d', strtotime('+4 days', $start));
		$weekDates[5] = date('Y-m-d', strtotime('+5 days', $start));
		$weekDates[6] = date('Y-m-d', strtotime('+6 days', $start));
		
		dump($weekDates, "KTBTrackerHelper::getWeekDates");
		return $weekDates;
	}
	
	public static function getWeekDays($weekStart = 6)
	{
		$weekDays = array();
		
		for ($d = 0; $d < 7; $d++) {
			$weekDays[] = JText::_(self::$weekDays[($d + $weekStart) % 7]);
		}
		
		return $weekDays;
	}
	
	public static function loadBootstrap()
	{
		$params = JComponentHelper::getParams(self::$option);
		$x = new Registry();
		
		if (JFactory::getApplication()->isSite()) {
			// Load in Twitter Bootstrap 3.x or default to active template
			if ($params->get('loadBS', 0)) {
				// Load in jQuery 2.x or default to active template
				if ($params->get('loadJQ', 0)) {
					// Pull in jQuery 2.2.4
					JHtml::script('https://code.jquery.com/jquery-2.2.4.min.js');
				}
	
				JHtml::script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
				JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
			} else {
				// Pull in the Joomla! default Bootstrap 2.3.2 and jQuery 1.x
				JHtml::_('bootstrap.framework');
			}
		} else {
			// Pull in the Joomla! default Bootstrap 2.3.2 and jQuery 1.x
			JHtml::_('bootstrap.framework');
		}
	}
	
	
	
	
	public static function getPhysicalRequirements($cycle = null)
	{
		$params = JComponentHelper::getParams(self::$option);
		$requirements = array();
		
		// If we are passed an empty cycle, if the current one if possible
		if (empty($cycle)) {
			$cycle = self::getCurrentCycle();
		}
		
		// Pull in Phyisical Requirements
		foreach (self::$physical_requirements as $column) {
			// Get the component or translated default (or 0 if not defined)
			$requirements[$column] = intval($params->get($column, JText::_('COM_KTBTRACKER_FIELD_' . $column . '_PLACEHOLDER')));
			
			// Override with any values defined in the current cycle (0 means not tracking)
			if (!empty($cycle) and property_exists($cycle, $column)) {
				$requirments[$column] = $cycle->$column;
			}
		}
		
		return $requirements;
	}
	
 	public static function getClassRequirements($cycle = null)
	{	
 		$params = JComponentHelper::getParams(self::$option);
		$requirements = array();
		
		// If we are passed an empty cycle, if the current one if possible
		if (empty($cycle)) {
			$cycle = self::getCurrentCycle();
		}
		
		// Pull in Additional Class Requirements
		foreach (self::$class_requirements as $column) {
			// Get the component or translated default (or 0 if not defined)
			$requirements[$column] = intval($params->get($column, JText::_('COM_KTBTRACKER_FIELD_' . $column . '_PLACEHOLDER')));
			
			// Override with any values defined in the current cycle (0 means not tracking)
			if (!empty($cycle) and property_exists($cycle, $column)) {
				$requirments[$column] = $cycle->$column;
			}
		}
		
		return $requirements;
	}
	
 	public static function getOtherRequirements($cycle = null)
	{	
 		$params = JComponentHelper::getParams(self::$option);
		$requirements = array();
		
		// If we are passed an empty cycle, if the current one if possible
		if (empty($cycle)) {
			$cycle = self::getCurrentCycle();
		}
		
		// Pull in Other Requirements
		foreach (self::$other_requirements as $column) {
			// Get the component or translated default (or 0 if not defined)
			$requirements[$column] = intval($params->get($column, JText::_('COM_KTBTRACKER_FIELD_' . $column . '_PLACEHOLDER')));
			
			// Override with any values defined in the current cycle (0 means not tracking)
			if (!empty($cycle) and property_exists($cycle, $column)) {
				if (!is_null($cycle->$column)) {
					$requirments[$column] = $cycle->$column;
				}
			}
		}
		
		return $requirements;
	}
	
	public static function getCycleRequirements($skip_virtuals=false)
	{
	    $reserved = array('id', 'title', 'alias', 'created', 'created_by', 'modified', 'modified_by', 'checked_out', 'checked_out_time');
	    $virtuals = array('journals');
	    
	    $db = JFactory::getDbo();
	    $db->setQuery("SHOW COLUMNS FROM #__ktbtracker_requirements");
	    $columns = array_values($db->loadColumn());
	    
	    if ($skip_virtuals) {
	        return array_diff($columns, $reserved, $virtuals);
	    }
	    
	    return array_diff($columns, $reserved);
	}
	
	public static function getCycleGoalSet($candidate)
	{
        // Create a new query object.
        $db = JFactory::getDbo();
        
        $cycle = self::getCycle($candidate->cycleid);

        $query = $db->getQuery(true);
        $query->select($db->qn(self::getCycleRequirements()));
        $query->from($db->qn('#__ktbtracker_requirements'));
        if (!empty($candidate->cycle_goals)) {
            $query->where($db->qn('id') . ' = ' . (int) $candidate->cycle_goals);
        } elseif (!empty($cycle->cycle_goals)) {
            $query->where($db->qn('id') . ' = ' . (int) $cycle->cycle_goals);
        } else {
            $query->where($db->qn('id') . ' = 1');
        }
        $db->setQuery($query);
        $goalSet = $db->loadObject();
        
        return $goalSet;
	}
	
	public static function getUserTotals($candidate)
	{
		// Determine the time zone offset of the site
		$tzoff = JHtml::date('now', 'P');
	
		// Local JDatabase object
		$db = JFactory::getDbo();
	
		// -------------------------------------------------------------------
		// Gather totals for the cycle (and split cycle), accounting for NULLS
		// -------------------------------------------------------------------
	
		// Determine cycle start/end dates
		$cycle = self::getCycle($candidate->cycleid);
		$intvl = self::getCycleInterval($cycle, 'now', $candidate);
	
		$req_columns = array();
		
        foreach (self::getCycleRequirements(true) as $req) {
             $req_columns[] = 'COALESCE(SUM(' . $db->qn($req) . '), 0) AS ' . $db->qn($req);    
        }
        
		$query = $db->getQuery(true);
		$query->select(implode(', ', $req_columns));
		$query->from('#__ktbtracker_tracking AS a');
		$query->leftJoin('#__ktbtracker_cycles AS c ON c.id = a.cycleid');
		$query->where('a.cycleid = ' . (int) $candidate->cycleid);
		$query->where('a.userid = ' . (int) $candidate->userid);
		$query->where('a.tracking_date BETWEEN ' .
				'DATE('.$db->quote(substr($intvl->start_date, 0, 10)).')' .
				' AND ' .
				'DATE('.$db->quote(substr($intvl->finish_date, 0, 10)).')');
		$db->setQuery($query);
	
		$totals = $db->loadObject();
	
		if ($candidate->cont_cycleid) {
	
			// Determine continued cycle start/end dates
			$cont_cycle = self::getCycle($candidate->cont_cycleid);
				
			if ($cont_cycle->publish_up > $db->getNullDate()) {
				$cont_start = $cont_cycle->publish_up;
			} else {
				$cont_start = $cont_cycle->cycle_start;
			}
			if ($cont_cycle->publish_down > $db->getNullDate()) {
				$cont_finish = $cont_cycle->publish_down;
			} else {
				$cont_finish = $cont_cycle->cycle_finish;
			}
				
			$query = $db->getQuery(true);
			$query->select(implode(', ', $req_columns));
			$query->from('#__ktbtracker_tracking AS a');
			$query->where('a.userid = ' . $candidate->userid);
			$query->where('a.cycleid = ' . (int) $candidate->cont_cycleid);
			$query->where('a.userid = ' . (int) $candidate->userid);
			$query->where('a.tracking_date BETWEEN '.
					'DATE('.$db->quote(substr($cont_start, 0, 10)).')' .
					' AND ' .
					'DATE('.$db->quote(substr($cont_finish, 0, 10)).')');
			$db->setQuery($query);
	
			$cont_totals = $db->loadObject();
				
			// Carry over the continue cycle values (up to the max).
			$goals = self::getCycleRequirements($cont_cycle);
			foreach (get_object_vars($totals) as $key => $value)
			{
				if (($goals[$key]->access == 0 || $goals[$key]->access == $candidate->access) && $goals[$key]->tract <= $candidate->tract)
				{
					$totals->$key += min($cont_totals->$key, ($goals[$key]->requirement * $goals[$key]->carryover_factor));
				}
			}
	
		}
	
		// -------------------------------------------------------------------
		// Include the journal entries (max of one per day)
		// -------------------------------------------------------------------
	
		// Select the required fields from the table.
		$query = $db->getQuery(true);
		$query->select('DATE(CONVERT_TZ(created,\'+00:00\',\''.$tzoff.'\')), COUNT(*)');
		//$query->from('#__k2_items');
		$query->from('#__easyblog_post');
		//$query->where('catid = 2');
		$query->where('created_by = '.(int) $candidate->userid);
		$query->where('created BETWEEN '.
				$db->quote($intvl->start_date).' AND '.$db->quote($intvl->finish_date).' + INTERVAL 1 DAY');
		$query->group('1');
	
		$db->setQuery($query);
		$db->execute();
	
		// Get the number of days where at least one journal entry was made
		$totals->journals = $db->getNumRows();
	
		// Return the totals object back to the caller
		return $totals;
	}
	
	
}
 
// Pure PHP - no closing required
