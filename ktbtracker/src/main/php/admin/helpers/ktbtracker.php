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

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;


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
    
}