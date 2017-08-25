<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

defined('JPATH_PLATFORM') or die;


/**
 * KTBTracker component HTML view for tracker lists (Administration).
 *
 * @since	1.0.0
 */
class KTBTrackerViewTracking extends JViewLegacy
{
 	/**
 	 * Display the the HTML list of tracking.
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
 			KTBTrackerHelper::addSubmenu('tracking');
 			$this->sidebar = JHtmlSidebar::render();
 			$this->addToolBar();
 		}
 		
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
 		$canDo = KTBTrackerHelper::getActions();
 		$user = JFactory::getUser();
 		
 		// Get the toolbar object instance
 		$bar = JToolbar::getInstance('toolbar');
 		
 		JToolBarHelper::title(JText::_('COM_KTBTRACKER_MANAGER_TRACKING'), 'signup');
 		
 		if ($canDo->get('core.create')) {
 			JToolBarHelper::addNew('tracker.add');
 		}
 		
 		if ($canDo->get('core.edit')) {
 			JToolBarHelper::editList('tracker.edit');
 		}
 		
 		if ($user->authorise('core.admin', 'com_ktbtracker')) {
 			JToolbarHelper::checkin('tracking.checkin', 'JTOOLBAR_CHECKIN', true);
 		}
 		
		if ($canDo->get('core.delete')) {
 			JToolBarHelper::deleteList('', 'tracking.delete');
 		}
 		
 		if ($user->authorise('core.admin', 'com_ktbtracker') || $user->authorise('core.options', 'com_ktbtracker')) {
 			JToolBarHelper::preferences('com_ktbtracker');
 		}
 	}
}

// Pure PHP - no closing required
