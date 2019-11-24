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
 * KTBTracker component task controller for tracking forms (Administration).
 * 
 * @since	1.0.0
*/
class KTBTrackerControllerTracker extends JControllerForm
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_KTBTRACKER_TRACKER';
	
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $view_list = 'tracking';
	
}

// Pure PHP - no closing required
