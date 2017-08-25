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
 * KTBTracker component HTML view for candidate list (Site).
 *
 * @since	1.0.0
 */
class KTBTrackerViewTracking extends JViewLegacy
{
	protected $items;
	
	protected $pagination;
	
	protected $state;
	
	
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @see     fetch()
	 * @since   12.2
	 */
	public function display($tpl = null)
	{	
		$this->items		 = $this->get('Items');
		$this->pagination	 = $this->get('Pagination');
		$this->state		 = $this->get('State'); dump($this->state, "State");
		
		$this->candidate     = $this->get('Candidate');
		$this->cycle         = $this->get('Cycle');
		$this->stats         = $this->get('Statistics');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		// Determine previous/next week links
		$baseURI = 'index.php?option=com_ktbtracker&view=tracking&canid='.(int) $this->candidate->id;
		$this->prevLink = JRoute::_($baseURI . '&trackingDate=' .
		    KTBTrackerHelper::getPrevWeekDate($this->items[3]->tracking_date), false);
		$this->nextLink = JRoute::_($baseURI . '&trackingDate=' .
		    KTBTrackerHelper::getNextWeekDate($this->items[3]->tracking_date), false);
		
		parent::display($tpl);
	}
	
}