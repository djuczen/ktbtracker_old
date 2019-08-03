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

use Joomla\CMS\Table\Table;


/**
 * KTBTracker Requirements Table
 *
 * The Tracking table class extends the Joomla! Table class and handles all CRUD processing
 * for the <code>#__ktbtracker_tracking</code> table.
 */
class KTBTrackerTableRequirements extends Table
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
     * @since   11.1
     */
    public function __construct($db)
    {
        parent::__construct('#__ktbtracker_requirements', 'id', $db);
    }
}
