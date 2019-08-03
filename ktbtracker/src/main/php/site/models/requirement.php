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

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Table\Table;


class KTBTrackerModelRequirement extends AdminModel
{
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table   A Table object
     *
     * @since   3.0
     * @throws  Exception
     */
    public function getTable($name = 'Requirements', $prefix = 'KTBTrackerTable', $options = array())
    {
        return Table::getInstance($name, $prefix, $options);
    }
    
    /**
     * Abstract method for getting the form from the model.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  FormModel|boolean  A Form object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_ktbtracker.requirement', 'requirement', array(
            'control'   => 'jform',
            'load_data' => $loadData,
        ));
        
        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }
    
    public function getLatest()
    {
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);
        $query->select($db->qn('id'));
        $query->from($db->qn('#__ktbtracker_requirements'));
        $query->where($db->qn('id').' IN (SELECT MAX(id) FROM #__ktbtracker_requirements)');
        $db->setQuery($query);
        
        return $this->getItem($db->loadResult());
    }
    
}
