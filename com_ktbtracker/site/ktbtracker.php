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


// Register KTA Black Belt helper class
JLoader::register('KTBTrackerHelper', JPATH_ROOT .'/components/com_ktbtracker/helpers/helper.php');

// Start our master controller
$controller = JControllerLegacy::getInstance('KTBTracker');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

// Pure PHP - no closing required
