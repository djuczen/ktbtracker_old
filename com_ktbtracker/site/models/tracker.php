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

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

include_once JPATH_ROOT . '/components/com_jsn/helpers/helper.php';


/**
 * KTBTracker component model for tracker forms (Site).
 * 
 * @since	1.0.0
 */
class KTBTrackerModelTracker extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param 	string 	$name	  The table name. Optional.
	 * @param 	string 	$prefix	  The class prefix. Optional.
	 * @param 	array 	$options  Configuration array for model. Optional.
	 *        	
	 * @return 	JTable 	A JTable object
	 *        
	 * @since 	1.0.0
	 */
	public function getTable($type = 'Tracking', $prefix = 'KTBTrackerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param 	array 	$data		Data for the form.
	 * @param 	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *        	
	 * @return 	mixed 	A JForm object on success, false on failure
	 *        
	 * @since 	1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
 		$app = JFactory::getApplication();
 		$user = JFactory::getUser();
 		
 		$id = $app->input->get('id', 0);
		
		// Get the form.
		$form = $this->loadForm('com_ktbtracker.tracker', 'tracker', array (
				'control' => 'jform',
				'load_data' => $loadData 
		));
		
		if (empty($form)) {
			return false;
		}
		
		// If an existing record, don't change any keys!
		if (!empty($id)) {
    		// Disable fields while saving.
    		// The controller has already verified this is an article you can edit.
    		$form->setFieldAttribute('userid', 'filter', 'unset');
    		$form->setFieldAttribute('cycleid', 'filter', 'unset');
    		$form->setFieldAttribute('tracking_date', 'filter', 'unset');
		}
		
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return 	array 	The default data is an empty array.
	 *        
	 * @since 	1.0.0
	 */
	protected function loadFormData() 
	{
	    $app = JFactory::getApplication();
	    
		// Check the session for previously entered form data.
		$data = $app->getUserState('com_ktbtracker.edit.' . $this->getName() . '.data', array());
		dump($data, 'loadFormData (saved)');
		// Attempt to get the record from the database
		if (empty($data)) {
			$data = $this->getItem();
		}
        dump($data, 'loadFormData (DB)');
        
		// If a new record, pre-populate our hidden keys
		if (empty($data->id)) {
		    $data->tracking_date = $this->getState('tracking.date');
		    $data->userid = $this->getState('tracking.user');
		    $data->cycleid = $this->getState('tracking.cycle');
		}
		
		dump($data, 'loadFormData (final)');
		return $data;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   3.0
	 */
	protected function populateState()
	{
	    // Initialise variables.
	    $app = JFactory::getApplication();
	    $session = JFactory::getSession();
	    
	    // Populate Criteria
	    $trackingUser = $app->getUserStateFromRequest($this->option . '.tracking.user', 'trackingUser');
	    $this->setState('tracking.user', $trackingUser);
	    
	    $trackingCycle = $app->getUserStateFromRequest($this->option . '.tracking.cycle', 'cycleid');
	    $this->setState('tracking.cycle', $trackingCycle);
	    
	    $trackingDate = $app->getUserStateFromRequest($this->option . '.tracking.date', 'trackingDate');
	    $this->setState('tracking.date', $trackingDate);
	    
	    $trackingCanId = $app->getUserStateFromRequest($this->option . '.tracking.canid', 'canid');
	    $this->setState('tracking.canid', $trackingCanId);
	    
	    // List state information.
	    parent::populateState();
	}
	
	/**
 	 * Prepare and sanitise the table data prior to saving.
 	 *
 	 * @param   JTable  $table  A reference to a JTable object.
 	 *
 	 * @return  void
 	 *
 	 * @since   1.0.0
 	 */
 	protected function prepareTable($table)
 	{
 		// Provide created and modified values
 	 	if (empty($table->id)) {
 			if (empty($table->created_by)) {
 				$table->created_by = JFactory::getUser()->id;
 			}
 			if (empty($this->created)) {
 				$table->created = JFactory::getDate()->toSql();
 			} 			
 		} else {
 			$table->modified_by = JFactory::getUser()->id;
 			$table->modified = JFactory::getDate()->toSql();
 		}
 	}
 	
 	/**
 	 * Method to save the form data.
 	 *
 	 * @param   array  $data  The form data.
 	 *
 	 * @return  boolean  True on success, False on error.
 	 *
 	 * @since   1.0.0
 	 */
 	public function save($data)
 	{
 	    return parent::save($data);
 	}
 	
 	/**
	 * Method to set the tracker status.
	 *
	 * @param   array    $pks    The ids of the items to update.
	 * @param   integer  $value  The value to set the status to.
	 *
	 * @return  boolean  True on success.
	 */
	public function setStatus($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = ArrayHelper::toInteger($pks);

		if (empty($pks)) {
			JFactory::getApplication()->enqueueMessage(JText::_('JERROR_NO_ITEM_SELECTED'), 'error');

			return false;
		}

		$table = $this->getTable();
		$count = 0;
		
		foreach ($pks as $pk) {
			if ($table->load($pk)) {
				$table->status = (int) $value;
				$table->store();
			} else {
			    JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
				return false;
			}
		}

		return true;
	}
		
	public function getCandidate()
	{
	    $canId = $this->getState('tracking.canid');
	    
	    if (!empty($canId)) {
	        $candidate = KTBTrackerHelper::getCandidate($canId);
	    } else {
            $trackingUser = $this->getState('tracking.date');
            $cycleid = $this->getState('tracking.cycle');
            
            $candidate = KTBTrackerHelper::getTrackingUser($trackingUser, $cycleid);
        }
        
	    dump($candidate, "Model Candidate");
	    return $candidate;
	}
	
	public function getCycle()
	{
	    $trackerId = $this->getState('tracker.id');
	    
	    if (!empty($trackerId)) {
	        $item = $this->getItem($trackerId);
	        
	        $cycle = KTBTrackerHelper::getCycle($item->cycleid);
	    } else {
	        $cycleid = $this->getState('tracking.cycle');
	        
	        $cycle = KTBTrackerHelper::getCycle($cycleid);
	    }
	    
	    dump($cycle, "Model Cycle");
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
	
}

// Pure PHP - no closing required
