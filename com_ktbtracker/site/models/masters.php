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

include_once JPATH_ROOT . '/components/com_jsn/helpers/helper.php';
include_once JPATH_ROOT . '/components/com_community/libraries/core.php';


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
                'cycleid', 'a.cycleid', 'cycle_name',
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
        $trackingUser = $this->getUserStateFromRequest($this->option . '.tracking.user', 'trackingUser', JFactory::getUser());
        $this->setState('tracking.user', $trackingUser);
        
        $trackingCycle = $this->getUserStateFromRequest($this->option . '.tracking.cycle', 'cycleid', KTBTrackerHelper::getCurrentCycle());
        $this->setState('tracking.cycle', $trackingCycle);
        
        $trackingDate = $this->getUserStateFromRequest($this->option . '.tracking.date', 'trackingDate', JHtml::_('date', 'now', 'Y-m-d'));
        $this->setState('tracking.date', $trackingDate);
        
        // Populate Filters
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $cycleId = $app->getUserStateFromRequest($this->context . '.filter.cycleid', 'filter_cycleid',
            KTBTrackerHelper::getCurrentCycleId());
        $this->setState('filter.cycleid', $cycleId);
        
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
        $id	.= ':'.$this->getState('filter.cycleid');
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
        // HACK - Calculate or get from component parameters
        $masterTracts = array(19, 20, 21, 22, 23, 24, 25);
        
        // Create a new query object.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();
        
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                $db->qn(array('a.id', 'a.status', 'a.tract', 'a.adult', 'a.hidden', 'a.userid', 'a.cycleid',
                    'a.checked_out', 'a.checked_out_time', 'a.created', 'a.created_by'))
                )
            );
        $query->from($db->qn('#__ktbtracker_candidates', 'a'));
        
        // Join over the users for the checked out user.
        $query->select($db->qn('uc.name', 'editor'));
        $query->join('LEFT', $db->qn('#__users', 'uc') . ' ON ' . $db->qn('uc.id') . ' = ' . $db->qn('a.checked_out'));
        
        // Join over the users for the author.
        $query->select($db->qn('ua.name', 'author_name'));
        $query->join('LEFT', $db->qn('#__users', 'ua') . ' ON ' . $db->qn('ua.id') . ' = ' . $db->qn('a.created_by'));
        
        // Join over the users for the candidate name.
        $query->select('CONCAT(' . $db->qn('un.name') . ', \' (\', ' . $db->qn('un.username') . ', \')\') AS user_name');
        $query->join('LEFT', $db->qn('#__users', 'un') . ' ON ' . $db->qn('un.id') . ' = ' . $db->qn('a.userid'));
        
        // Join over the cycles for the cycle name.
        $query->select($db->qn('cn.title', 'cycle_name'));
        $query->join('LEFT', $db->qn('#__ktbtracker_cycles', 'cn') . ' ON ' . $db->qn('cn.id') . ' = ' . $db->qn('a.cycleid'));
        
        // Join over the usergroups for the tract name.
        $query->select($db->qn('tn.title', 'tract_name'));
        $query->join('LEFT', $db->qn('#__usergroups', 'tn') . ' ON ' . $db->qn('tn.id') . ' = ' . $db->qn('a.tract'));
        
        // Join over the usergroups for the adult name.
        $query->select('(CASE ' . $db->qn('a.adult') .
            ' WHEN ' . 1 . ' THEN ' . $db->q(JText::_('COM_KTBTRACKER_OPTION_POOM')) .
            ' WHEN ' . 2 . ' THEN ' . $db->q(JText::_('COM_KTBTRACKER_OPTION_ADULT')) .
            ' WHEN ' . 3 . ' THEN ' . $db->q(JText::_('COM_KTBTRACKER_OPTION_MASTER')) . ' END) AS ' . $db->qn('adult_name'));
        
        // Hide hidden users (except to authorized users)
        if (!$user->authorise('core.edit', 'com_ktbtracker')) {
            $query->where($db->qn('hidden') . ' = 0');
        }
        
        // Restrict candidates to designated tracts
        $query->where($db->qn('a.tract') . ' IN (' . implode(',', $masterTracts) . ')');

        // Filter by cycle id.
        $cycleId = $this->getState('tracking.cycle');
        if (is_numeric($cycleId)) {
            $query->where($db->qn('a.cycleid') . ' = ' . (int) $cycleId);
        }
        
        // Filter by status (state).
        $status = $this->getState('filter.status');
        if (is_numeric($status)) {
            $query->where($db->qn('a.status') . ' = ' . (int) $status);
        }
        
        // Filter by tract (access).
        $tract = $this->getState('filter.tract');
        if (is_numeric($tract)) {
            $query->where($db->qn('a.tract') . ' = ' . (int) $tract);
        }
        
        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->qn('a.id') . ' = '.(int) substr($search, 3));
            } elseif (stripos($search, 'author:') === 0) {
                $search = $db->q('%'.$db->escape(substr($search, 7), true).'%');
                $query->where('(' . $db->qn('ua.name') . ' LIKE ' . $search . ' OR ' . $db->qn('ua.username') . ' LIKE ' . $search . ')');
            } else {
                $search = $db->q('%'.$db->escape($search, true).'%');
                $query->where('(' . $db->qn('a.name') . ' LIKE ' . $search . ')');
            }
        }
        
        // Add the list ordering clause.
        $orderCol	= $this->getState('list.ordering', 'user_name');
        $orderDirn	= $this->getState('list.direction', 'ASC');
        $query->order($db->escape($db->qn('cycleid') . ' DESC, ' . $db->qn($orderCol) . ' ' . $orderDirn));
        
        return $query;
    }
    
    protected function addItemDetails(&$item)
    {
        $user = JFactory::getUser($item->userid);
        
        $item->username = $user->username;
        $item->name = $user->name;
        $item->email = $user->email;
        
        $jsn_user = JsnHelper::getUser($item->userid);
        
        $item->display_name = $jsn_user->getFormatName();
        $item->online = $jsn_user->isOnline();
        $item->link = $jsn_user->getLink();
        
        $item->profile = JUserHelper::getProfile($item->userid);
        
        // Additional candidate information
        $candidate = KTBTrackerHelper::getCandidate($item->id);
        $item->last_tracked = $this->getLastTracked($candidate);
        $item->last_journaled = $this->getLastJournaled($candidate);
        
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
    
    protected function getLastTracked($candidate)
    {
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('MAX(' . $db->qn('tracking_date') . ')');
        $query->from($db->qn('#__ktbtracker_tracking'));
        $query->where($db->qn('userid') . ' = ' . (int) $candidate->userid);
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    
    protected function getLastJournaled($candidate)
    {
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('MAX(DATE(CONVERT_TZ(' . $db->qn('created') . ',\'+00:00\',\'' . JHtml::date('now', 'P') . '\')))');
        $query->from($db->qn('#__easyblog_post'));
        $query->where($db->qn('created_by') . ' = ' . (int) $candidate->userid);
        $db->setQuery($query);
        return $db->loadResult();
    }
}

// Pure PHP - no closing required
