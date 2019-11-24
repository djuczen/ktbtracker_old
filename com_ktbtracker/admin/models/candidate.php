<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// Include Easy Profile support
include_once JPATH_ROOT . '/components/com_jsn/helpers/helper.php';


/**
 * KTBTracker component model for candidate forms (Administration).
 * 
 * @since	1.0.0
 */
class KTBTrackerModelCandidate extends JModelAdmin
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
	public function getTable($type = 'Candidates', $prefix = 'KTBTrackerTable', $config = array())
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
		$form = $this->loadForm('com_ktbtracker.candidate', 'candidate', array (
				'control' => 'jform',
				'load_data' => $loadData 
		));
		
		if (empty($form)) {
			return false;
		}
		
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_ktbtracker.candidate.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_ktbtracker'))) {
			// Disable fields for display.
			$form->setFieldAttribute('userid', 'disabled', 'true');
			$form->setFieldAttribute('cycleid', 'disabled', 'true');
			$form->setFieldAttribute('cont_cycleid', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('userid', 'filter', 'unset');
			$form->setFieldAttribute('cycleid', 'filter', 'unset');
			$form->setFieldAttribute('cont_cycleid', 'filter', 'unset');
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_ktbtracker.edit.' . $this->getName() . '.data', array());
		
		// Attempt to get the record from the database
		if (empty($data)) {
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param 	integer	$pk		The id of the primary key.
	 *        	
	 * @return 	mixed 	Object on success, false on failure.
	 *        
	 * @since 	1.0.0
	 */
	public function getItem($pk = null) 
	{
		// Access the component parameters
		$params = JComponentHelper::getParams('com_ktbtracker');
		
		// Initialise variables.
		$pk = (! empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id' );
		$table = $this->getTable();
		
		if ($pk > 0) {
			// Attempt to load the row.
			$return = $table->load($pk);
			
			// Check for a table object error.
			if ($return === false && $table->getError()) {
				$this->setError($table->getError());
				return false;
			}
		}
		
		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(true);
		$item = JArrayHelper::toObject($properties, 'JObject');
		
		if (property_exists($item, 'params')) {
			$registry = new Registry();
			$registry->loadJSON($item->params);
			$item->params = $registry->toArray();
		}
		
		// Augment the item with any additional details;
		$this->addItemDetails($item);
		
		return $item;
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
	 * Method to set the candidate status.
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
			$this->setError(JText::_('JERROR_NO_ITEM_SELECTED'));

			return false;
		}

		$table = $this->getTable();
		$count = 0;
		
		foreach ($pks as $pk) {
			if ($table->load($pk)) {
				$table->status = (int) $value;
				$table->store();
			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Method to augment item record with additional details (properties).
	 *
	 * @param 	mixed 	$item	the item (candidate record) to be augmented.
	 * 
	 * @return	boolean	Always returns the value <code>true</code>.
	 */
	protected function addItemDetails(&$item) 
	{
		//$item->totals = KTBTrackerHelper::getUserTotals($item);
		$this->getUserInfo($item);
	}
	
	protected function getUserInfo(&$item) 
	{
		// Nothing to do if no record
		if (empty($item)) {
			return false;
		}
		
		// Assume we cannot find the user
		$item->name = 'Unknown Candidate';
		$item->username = 'nobody';
		$item->email = 'nobody@email.com';
		
		$user = JFactory::getUser($item->userid);
		
		// Augment with Joomla user data
		$item->name = $user->name;
		$item->username = $user->username;
		$item->email = $user->email;
		
		// Augment with user profile data
		$item->profile = JUserHelper::getProfile($item->userid);
		
		// Augment with Easy Profile data
		$jsn_user = JsnHelper::getUser($item->userid);
		$item->display_name = $jsn_user->getFormatName();
		$item->online = $jsn_user->isOnline();
		$item->link = $jsn_user->getLink();
		
		// Get the tract (belt rank) name
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('title'));
		$query->from($db->qn('#__usergroups'));
		$query->where($db->qn('id') . ' = ' . (int) $item->tract);
		$db->setQuery($query);
		$item->tract_name = $db->loadResult();
		
		return true;
	}
	
}

// Pure PHP - no closing required
