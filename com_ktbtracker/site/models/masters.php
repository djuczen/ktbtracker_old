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
 * KTBTracker component model for masters list (Site).
 * 
 * @since	1.0.0
 */
class KTBTrackerModelMasters extends JModelList
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
					'userid', 'a.userid', 'user_name',
					'tract', 'a.tract',
					'status', 'a.status',
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

		foreach ($items as $item) 
		{
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
		
		$trackingDate = $this->getUserStateFromRequest($this->option . '.tracking.date', 'trackingDate');
		$this->setState('tracking.date', $trackingDate);
		
		// Populate Filters
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
		$this->setState('filter.status', $status);
		
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
		$id	.= ':'.$this->getState('filter.status');
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
				'a.id, a.status, a.hidden, a.userid, ' .
				'a.checked_out, a.checked_out_time, a.created, a.created_by' 
			)
		);
		$query->from('#__ktbtracker_masters AS a');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');
		
		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		
		// Join over the users for the candidate name.
		$query->select('un.name AS user_name');
		$query->join('LEFT', '#__users AS un ON un.id = a.userid');
		
		// Filter by status (state).
		$status = $this->getState('filter.status');
		if (is_numeric($status)) {
			$query->where('a.status = ' . (int) $status);
		}
		
		// Filter by tract (access).
		$tract = $this->getState('filter.tract');
		if (is_numeric($tract)) {
			$query->where('a.tract = ' . (int) $tract);
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} elseif (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.name LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'user_name');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
	
	protected function addItemDetails(&$item)
	{
		$user = JFactory::getUser($item->userid);
		
		$item->username = $user->username;
		$item->name = $user->name;
		$item->email = $user->email;
		
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
	
}

// Pure PHP - no closing required
	