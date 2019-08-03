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


class JHtmlKTBTracker 
{
    public static function easypiechart($context, $percent = 0, $title = '', $width = 3)
    {
        $html = array();
        $pct_i = 0;
        $pct_t = 0;
        $extra = '';
        
        if (is_numeric($percent)) {
            $pct_i = intval($percent * 100);
            $pct_t = intval($percent * 100);
        }
        
        if (!empty($title))
        {
            $extra = ' title="' . $title . '"';
        }
        
        // Put $pct_i in the range of 0-100
        $pct_i = min(max($pct_i, 0), 100);
        
        $html[] = '<div id="' . $context . '" class="easy-pie-chart" data-percent="' . $pct_i . '"' . $extra . '>';
        $html[] = '<span>' . $pct_t . '%</span>';
        $html[] = '<script type="text/javascript">';
        $html[] = "jQuery(function() {";
        $html[] = "jQuery('#" . $context . "').easyPieChart({";
        $html[] = "barColor: colorScale,";
        $html[] = "trackColor: '#eee',";
        $html[] = "scaleColor: false,";
        $html[] = "lineWidth: " . $width . ",";
        $html[] = "animate: 1000,";
        $html[] = "size: 60";
        $html[] = "});";
        $html[] = "});";
        $html[] = '</script>';
        $html[] = '</div>';
        
        return implode('', $html);
    }
}