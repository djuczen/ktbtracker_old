<?php
/**
 * @package		Joomla.Site
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

defined('JPATH_PLATFORM') or die;


/**
 * KTBTracker component model for main dashboard (Administration).
 * 
 * @since	3.0.0
*/

class KTBTrackerModelDashboard extends JModelLegacy
{
	/**
	 * @var		JModelAdmin	An instance of the KTBTrackerModelCandidate.
	 * 
	 */
	protected $candidate_model;
	
	/**
	 * @var		JModelAdmin	An instance of the KTBTrackerModelCycle.
	 *
	 */
	protected $cycle_model;
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->getVar('layout')) {
			$this->context .= '.' . $layout;
		}

		// Populate Criteria
		$trackingUser = $app->getUserStateFromRequest($this->option . '.tracking.user', 'trackingUser');
		$this->setState('tracking.user', $trackingUser);
		
		$trackingCycle = $app->getUserStateFromRequest($this->option . '.tracking.cycle', 'cycleid');
		$this->setState('tracking.cycle', $trackingCycle);
		
		$trackingDate = $app->getUserStateFromRequest($this->option . '.tracking.date', 'trackingDate');
		$this->setState('tracking.date', $trackingDate);
		
		$trackingCandidate = $app->getUserStateFromRequest($this->option . 'tracking.candidate', 'canid');
		$this->setState('tracking.candidate', $trackingCandidate);
		
		// List state information.
		parent::populateState();
	}
	
	/**
	 * A simple proxy for instantiating the KTBTrackerModelCandidate class.
	 *
	 * @return	JModelAdmin	the result from JModelAdmin::getInstance()
	 */
	protected function getCandidateModel()
	{
		if (empty($this->candidate_model)) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ktbtracker/models');
			$this->candidate_model = JModelAdmin::getInstance('Candidate', 'KTBTrackerModel', array('ignore_request' => true));
		}
		return $this->candidate_model;
	}
	
	/**
	 * A simple proxy for instantiating the KTBTrackerModleCycle class.
	 *
	 * @return	JModelAdmin	the result from JModelAdmin::getInstance()
	 */
	protected function getCycleModel()
	{
		if (empty($this->cycle_model)) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ktbtracker/models');
			$this->cycle_model = JModelAdmin::getInstance('Cycle', 'KTBTrackerModel', array('ignore_request' => true));
		}
		return $this->cycle_model;
	}
	
	public function getLifetimeStatistics()
	{
		return $this->getCycleStatistics(null, 0);
	}
	
	public function getCycleStatistics($candidate = null, $cycleId = null)
	{
		$trackingDate = $this->getState('tracking.date', 'now');
		$trackingUser = $this->getState('tracking.user', null);
		
		$user = JFactory::getUser($trackingUser);
		$cycle = null;
		$interval = null;
		
		$stats = new stdClass();
		
		
		// Attempt to get a candiate if one not presented
		if (empty($candidate)) {
			$canId = $this->getState('tracking.candidate', 0);
			
			if (!empty($canId)) {
				$candidate = $this->getCandidateModel()->getItem($canId);
			} else {
				$candidate = KTBTrackerHelper::getTrackingUser();
			}
		}
		
		// If presented with a candidate, we know the user and the cycle
		if (!empty($candidate)) {
			$user = JFactory::getUser($candidate->userid);
			$cycle = $this->getCycleModel()->getItem($candidate->cycleid);
		}
		
		// Attempt to get a cycle if not presented
		if (empty($cycle)) {
			if (is_null($cycleId)) {
				$cycleId = $this->getState('tracking.cycle', KTBTrackerHelper::getCycleId());
			}
			
			$cycle = $this->getCycleModel()->getItem($cycleId);
		}
		
		// If presented with a cycle, determine the interval data
		if (!empty($cycle)) {
			$interval = KTBTrackerHelper::getCycleInterval($cycle, $trackingDate, $candidate);
		}
		
		$stats->candidate = $candidate;
		$stats->cycle = $cycle;
		$stats->interval = $interval;
		$stats->requirements = KTBTrackerHelper::getCycleRequirements($cycle);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// -------------------------------------------------------------------
		// Calculate the requirements tracking
		// -------------------------------------------------------------------
		
		foreach ($stats->requirements as $requirement => $goal) {
			if ($requirement == 'journals') continue; // 'journals' is calculated below
			$query->select('SUM(' . $db->qn('a.' . $requirement) . ') AS ' . $db->qn($requirement));
		}
		$query->from($db->qn('#__ktbtracker_tracking', 'a'));
		$query->where($db->qn('userid') . ' = ' . (int) $user->id);
		if (!empty($interval)) {
			$query->where($db->qn('a.tracking_date') . ' BETWEEN ' .
					'  DATE(' . $db->q(substr($interval->cycle_start, 0, 10)) . ') ' .
					' AND ' .
					'  DATE(' . $db->q(substr($interval->cycle_finish, 0, 10)) . ') ' .
					' + INTERVAL 1 DAY');
		}
		$db->setQuery($query);
		
		$stats->tracking = $db->loadObject();
		
		// -------------------------------------------------------------------
		// Include the journal entries (max of one per day)
		// -------------------------------------------------------------------
		
		// Select the required fields from the table.
		$query = $db->getQuery(true);
		$query->select('DATE(CONVERT_TZ(created,\'+00:00\',\'' . JHtml::date('now', 'P') . '\')), COUNT(*)');
		$query->from($db->qn('#__easyblog_post'));
		$query->where($db->qn('created_by') . ' = ' . (int) $user->id);
		if (!empty($interval)) {
			$query->where($db->qn('created') . ' BETWEEN '.
					$db->q($interval->cycle_start) . 
					' AND ' . 
					$db->q($interval->cycle_finish) .
					' + INTERVAL 1 DAY');
		}
		$query->group('1');
		
		$db->setQuery($query);
		$db->execute();

		// Get the number of days where at least one journal entry was made
		$stats->tracking->journals = $db->getNumRows();
		
		// -------------------------------------------------------------------
		// Now calculate the progress
		// -------------------------------------------------------------------
		
		$progress = new stdClass();
		
		foreach ($stats->requirements as $requirement => $goal) {
			// Assume the goal has NOT been met (or not tracked)
			$progress->$requirement = 0.0;
			
			// Determine the total progress towards the goal
			if (!empty($goal) && !empty($stats->tracking->$requirement)) {
				$progress->$requirement = $stats->tracking->$requirement / $goal;
			}
			
			// Normalize the progress (0.0 - 1.0)  -- TODO later!
			//$progress[$requirement] = max(max($progress[$requirement], 1.0), 1.0);
		}
		$stats->progress = $progress;
		
		return $stats;
	}
	
	
	public function getCurrentCycle()
	{
		return $this->getCycleModel()->getCurrentCycle();
	}
	
	public function getCandidateCycles()
	{
		return $this->getCycleModel()->getCandidateCycles();
	}
}

// Pure PHP - no closing required
