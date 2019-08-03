<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_ktbtracker
 * 
 * @copyright   Copyright (C) 2012-${COPYR_YEAR} David Uczen Photography, Inc. All rights reserved.
 * @license     GNU General Public License (GPL) version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;


// Make our helper class available
JLoader::register('KTBTrackerHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/ktbtracker.php');


// Get an instance of the controller 
$controller = BaseController::getInstance('KTBTracker');

// Perform the request task
$input = Factory::getApplication()->input;
$controller->execute($input->getCmnd('task'));

// Redirect
$controller->redirect();
