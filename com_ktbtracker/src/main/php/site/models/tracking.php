<?php
/**
 * @package		Joomla.Site
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Utilities\ArrayHelper;


/**
 * KTBTracker component model for tracking list (Site).
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
					'cycleid', 'a.cycleid', 'cycle_name',
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
	    // Properties for creating an empty tracking record
	    static $_emptyTrackingRecord = null;
	    
	    $dayNames = array(
	        JText::_('SUN'), JText::_('MON'), JText::_('TUE'), JText::_('WED'),
	        JText::_('THU'), JText::_('FRI'), JText::_('SAT'),
	    );
	    
	    // Get the empty record properties if we don't already have
	    if (empty($_emptyTrackingRecord))
	    {
	        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ktbtracker/tables');
	        $table = JTable::getInstance('Tracking', 'KTBTrackerTable');
	        $table->reset();
	        $_emptyTrackingRecord = $table->getProperties(true);
	    }
	    
	    // Get the application API and current user
	    $app	= JFactory::getApplication();
	    $user	= JFactory::getUser();
	    $db     = JFactory::getDbo();
	    
	    dump($user->authorise('core.edit', 'com_ktbtracker'), 'core.edit');
	    // Get the component parameters
	    $params = JComponentHelper::getParams($this->option);
	    
	    // Get the tracking user and date from the saved state, if possible
	    $trackingUser = $this->getState('tracking.user', $user->id);
	    $trackingDate = $this->getState('tracking.date', JHtml::_('date', 'now', 'Y-m-d', null));
	    
	    // Start with an empty list
	    $items = array();
	    
	    // Get requested candidate/cycle otherwise return empty array
	    $canid = $this->getState('tracking.canid');
	    if (!empty($canid)) {
	        $candidate = KTBTrackerHelper::getCandidate((int) $canid);
	        $cycle = KTBTrackerHelper::getCycle($candidate->cycleid);
	    } else {
	        return $items;
	    }
	    
	    
	    // Determine the week to display
	    $weekStart = $cycle->cycle_weekstart;
	    
	    $weekDates = KTBTrackerHelper::getWeekDates($trackingDate, $weekStart); //8-17
	    dump($weekDates, 'KTBTrackerHelper::getWeekDates(' . $trackingDate . ',' . $weekStart . ')');
	    
	    // -------------------------------------------------------------------
	    // Create an empty tracking record for each day of the displayed week
	    // -------------------------------------------------------------------
	    
	    foreach($weekDates as $d => $day) {
	        // Construct an "empty" database record for each week day
	        $item = ArrayHelper::toObject($_emptyTrackingRecord);
	        $item->tracking_day = $dayNames[($weekStart + $d) % 7];
	        $item->tracking_date = $weekDates[$d];
	        $item->userid = $candidate->userid;
	        $item->editable = false;
	        if ($user->authorise('core.create', 'com_ktbtracker')) {
	            if ($user->authorise('core.edit', 'com_ktbtracker') || $item->userid == $user->id) {
	                $item->editable = true;
	            }
	        }
	        $item->editLink = JRoute::_('index.php?option=com_ktbtracker&view=tracker&layout=edit&trackingDate='.$weekDates[$d].'&trackingUser='.$candidate->userid.'&cycleid='.$cycle->id);
	        //$item->editLink = JRoute::_('index.php?option=com_ktbtracker&view=tracker&layout=edit&trackingDate='.$weekDates[$d].'&canid='.$candidate->id);
	        $items[] = $item;
	    }
	    
	    // -------------------------------------------------------------------
	    // Collect the daily tracking records for the displayed week
	    // -------------------------------------------------------------------
	    
	    $days = array();
	    
	    // If we have a candidate, get the records
	    if ($candidate->id) {
	        
	        // Select the required fields from the table.
	        $query = $db->getQuery(true);
	        $query->select('*');
	        $query->from($db->qn('#__ktbtracker_tracking'));
	        $query->where($db->qn('cycleid') . ' = ' . (int) $candidate->cycleid);
	        $query->where($db->qn('userid') . ' = ' . (int) $candidate->userid);
	        $query->where($db->qn('tracking_date') . ' BETWEEN ' . $db->q($weekDates[0]) . ' AND ' .
	            $db->q($weekDates[6] ) . ' + INTERVAL 1 DAY');
	        
	        $db->setQuery($query);
	        
	        $days = $db->loadObjectList();
	        
	        // Check for a database error.
	        if ($db->getErrorNum())
	        {
	            $this->setError($db->getErrorMsg());
	            return false;
	        }
	        
	    }
	    
	    
	    // Populate our "empty" records with any returned records
	    foreach ($days as $d => $day)
	    {
	        foreach($items as $i => $item)
	        {
	            if ($day->tracking_date == $item->tracking_date)
	            {
	                // Copy the returned record properties
	                $item = $day;
	                // Reset "augmented" field(s)
	                $item->tracking_day = $dayNames[($weekStart + $i) % 7];
	                $item->editable = false;
	                dump($user->authorise('core.edit', 'com_ktbtracker'), 'core.edit');
	                if ($user->authorise('core.edit', 'com_ktbtracker'))
	                {
	                    $item->editable = true;
	                }
	                elseif ($user->authorise('core.edit.own', 'com_ktbtracker'))
	                {
	                    if ($item->created_by == $user->id || $item->userid == $user->id)
	                    {
	                        $item->editable = true;
	                    }
	                }
	                $item->editLink = JRoute::_('index.php?option=com_ktbtracker&task=tracker.edit&id='.(int) $item->id);
	                $items[$i] = $item;
	            }
	        } //endforeach;
	        
	    } //endforeach;
	    
	    
	    // -------------------------------------------------------------------
	    // Collect the journal entries
	    // -------------------------------------------------------------------
	    
	    // Determine the time zone offset of the site
	    $tzoff = JHtml::date('now', 'P');
	    
	    // Select the required fields from the table (K2 stores as UTC, so convert to local).
	    $query = $db->getQuery(true);
	    $query->select('COUNT(*) AS entries, DATE(CONVERT_TZ(created,\'+00:00\',\'' . $tzoff . '\')) AS tracking_date');
	    
        $query->from($db->qn('#__easyblog_post'));
	    $query->where($db->qn('created_by') . ' = ' . (int) $candidate->userid);
	    $query->where('DATE(CONVERT_TZ(created,\'+00:00\',\'' . $tzoff . '\')) BETWEEN '.
	        $db->q($weekDates[0]) . ' AND ' . $db->q($weekDates[6]) . ' + INTERVAL 1 DAY');
	    $query->group('tracking_date');
	    $query->order('tracking_date ASC');
	    $db->setQuery($query);
	        
	    $journals = $db->loadObjectList();
	        
        // If no rows, ensure we have an array
        if (empty($journals))
        {
            $journals = array();
        }
        
        // Augment our records with any returned records
        foreach ($journals as $j => $journal)
        {
            foreach($items as $i => $item)
            {
                if ($journal->tracking_date == $item->tracking_date)
                {
                    $item->reqmnt_blog = $journal->entries;
                }
            } //endforeach;
            
        } //endforeach;
	        
	    
        // -------------------------------------------------------------------
        // Collect the cycle goals
        // -------------------------------------------------------------------
        dump($items, 'KTBTrackerModelTracking::getItems()');
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
		$trackingUser = $this->getUserStateFromRequest($this->option . '.tracking.user', 'trackingUser', JFactory::getUser()->id);
		$this->setState('tracking.user', $trackingUser);
		
		$trackingCycle = $this->getUserStateFromRequest($this->option . '.tracking.cycle', 'cycleid', KTBTrackerHelper::getCurrentCycleId());
		$this->setState('tracking.cycle', $trackingCycle);
		
		$trackingDate = $this->getUserStateFromRequest($this->option . '.tracking.date', 'trackingDate', JHtml::_('date', 'now', 'Y-m-d'));
		$this->setState('tracking.date', $trackingDate);
		
		$trackingCanid = $this->getUserStateFromRequest($this->option . '.tracking.canid', 'canid');
		$this->setState('tracking.canid', $trackingCanid);
		
		// Populate Filters
    	$cycleId = $app->getUserStateFromRequest($this->context . '.filter.cycle_id', 'filter_cycle_id', KTBTrackerHelper::getCurrentCycleId());
		$this->setState('filter.cycle_id', $cycleId);
		
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
		$id	.= ':'.$this->getState('filter.cycle_id');
		
		return parent::getStoreId($id);
	}
	
	public function getCandidate()
	{
	    $canId = $this->getState('tracking.canid');
	    
	    if (!empty($canId)) {
	        $candidate = KTBTrackerHelper::getCandidate($canId);
	    } else {
	        $candidate = KTBTrackerHelper::getTrackingUser($this->getState('tracking.user'), $this->getState('tracking.cycle'));
	    }
	    
	    dump($candidate, "Model Candidate");
	    return $candidate;
	}
	
	public function getCycle()
	{
	    $candidate = $this->getCandidate();
	    
	    if (!empty($candidate)) {
	        $cycle = KTBTrackerHelper::getCycle($candidate->cycleid);
	    } else {
	        $cycle = KTBTrackerHelper::getCycle($this->getState('tracking.cycle'));
	    }
	    
	    return $cycle;
	}

	public function getStatistics()
	{
	    $candidate = $this->getCandidate();
	    
	    if (!empty($candidate)) {
	        $stats = KTBTrackerHelper::getUserStatistics($candidate);
	    } else {
	        $stats = KTBTrackerHelper::getUserStatistics();
	    }
	    
	    return $stats;
	}
	
	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since   11.1
	 */
	public function getTotal()
	{
	    return 7;
	}
	
	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 *
	 * @since   11.1
	 */
	public function getStart()
	{
	    return 0;
	}
	
}

// Pure PHP - no closing required
	