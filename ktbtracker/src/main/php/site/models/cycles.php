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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\User\User;
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
        static $_emptyCycleRecord = null;
        
        if (empty($_emptyCycleRecord))
        {
            $table = Table::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
            $table = Table::getInstance('Cycles', 'KTBTrackerTable');
            $table->reset();
            $_emptyCycleRecord = $table->getProperties(true);
        }
        
        $items = parent::getItems();
        
        if (empty($items))
        {
            $items = array(ArrayHelper::toObject($_emptyCycleRecord));
        }
        
        return $items;
    }
    
    public function getItem($id = null)
    {
        
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
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__ktbtracker_cycles'));
        $query->order($db->qn('cycle_start').' DESC');
        
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
        parent::populateState($ordering, $direction);
    }
}
