<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

defined('JPATH_PLATFORM') or die;


/**
 * KTBTracker component model for tracking list (Administration).
 * 
 * @since	1.0.0
 */
class KTBTrackerModelTracking extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'tracking_date', 'a.tracking_date',
					'userid', 'a.userid', 'user_name',
			);
		}
	
		parent::__construct($config);
	}
		
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $item) {
			$this->addItemDetails($item);
		}
		
		return $items;
	}
	
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
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->getVar('layout')) {
			$this->context .= '.' . $layout;
		}

		// Populate Criteria
		$trackingUser = $this->getUserStateFromRequest($this->option . '.tracking.user', 'trackingUser');
		$this->setState('tracking.user', $trackingUser);
		
		$trackingCycle = $this->getUserStateFromRequest($this->option . '.tracking.cycle', 'cycleid');
		$this->setState('tracking.cycle', $trackingCycle);
		
		// Populate Filters
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$trackingDate = $this->getUserStateFromRequest($this->option . '.filter.tracking_date', 'filter_tracking_date');
		$this->setState('filter.tracking_date', $trackingDate);
		
		$userName = $app->getUserStateFromRequest($this->context . '.filter.user_name', 'filter_userid');
		$this->setState('filter.user_name', $userName);
		
		$cycleId = $app->getUserStateFromRequest($this->context . '.filter.cycle_id', 'filter_cycleid', 
				KTBTrackerHelper::getCurrentCycleId());
		$this->setState('filter.cycleid', $cycleId);

		$tract = $app->getUserStateFromRequest($this->context . '.filter.tract', 'filter_tract');
		$this->setState('filter.tract', $tract);		
		
		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.tracking_date');
		$id	.= ':'.$this->getState('filter.userid');
		$id	.= ':'.$this->getState('filter.cycleid');
		$id .= ':'.$this->getState('filter.tract');
		
		return parent::getStoreId($id);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.tracking_date, a.userid, ' .
				'a.checked_out, a.checked_out_time, a.created, a.created_by' 
			)
		);
		
		$query->select('(SELECT COUNT(*) FROM `#__easyblog_post` ' .
				'WHERE DATE(created) = a.tracking_date AND created_by = a.userid) AS journals');
		
		$query->from('#__ktbtracker_tracking AS a');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');
		
		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		
		// Join over the users for the candidate name.
		$query->select('un.name AS user_name');
		$query->join('LEFT', '#__users AS un ON un.id = a.userid');
		
		// Join over the cycles for the cycle name.
		$query->select('cn.title AS cycle_name');
		$query->join('LEFT', '#__ktbtracker_cycles AS cn ON cn.id = a.cycleid');
		
		$trackingDate = $this->getState('filter.tracking_date');
		if (!empty($status)) {
			$query->where('a.tracking_date = ' . $db-q(substr($trackingDate, 0, 10)));
		}
		
		$cycleid = $this->getState('filter.cycleid');
		if (!empty($cycleid)) {
			if (empty($trackingDate)) {
				$interval = KTBTrackerHelper::getCycleInterval(KTBTrackerHelper::getCycle($cycleid));
				if (!empty($interval)) {
					$query->where('a.tracking_date BETWEEN ' . 
							$db->q(substr($interval->start_date, 0, 10)) . 
							' AND ' . 
							$db->q(substr($interval->finish_date, 0, 10)));
				}
			} else {
				$query->where('a.cycleid = ' . (int) $cylceid);
			}
		}
		
		$userid = $this->getState('filter.userid');
		if (!empty($userName)) {
			$query->where('a.userid = ' . (int) $userid);
		}
		
		$tract = $this->getState('filter.tract');
		if (!empty($tract)) {
			$query->where('a.tract = ' . (int) $tract);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->getState('list.ordering', 'tracking_date');
		$orderDirn	= $this->getState('list.direction', 'desc');
		$query->order($db->escape($orderCol.' '.$orderDirn . ', user_name ASC'));
		
		return $query;
	}
	
	protected function addItemDetails(&$item)
	{
		// Additional user details
		$user = JFactory::getUser($item->userid);	
		$item->username = $user->username;
		$item->name = $user->name;
		$item->email = $user->email;
		
		return true;
		
		// Additional candidate details
		$candidate = KTBTrackerHelper::getCandidate($item->id);
		$item->last_tracked = $this->getLastJournaled($candidate);
		
		return true;
		
		$candidate = KTBTrackerHelper::getCandidate($item->id);
		$cycle = KTBTrackerHelper::getCycle($item->cycleid);
		
		$cand_total = 0;
		$goal_total = 0;
		foreach (get_object_vars($candidate->totals) as $key => $value) {
			if (($cycle->goals[$key]->access == 0 || $cycle->goals[$key]->access == $candidate->access) && $cycle->goals[$key]->tract <= $candidate->tract) {
				$cand_total += $value;
				if (!empty($candidate->$key)) {
					$goal_total += $candidate->$key;
				} else {
					$goal_total += $cycle->goals[$key]->requirement;
				}
			}
		}
		$item->totals = $candidate->totals;
		$item->overall = ($goal_total > 0) ? ($cand_total / $goal_total) : $cand_total;
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('MAX(created)');
		$query->from('#__easyblog_post');
		$query->where('created_by = ' . (int) $candidate->userid);
		$db->setQuery($query);
		$item->last_tracked = $db->loadResult();
		
		return true;
	}
	
	protected function getLastJournaled($candidate)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__easyblog_post');
		$query->where('created_by = ' . (int) $candidate->userid);
		$db->setQuery($query);
		return $db->loadResult();		
	}
}

// Pure PHP - no closing required
	