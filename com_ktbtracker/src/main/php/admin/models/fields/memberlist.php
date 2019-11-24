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


// Get our base class
JFormHelper::loadFieldClass('list');


/**
 * KTB Tracker component form field for candidate lists
 */
class JFormFieldMemberList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'MemberList';
	
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

		$query->select($db->qn('a.id') . ' AS value, CONCAT(' . $db->qn('a.name') . ', \' (\', ' . $db->qn('a.username') . ', \')\') AS text');
		$query->from($db->qn('#__users', 'a'));
		
		// If a groups filter is specified, use it
		if (count($this->getGroups())) {
		    $subQuery = $db->getQuery(true);
		    $subQuery->select($db->qn('userid'));
		    $subQuery->from($db->qn('#__user_usergroup_map'));
		    $subQuery->where($db->qn('group_id') . ' IN ' . $db->q(explode(',', $this->getGroups())));
			$subselect  = '';
			$subselect .= 'SELECT userid ';
			$subselect .= 'FROM #__user_usergroup_map ';
			$subselect .= 'WHERE group_id IN \''.explode(',', $this->getGroups()).'\'';
			
			$query->where($db->qn('a.id') . ' IN (' . (string) $subQuery . ')');
		}
		
		// If a exluded user filter is specified, use it
		if (count($this->getExcluded())) {
			$query->where($db->qn('a.id') . ' NOT IN ' . $db->q(explode(',', $this->getExcluded())));
		}
		
		$query->order($db->qn('a.name') . ' ASC');

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

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 *
	 * @since   11.1
	 */
	protected function getGroups()
	{
		$groups = $this->element['groups'] ? preg_split('/[\s,]+/', $this->element['groups'], -1, PREG_SPLIT_NO_EMPTY) : array();
		
		if (empty($groups)) {
			return null;
		}
		
		return $groups;
	}
	
	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   11.1
	 */
	protected function getExcluded()
	{
		$excluded = $this->element['excluded'] ? preg_split('/[\s,]+/', $this->element['excluded'], -1, PREG_SPLIT_NO_EMPTY) : array();
		
		if (empty($excluded)) {
			return null;
		}
		
		return $excluded;
	}
}

// Pure PHP - no closing required
