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


// KTBTracker Component Defines
require_once JPATH_ADMINISTRATOR . '/components/com_ktbtracker/helpers/defines.php';

// Get our base class
JFormHelper::loadFieldClass('list');


/**
 * KTB Tracker component form field for cycle lists
 */
class JFormFieldStatusList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'StatusList';
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		$options[] = JHtml::_('select.option', STATUS_AUDITING, JText::_('COM_KTBTRACKER_STATUS_AUDITING'));
		$options[] = JHtml::_('select.option', STATUS_CANDIDATE, JText::_('COM_KTBTRACKER_STATUS_CANDIDATE'));
		$options[] = JHtml::_('select.option', STATUS_GRADUATE, JText::_('COM_KTBTRACKER_STATUS_GRADUATE'));
		$options[] = JHtml::_('select.option', STATUS_DNF, JText::_('COM_KTBTRACKER_STATUS_DNF'));
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}

// Pure PHP - no closing required
