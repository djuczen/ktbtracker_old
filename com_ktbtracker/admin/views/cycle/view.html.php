<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * KTBTracker component HTML view for cycle forms (Administration). 
 * 
 * @since	1.0.0
 */
class KTBTrackerViewCycle extends JViewLegacy
{
	/** @var	JForm	form */
	protected $form = null;

	/**
	 * Display the cycle edit form
	 *
	 * @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		
		// Assign data to the view
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
			
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage(implode('<br />', $errors), 'error');
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}
			
		// Set the toolbar
		$this->addToolBar();
			
		// Display the view
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return	void
	 *
	 * @since	1.0.0
	 */
	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;
		
		// Hide the Joomla Administratior Main Menu
		$input->set('hidemainmenu', true);
		
		$isNew =($this->item->id == 0);
		
		if ($isNew) {
			$title = JText::_('COM_KTBTRACKER_MANAGER_CYCLE_NEW');
		} else {
			$title = JText::_('COM_KTBTRACKER_MANAGER_CYCLE_EDIT');	
		}
		
		JToolBarHelper::title($title, 'pencil-2');
		JToolBarHelper::save('cycle.save');
		JToolBarHelper::cancel('cycle.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
}

// Pure PHP - no closing required
