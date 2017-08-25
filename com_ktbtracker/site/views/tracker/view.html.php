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


/**
 * KTBTracker component HTML view for tracker forms (Administration). 
 * 
 * @since	1.0.0
 */
class KTBTrackerViewTracker extends JViewLegacy
{
	/** @var	JForm	form */
	protected $form = null;

	/**
	 * Display the tracker HTML form.
	 *
	 * @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		
		// Assign data to the view
		$this->state	= $this->get('State'); dump($this->state, "State");
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		
		$this->cycle    = $this->get('Cycle');
		$this->candidate = $this->get('Candidate');
		$this->stats    = $this->get('Statistics');
			
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage(implode('<br />', $errors), 'error');
			return false;
		}
			
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
		
		// Hide the Joomla Administratior Maine Menu
		$input->set('hidemainmenu', true);
		
		$isNew =($this->item->id == 0);
		
		JToolBarHelper::title(JText::_('COM_KTBTRACKER_MANAGER_TRACKER_'. ($isNew ? 'NEW' : 'EDIT')), 'pencil-2');
		JToolBarHelper::save('tracker.save');
		JToolBarHelper::cancel('tracker.cancel', ($isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'));
	}
}

// Pure PHP - no closing required
