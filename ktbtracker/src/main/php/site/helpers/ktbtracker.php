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

use function FOFDatabaseQuery\dump;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;


abstract class KTBTrackerHelper extends ContentHelper
{
    protected static $physical_requirements = array(
        'miles',
        'pushups',
        'situps',
        'burpees',
        'kicks',
        'poomsae',
        'self_defense',
        'sparring',
        'jumps',
        'pullups',
        'rolls_falls',
    );
    
    protected static $class_requirements = array(
        'class_saturday',
        'class_weekday',
        'class_sparring',
        'class_pmaa',
        'class_masterq',
        'class_dreamteam',
        'class_hyperpro',
    );
    
    protected static $other_requirements = array(
        'meditation',
        'raok',
        'mentor',
        'mentee',
        'leadership',
        'leadership2',
        'journals',
    );
    
    protected $__cycleModel;
    
    protected $__requirementModel;
    
    protected $__trackingModel;
    
    
    public static function addSubmenu($submenu)
    {
        JHtmlSidebar::addEntry(
            Text::_('COM_KTBTRACKER_SUBMENU_XYZ'),
            'index.php?option=com_ktbtracker',
            $submenu == 'helloworlds'
        );  
    }
    
    public static function getCycleRequirements()
    {
        return array_merge(self::$physical_requirements, self::$class_requirements, self::$other_requirements);
    }
    
    public static function translate($string, $language_tag='')
    {
        static $loadedLanguages;
        
        if (!isset($loadedLanguages))
        {
            $loadedLanguages = array(Factory::getLanguage()->getTag());
        }
        
        if (empty($language_tag))
        {
            $language_tag = Factory::getLanguage()->getTag();
        }
        
        $lang = Language::getInstance($language_tag);
        
        if (!in_array($language_tag, $loadedLanguages))
        {
            $lang->load('com_ktbtracker'. JPATH_BASE, $language_tag);
            
            $loadedLanguages[] = $language_tag;
        }
        
        return $lang->_($string);
    }
    
    public static function daysFromCycleStart($cycle, $date = 'now')
    {
        $cycleStart = date_create($cycle->cycle_start);
        $fromDate   = date_create($date);
        
        return intval($cycleStart->diff($fromDate)->format("%r%a"));
    }
    
    public static function daysToCycleFinish($cycle, $date = 'now')
    {
        $cycleFinish    = date_create($cycle->cycle_finish);
        $fromDate       = date_create($date);
        
        return intval($fromDate->diff($cycleFinish)->format("%r%a"));
    }
    
    /**
     * 
     * @param mixed $cycle
     * @param mixed $userid
     * @return stdClass
     */
    public static function getCycleStats($cycle = null, $userid = null)
    {
        $stats = new stdClass();
        $stats->overall = 0.0;
        $reqmntCount = 0;
        
        if (empty($cycle)) {
            if (!isset($__cycleModel)) {
                BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
                $__cycleModel = AdminModel::getInstance('Cycle', 'KTBTrackerModel', array('ignore_request' => true));
            }
            $cycle = $__cycleModel->getCurrentCycle();
        }
        dump($cycle, 'Cycle');
        
        if (!isset($__trackingModel)) {
            BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
            $__trackingModel = AdminModel::getInstance('KTBTracker', 'KTBTrackerModel', array('ignore_request' => true));
        }
        $totals = $__trackingModel->getTrackingTotals($cycle);
        dump($totals, 'Totals');
        
        if (!isset($__requirementModel)) {
            BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
            $__requirementModel = AdminModel::getInstance('Requirement', 'KTBTrackerModel', array('ignore_request' => true));
        }
        $reqmnts = $__requirementModel->getLatest();
        dump($reqmnts, 'Requirements');
        
        // Calculate the percentage (0.0 - 1.0+) of cycle requirements completed
        foreach (self::getCycleRequirements() as $reqmnt)
        {
            if (!empty($reqmnts->$reqmnt))
            {
                $stats->$reqmnt = ($totals->$reqmnt / $reqmnts->$reqmnt);
            }
        }
        
        // Calculate the overall percentage (0.0 - 1.0+) pf cycle requirements completed
        foreach (self::getCycleRequirements() as $reqmnt)
        {
            $stats->overall += $stats->$reqmnt;
            $reqmntCount += 1;
        }
        $stats->overall = ($stats->overall / $reqmntCount);
        
        dump($stats, 'Stats');
        return $stats;
    }

    public static function getDailyStats($date = 'now', $cycle = null, $userid = null)
    {
        $stats = new stdClass();
        $stats->overall = 0.0;
        $reqmntCount = 0;
        $daysLeft = self::daysToCycleFinish($cycle);
        
        if (empty($cycle)) {
            if (!isset($__cycleModel)) {
                BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
                $__cycleModel = AdminModel::getInstance('Cycle', 'KTBTrackerModel', array('ignore_request' => true));
            }
            $cycle = $__cycleModel->getCycleForDate($date);
        }
        dump($cycle, 'Cycle');
        
        if (!isset($__trackingModel)) {
            BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
            $__trackingModel = AdminModel::getInstance('KTBTracker', 'KTBTrackerModel', array('ignore_request' => true));
        }
        $totals = $__trackingModel->getCycleTrackingTotals($cycle);
        dump($totals, 'Totals');
        
        if (!isset($__requirementModel)) {
            BaseDatabaseModel::addIncludePath(JPATH_COMPONENT . '/models');
            $__requirementModel = AdminModel::getInstance('Requirement', 'KTBTrackerModel', array('ignore_request' => true));
        }
        $reqmnts = $__requirementModel->getLatest();
        
        // Calculate the percentage (0.0 - 1.0+) of daily requirements completed
        foreach (self::getCycleRequirements() as $reqmnt)
        {
            $dailyMin = ($reqmnts->$reqmnt / $daysLeft);
            if (!empty($dailyMin)) {
                $stats->$reqmnt = ($totals->$reqmnt / $dailyMin);
            } else {
                $stats->$reqmnt = 0.0;
            }
        }
        
        // Calculate the overall percentage (0.0 - 1.0+) pf daily requirements completed
        foreach (self::getCycleRequirements() as $reqmnt)
        {
            $stats->overall += $stats->$reqmnt;
            $reqmntCount += 1;
        }
        $stats->overall = ($stats->overall / $reqmntCount);
        
        return $stats;
    }
}