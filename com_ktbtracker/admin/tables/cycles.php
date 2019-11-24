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
 * KTBTracker component JTable class for cycles (Administration).
 *
 * The Cycles table class extends the Joomla! JTable and handles all CRUD processing
 * for the <code>#__ktbtracker_cycles</code> table.
 * 
 * @since 	1.0.0
 */
class KTBTrackerTableCycles extends JTable
{
	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   string     $table  Name of the table to model.
	 * @param   string     $key    Name of the primary key field in the table.
	 * @param   JDatabase  &$db    JDatabase connector object.
	 *
	 * @since   1.0.0
	 */
	function __construct(&$db)
	{
		parent::__construct('#__ktbtracker_cycles', 'id', $db);
	}
	
	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/store
	 * @since   3.0.0
	 */
	public function store($updateNulls = true)
	{
		return parent::store($updateNulls);
	}
}

// Pure PHP - no closing required
