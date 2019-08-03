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
 * KTB Tracker component master controller (Site).
 */
class KTBTrackerController extends JControllerLegacy
{

	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $default_view = 'dashboard';

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since   1.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
	    $view = $this->input->get('view', $this->default_view);
	    $layout = $this->input->get('layout', 'default');
	    $id = $this->input->get('id');
	    
	    // Check for edit forms...
	    if ($view == 'tracker' && $layout == 'edit' && !$this->checkEditId('com_ktbtracker.edit.tracker', $id)) {
	        $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
	        $this->setMessage($this->getError(), 'error');
	        $this->setRedirect(JRoute::_('index.php?option=com_ktbtracker&view=tracking', false));
	    }
	    
	    // Default view processing
	    parent::display($cachable, $urlparams);
	    
	    // Support for JObject chaining
	    return $this;
	}
	
}

// Pure PHP - no closing required

