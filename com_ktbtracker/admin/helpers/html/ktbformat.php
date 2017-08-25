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

//jimport('Twilio.Services.Twilio');


/**
 * KTBTracker component helper class for HTML output
 * 
 * @since	1.0.0
 */
class JHtmlKTBFormat
{
	public static function number($value, $decimals = null, $default = '')
	{
		// Make sure we are working with a number, otherwise return default ('') if the
		// parameter is null, or the parameter itself if not null
		if (!is_numeric($value)) {
			if (is_null($value)) {
				return $default;
			}
		
			return $value;
		}
		
		// If precision is specified, use it
		if (is_numeric($decimals)) {
			return number_format($value, abs($decimals));
		}
		
		// If not an integer, use 1- or 2-digit precision
		if ((($value * 100) % 10) > 0) {
			return number_format($value, 2);
		}
		if ((($value * 10) % 10) > 0) {
			return number_format($value, 1);
		}
		
		// Return value formatted with no precision
		return number_format($value, 0);
		
	}
	
	public static function date($value, $format = null, $tz = null, $gregorian = false, $default = 'N/A')
	{
		// Make sure we are working with date, otherwise return default ('N/A')
		if (strtotime($value) > 0) {
			return JHtml::_('date', $value, $format, $tz, $gregorian);
		}
		
		return $default;
	}
}