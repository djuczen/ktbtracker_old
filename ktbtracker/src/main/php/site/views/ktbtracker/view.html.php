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

use Joomla\CMS\MVC\View\HtmlView;


/**
 *
 */
class KTBTrackerViewKTBTracker extends HtmlView 
{
    protected $items;
    
    protected $cycle;
    
    protected $reqmnts;
    
    protected $totals;
    
    protected $stats;
    
    
    function display($tpl = null)
    {
        $this->items        = $this->get('Items');
        $this->cycle        = $this->get('CurrentCycle', 'Cycle');
        $this->reqmnts      = $this->get('Latest', 'Requirement');
        
        if (!empty($this->items[0]->cycleid))
        {
            if (!empty($this->cycle) && $this->cycle->id != $this->items[0]->cycleid)
            {
                $this->cycle = $this->getModel('Cycle')->getItem($this->items[0]->cycleid);
            }
        }
        
        $this->stats        = KTBTrackerHelper::getCycleStats($this->cycle);
        dump($this->stats, 'CycleStats');
        $this->totals       = $this->getModel('KTBTracker')->getTrackingTotals($this->cycle);
        dump($this->totals, 'TrackingTotals');
        
        parent::display($tpl);
    }
}