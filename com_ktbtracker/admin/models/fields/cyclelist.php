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


// Get our base class
JFormHelper::loadFieldClass('list');


/**
 * KTB Tracker component form field for cycle lists
 */
class JFormFieldCycleList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'CycleList';
	
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

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.cycle_start, a.id AS value, a.title AS text');
		$query->from('#__ktbtracker_cycles AS a');
		$query->order('a.cycle_start DESC');

		$db->setQuery($query);
		
		$optlist = $db->loadObjectList();
		
		// Create the additional options from the query
		foreach ($optlist as $opt) {
			$options[] = JHtml::_('select.option', $opt->value, $opt->text);
		}
				
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}

// Pure PHP - no closing required
