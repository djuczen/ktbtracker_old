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
 * KTBTracker component model for cycle forms (Administration).
 * 
 * @since	1.0.0
 */
 class KTBTrackerModelCycle extends JModelAdmin
 {
 	/**
 	 * Method to get a JTable ojbect, load it if necessary.
 	 * 
 	 * @param	string	$type	The table name (optional).
 	 * @param	string	$prefix	The class prefix (optional).
 	 * @param	array	$config	Configuration array for model (optiona).
 	 * 
 	 * @return	JTable	A JTable object
 	 * 
 	 * @since	1.0.0
 	 */
 	public function getTable($type = 'Cycles', $prefix = 'KTBTrackerTable', $config = array())
 	{
 		return JTable::getInstance($type, $prefix, $config);
 	}
 	
 	/**
 	 * Method to get the record form.
 	 * 
 	 * @param	array	$data	Data for the form.
 	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
 	 * 
 	 * @return	mixed	A JForm object on success, false on failure
 	 * 
 	 * @since 1.0.0
 	 */
 	public function getForm($data = array(), $loadData = true)
 	{
 		$app = JFactory::getApplication();
 		$user = JFactory::getUser();
 		
 		$id = $app->input->get('id', 0);
 		
 		// Get the form
 		$form = $this->loadForm('com_ktbtracker.cycle', 'cycle', array('control' => 'jform', 'load_data' => $loadData));
 		
 		if (empty($form)) {
 			return false;
 		}
 		
 		// Modify the form based on Edit State access
 		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_ktbtracker.cycle.' . (int) $id))
 				|| ($id == 0 && !$user->authorise('core.edit.state', 'com_ktbtracker'))) {
 			// Disable fields on display
 			$form->setFieldAttribute('published', 'disabled', 'true');
 			
 			// Disable fields on save
 			$form->setFieldAttribute('published', 'filter', 'unset');
 		}
 		
 		return $form;
 	}
 	
 	/**
 	 * Method to get the data that should be injected into the form.
 	 * 
 	 * @return	mixed	The data for the form.
 	 * 
 	 * @since	1.0.0
 	 */
 	protected function loadFormData()
 	{
 		$app = JFactory::getApplication();
 		
 		// Check the session for previously entered form data.
 		$data = $app->getUserState('com_ktbtracker.edit.cycle.data', array());
 		
 		if (empty($data)) {
 			$data = $this->getItem();
 			
 			// Prime some default values for add...
 			if ($this->getState('cycle.id') == 0) {
 				$filters = (array) $app->getUserState('com_ktbtracker.cycles.filter');
 			}
 		}

 		return $data;
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
 		// Generate the alias if empty
 		if (in_array(JFactory::getApplication()->input->get('task'),array('apply', 'save', 'save2new')) && empty($table->id))
 		{
 			if (empty($table->alias)) {
 				if (JFactory::getConfig()->get('unicodeslugs') == 1) {
 					$table->alias = JFilterOutput::stringURLUnicodeSlug($table->title);
 				} else {
 					$table->alias = JFilterOutput::stringURLSafe($table->title);
 				}
 			}
 		}
 		
 		// If fields are nullable, set them as so
 		//foreach (KTBTrackerHelper::getCycleRequirements($this) as $column) {
 		//	if (!strlen($table->$column)) {
 		//		$table->$column = null;
 		//	}
 		//}
 		
 		// Provide created and modified values
 	 	if (empty($table->id)) {
 			if (empty($table->created_by)) {
 				$table->created_by = JFactory::getUser()->id;
 			}
 			if (empty($table->created)) {
 				$table->created = JFactory::getDate()->toSql();
 			} 			
 		} else {
 			$table->modified_by = JFactory::getUser()->id;
 			$table->modified = JFactory::getDate()->toSql();
 		}
 	}
 	
 	/**
 	 * Returns the cycle object for the current cycle if today's date falls within the date range
 	 * of the current cycle, otherwise NULL is returned.
 	 * 
 	 * @return mixed|NULL The cycle object for the current cycle, or NULL if there is no current
 	 * 			cycle
 	 */
 	public function getCurrentCycle()
 	{
 		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);
 	
 		$query->select('*');
 		$query->from($db->qn('#__ktbtracker_cycles'));
 		$query->where('CURDATE() BETWEEN ' .
 				'  CASE WHEN ' . $db->qn('cycle_prestart') . ' > ' . $db->q($db->getNullDate()) .
 				'   THEN ' .$db->qn('cycle_prestart') . ' ELSE ' . $db->qn('cycle_start') . ' END ' .
 				' AND ' .
 				'  CASE WHEN ' . $db->qn('cycle_cutoff') . ' > ' . $db->q($db->getNullDate()) .
 				'   THEN ' .$db->qn('cycle_cutoff') . ' ELSE ' . $db->qn('cycle_finish') . ' END ' .
 				' + INTERVAL 1 DAY', 'OR');
 		$db->setQuery($query);
 	
 		return $db->loadObject();
 	}
 	
 	/**
 	 * Returns an array of cycle objects representing the cycles in which the specified candidate has
 	 * participated.
 	 * 
 	 * The array of cycle objects may be empty if no
 	 * @param unknown $trackingUser
 	 * @return mixed|NULL|unknown[]|mixed[]
 	 */
 	public function getCandidateCycles($trackingUser = null)
 	{
 		if (empty($trackingUser)) {
 			$trackingUser = JFactory::getApplication()->input->get('trackingUser', null);
 		}
 		
 		$user = JFactory::getUser($trackingUser);
 		
 		$db = JFactory::getDbo();
 		$query = $db->getQuery(true);
 		
 		$query->select('*');
 		$query->from($db->qn('#__ktbtracker_cycles'));
 		$query->where($db->qn('id') . ' IN ' . 
 				'SELECT ' . $db->qn('cycleid') .
 				' FROM ' . $db->qn('#__ktbtracker_candidates') .
 				' WHERE ' . $db->qn('userid') . ' = ' . (int) $user->id);
 		$query->order($db->qn('cycle_start') . ' ASC');
 		$db->setQuery($query);
 		
 		return $db->loadObjectList();
 	}
 }
 
// Pure PHP - no closing required
