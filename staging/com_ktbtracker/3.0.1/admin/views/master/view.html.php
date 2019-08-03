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


/**
 * KTBTracker component HTML view for master forms (Administration). 
 * 
 * @since	1.0.0
 */
class KTBTrackerViewMaster extends JViewLegacy
{
	/** @var	JForm	form */
	protected $form = null;

	/**
	 * Display the candidate HTML form.
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
		
		// Hide the Joomla Administratior Maine Menu
		$input->set('hidemainmenu', true);
		
		$isNew =($this->item->id == 0);
		
		JToolBarHelper::title(JText::_('COM_KTBTRACKER_MANAGER_MASTER_'. ($isNew ? 'NEW' : 'EDIT')), 'pencil-2');
		JToolBarHelper::save('candidate.save');
		JToolBarHelper::cancel('candidate.cancel', ($isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'));
	}
}

// Pure PHP - no closing required
