<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_ktbtracker
 *
 * @copyright   Copyright (C) 2012-${COPYR_YEAR} David Uczen Photography, Inc. All rights reserved.
 * @license     GNU General Public License (GPL) version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

class KTBTrackerModelKTBTracker extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     ListModel
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
     * @since   1.6
     */
    public function getItems()
    {
        static $_emptyTrackingRecord = null;
        
        if (empty($_emptyTrackingRecord))
        {
            $table = Table::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
            $table = Table::getInstance('Tracking', 'KTBTrackerTable');
            $table->reset();
            $_emptyTrackingRecord = $table->getProperties(true);
        }
        
        $items = parent::getItems();
        
        if (empty($items))
        {
            $items = array(ArrayHelper::toObject($_emptyTrackingRecord));
        }
        
        return $items;
    }
    
    /**
     * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
     *
     * @return  \JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        $trackingDate = new Date($this->getState('trackingDate', 'now'));
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__ktbtracker_tracking'));
        $query->where($db->qn('userid').' = '.(int) Factory::getUser()->get('id'));
        $query->where($db->qn('tracking_date').' = '.$db->quote($trackingDate->format('Y-m-d')));
        
        return $query;
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
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        
        $trackingDate = $app->getUserStateFromRequest("$this->option.ktbtracker.trackingDate", 'trackingDate');
        $this->setState('ktbtracker.date', $trackingDate);
        
        parent::populateState($ordering, $direction);
    }
    

    public function getCycleTrackingTotals($cycle, $userid = 323)
    {
        $totals = new stdClass();
        $db = $this->getDbo();
        
        $cycle_start    = new Date($cycle->cycle_start);
        if (!empty($cycle->cycle_prestart))
        {
            $cycle_start    = new Date($cycle->cycle_prestart);
        }
        $cycle_finish   = new Date($cycle->cycle_finish);
        
        $query = $db->getQuery(true);
        foreach (KTBTrackerHelper::getCycleRequirements() as $reqmnt) {
            if ($reqmnt == 'journals') continue;
            $query->select('SUM('.$db->qn($reqmnt).') AS '.$db->qn($reqmnt));
        }
        $query->from($db->qn('#__ktbtracker_tracking'));
        $query->where($db->qn('userid') . ' = ' . (int) 323 /*Factory::getUser($userid)->get('id')*/);
        $query->where($db->qn('tracking_date') . ' BETWEEN ' .
            $db->quote($cycle_start->format('Y-m-d')) .
            ' AND ' . 
            $db->quote($cycle_finish->format('Y-m-d')));
        
        $db->setQuery($query);
        
        $totals =  $db->loadObject();
        $totals->journals = 0; //$this->getBlogTotals($cycle_start, $cycle_finish, $userid);
        
        return $totals;
    }
    
    public function getDailyTrackingTotals()
    public function getBlogTotals($cycle_start, $cycle_finish, $userid = 323)
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select('DATE(CONVERT_TZ(created,\'+00:00\',\'' . HTMLHelper::date('now', 'P') . '\')), COUNT(*)');
        $query->from($db->qn('#__easyblog_post'));
        $query->where($db->qn('created_by') . ' = ' . (int) 323 /*Factory::getUser($userid)->get('id')*/);
        $query->where($db->qn('created') . ' BETWEEN '.
            $db->q($cycle_start) .
            ' AND ' .
            $db->q($cycle_finish) .
            ' + INTERVAL 1 DAY');
        $query->group('1');
        
        $db->setQuery($query);
        $db->execute();
        
        // Get the number of days where at least one journal entry was made
        return $db->getNumRows();
        
    }
}