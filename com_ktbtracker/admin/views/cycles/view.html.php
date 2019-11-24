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
 * KTBTracker component HTML view class for cycle lists (Administration).
 * 
 * @since	1.0.0
 */
 class KTBTrackerViewCycles extends JViewLegacy
 {
 	/**
 	 * Display the the HTML list of cycles
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
 			KTBTrackerHelper::addSubmenu('cycles');
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
 		
 		JToolBarHelper::title(JText::_('COM_KTBTRACKER_MANAGER_CYCLES'), 'users');
 		
 		if ($canDo->get('core.create')) {
 			JToolBarHelper::addNew('cycle.add');
 		}
 		
 		if ($canDo->get('core.edit')) {
 			JToolBarHelper::editList('cycle.edit');
 		}
 		
 		if ($user->authorise('core.admin', 'com_ktbtracker')) {
 			JToolbarHelper::checkin('cycles.checkin', 'JTOOLBAR_CHECKIN', true);
 		}
 		
 		if ($canDo->get('core.delete')) {
 			JToolBarHelper::deleteList('', 'cycles.delete');
 		}
 		
 		if ($user->authorise('core.admin', 'com_ktbtracker') || $user->authorise('core.options', 'com_ktbtracker')) {
 			JToolBarHelper::preferences('com_ktbtracker');
 		}
 	}
 }
 
// Pure PHP - no closing required
