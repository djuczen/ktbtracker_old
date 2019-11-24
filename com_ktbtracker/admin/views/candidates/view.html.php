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
 * KTBTracker component HTML view for candidate lists (Administration).
 *
 * @since	1.0.0
 */
class KTBTrackerViewCandidates extends JViewLegacy
{
 	/**
 	 * Display the the HTML list of candidates.
 	 * 
 	 * @param	string	$tpl	The name of the template file to parse; automatically searches through the template paths.
 	 * 
 	 * @return	void
 	 * 
 	 * @since	1.0.0
 	 */
 	function display($tpl = null)
 	{
 		$app		= JFactory::getApplication();
 		
 		// Assign data to the view
 		$this->state		= $this->get('State');
 		$this->items 		= $this->get('Items');
 		$this->pagination	= $this->get('Pagination');
 		$this->filterForm	= $this->get('FilterForm');
 		$this->activeFilters = $this->get('ActiveFilters');
 		
 		// For non-modal views, add a sidebar and toolbar
 	 	if ($this->getLayout() !== 'modal') {
 			KTBTrackerHelper::addSubmenu('candidates');
 			$this->sidebar = JHtmlSidebar::render();
 			$this->addToolBar();
 		}
 		
 		// Check for errors.
 		if (count($errors = $this->get('Errors'))) {
 			$app->enqueueMessage(implode('<br />', $errors), 'error');
 			return false;
 		}
 		
 		JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
 		JHtml::stylesheet('com_ktbtracker/ktbtracker.css', array(), true);
 		
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
 		$canDo = KTBTrackerHelper::getActions();
 		$user = JFactory::getUser();
 		
 		// Get the toolbar object instance
 		$bar = JToolbar::getInstance('toolbar');
 		
 		JToolBarHelper::title(JText::_('COM_KTBTRACKER_MANAGER_CANDIDATES'), 'users');
 		
 		if ($canDo->get('core.create')) {
 			JToolBarHelper::addNew('candidate.add');
 		}
 		
 		if ($canDo->get('core.edit')) {
 			JToolBarHelper::editList('candidate.edit');
 		}
 		
 		if ($user->authorise('core.admin', 'com_ktbtracker')) {
 			JToolbarHelper::checkin('candidates.checkin', 'JTOOLBAR_CHECKIN', true);
 		}
 		
 		if ($canDo->get('core.edit.state')) {
			JToolbarHelper::custom('candidates.auditing', 'fa fa-eye', '', 'COM_KTBTRACKER_STATUS_AUDITING', true);
			JToolbarHelper::custom('candidates.active', 'fa fa-user', '', 'COM_KTBTRACKER_STATUS_CANDIDATE', true);
			JToolbarHelper::custom('candidates.graduated', 'fa fa-graduation-cap', '', 'COM_KTBTRACKER_STATUS_GRADUATE', true);
			JToolbarHelper::custom('candidates.dnf', 'fa fa-user-times', '', 'COM_KTBTRACKER_STATUS_DNF', true);
		}
		
		if ($canDo->get('core.delete')) {
 			JToolBarHelper::deleteList('', 'candidates.delete');
 		}
 		
 		if ($user->authorise('core.admin', 'com_ktbtracker') || $user->authorise('core.options', 'com_ktbtracker')) {
 			JToolBarHelper::preferences('com_ktbtracker');
 		}
 	}
}

// Pure PHP - no closing required
