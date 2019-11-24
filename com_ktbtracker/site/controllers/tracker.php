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


/**
 * KTBTracker component task controller for tracking forms (Site).
 * 
 * @since	1.0.0
*/
class KTBTrackerControllerTracker extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_KTBTRACKER_TRACKER';
	
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $view_list = 'tracking';
	
	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
	    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
	    $user = JFactory::getUser();
	    
	    // Guests cannot edit
	    if ($user->guest) {
	        return false;
	    }
	    
	    // If user can edit all records, then allow
	    if ($user->authorise('core.edit', $this->option)) {
	        return true;
	    }
	    
	    // If user created record or is being tracked, then allow
	    if ($user->authorise('core.edit.own', $this->option)) {
	        $owner = (int) isset($data['created_by']) ? $data['created_by'] : 0;
	        $tracked = (int) isset($data['userid']) ? $data['userid'] : 0;
	        
	        if ((empty($owner) || empty($tracked)) && $recordId) {
	            $record = $this->getModel()->getItem($recordId);
	            
	            if (empty($record)) {
	                return false;
	            }
	            
	            $owner = $record->created_by;
	            $tracked = $record->userid;
	        }
	        
	        if ($owner == $user->id || $tracked == $user->id) {
	            return true;
	        }
	        
	        // Since there is no asset tracking, revert to the parent
	        return parent::allowEdit($data, $key);
	    }
	}
	
	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the record can be added, a JError object if not.
	 *
	 * @since   1.0.0
	 */
	public function add()
	{
	    $result = parent::add();
	    
	    return $result;
	}
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   1.0.0
	 */
	public function cancel($key = null)
	{
	    $result = parent::cancel($key);
	    
	    return $result;
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   1.0.0
	 */
	public function edit($key = null, $urlVar = null)
	{
	    $result = parent::edit($key, $urlVar);
	    
	    return $result;
	}
	
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.0.0
	 */
	public function save($key = null, $urlVar = null)
	{
	    $result = parent::save($key, $urlVar);
	    
	    return $result;
	}
}

// Pure PHP - no closing required
