<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;


/**
 * KTBTracker component model for cycle lists (Administration)
 * 
 * @since	1.0.0
 */
 class KTBTrackerModelCycles extends JModelList
 {
 	function __construct($config = array())
 	{
 		if (empty($config['filter_fields'])) {
 			$config['filter_fields'] = array(
 					'id', 'a.id',
 					'title', 'a.title',
 					'alias', 'a.alias',
 					'checked_out', 'a.checked_out',
 					'checked_out_time', 'a.checked_out_time',
 					'created_user_id', 'a.created_user_id',
 					'created_by_alias', 'a.created_by_alias',
 					'created_time', 'a.created_time',
 				);
 		}
 		
 		parent::__construct($config);
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
 		$app = JFactory::getApplication();
 		
 		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
 		$this->setState('filter.search', $search);
 		
 		parent::populateState('a.cycle_start', 'desc');
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
		// Add the list state to the store id.
		$id .= ':' . $this->getState('filter.search');

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
 		// Initialize variables
 		$db		= JFactory::getDbo();
 		$query	= $db->getQuery(true);
 			
 		// Create the base select statement
 		$query->select('a.*');
		$query->from($db->qn('#__ktbtracker_cycles', 'a'));

 		$search = $this->getState('filter.search');
 		if (!empty($search)) {
 			if (stripos('id:', $search) === 0) {
 				$query->where($db->qn('a.id') . ' = ' . (int) substr($search, 3));
 			} else {
 				$search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
 				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
 			}
 		}
 		
 		$orderCol = $this->getState('list.ordering', 'a.cycle_start');
 		$orderDirn = $this->getState('list.direction', 'desc');
 		
 		$query->order($db->escape($orderCol . ' ' . $orderDirn));
 		
 		return $query;
 	}
 	
 	/**
 	 * Method to get an array of data items.
 	 *
 	 * @return  mixed  An array of data items on success, false on failure.
 	 *
 	 * @since   1.0.0
 	 */
 	public function getItems()
 	{
 		
 		if ($items = parent::getItems())
 		{ 		
 		}
	 		
 		return $items;
 	}
 	
 }

// Pure PHP - no closing required
