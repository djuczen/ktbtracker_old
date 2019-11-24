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
 * KTBTracker component task controller for main dashboard (Administration).
 * 
 * @since	3.0.0
*/
class KTBTrackerControllerDashboard extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_KTBTRACKER_DASHBOARD';

	/**
	 * Constructor.
	 * 
	 * @param 	array $config An optional associative array of configuration settings.
	 * 
	 * @return 	KTBTrackerControllerCandidates
	 * @see 	JController
	 * @since	1.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Dashboard', $prefix = 'KTBTrackerModel', $config =  array('ignore_request' => true)) 
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}

// Pure PHP - no closing required 
