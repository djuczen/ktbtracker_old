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

require_once JPATH_ADMINISTRATOR . '/components/com_ktbtracker/helpers/defines.php';


// Register KTB Tracker helper class
JLoader::register('KTBTrackerHelper', JPATH_ADMINISTRATOR . '/components/com_ktbtracker/helpers/helper.php');

// Get an instance of the controller prefixed by KTBTracker
$controller = JControllerLegacy::getInstance('KTBTracker');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

// Pure PHP - no closing required
