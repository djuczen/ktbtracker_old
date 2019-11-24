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

use Joomla\Utilities\ArrayHelper;

// KTBTracker Component Defines
require_once JPATH_ADMINISTRATOR . '/components/com_ktbtracker/helpers/defines.php';

//jimport('Twilio.Services.Twilio');


/**
 * KTBTracker component helper class for HTML output
 * 
 * @since	1.0.0
 */
class JHtmlKTBTracker
{
	public static function status($i, $value = 0, $taskPrefix = '', $canChange = true)
	{
		$states = array(
				STATUS_AUDITING		=>	array('fa fa-eye', JText::_('COM_KTBTRACKER_STATUS_AUDITING'), '', 'auditing'),
				STATUS_CANDIDATE	=>	array('fa fa-user', JText::_('COM_KTBTRACKER_STATUS_CANDIDATE'), '', 'active'),
				STATUS_GRADUATE		=>	array('fa fa-graduation-cap', JText::_('COM_KTBTRACKER_STATUS_GRADUATE'), '', 'graduated'),
				STATUS_DNF			=>	array('fa fa-user-times', JText::_('COM_KTBTRACKER_STATUS_DNF'), '', 'dnf'),
		);
	
		$state = ArrayHelper::getValue($states, (int) $value, $states[0]);
		$icon = '<i class="icon-' . $state[0] . '"' . $state[2] . '></i>';
		$html = array();
		$html[] = '<div class="btn-group">';
		$html[] = '<a class="btn btn-micro hasTip" href="javascript://" title="' . $state[1] . '">' . $icon . '</a>';
		foreach ($states as $k => $state) {
			if ((int) $value == $k) continue;
			JHtml::_('actionsdropdown.addCustomItem', $state[1], $state[0], 'cb'. $i, $taskPrefix . $state[3]);
		}
		$html[] = JHtml::_('actionsdropdown.render', (int) $value);
		$html[] = '</div>';
	
		return implode('', $html);
	}
	
	/**
	 * Formats a number for presentation.
	 *
	 * If the number is an integer (or equivalent) a precision of 0 is used,
	 * otherwise a default 2-digit precision is used.
	 *
	 * @param float $number The number being formatted.
	 * @param int $decimals [optional] Sets the number of decimal points.
	 *
	 * @return string The number formatted with the optionally specified number
	 * 				of decimals and thousands separators.
	 */
	public static function formatted($number, $decimals = null, $default = '')
	{
		// Make sure we are working with a number, otherwise return '' if the
		// parameter is null or the parameter itself
		if (!is_numeric($number)) {
			if (is_null($number)) {
				return $default;
			}
				
			return $number;
		}
	
		// If precision is specified, use it
		if (is_numeric($decimals)) {
			return number_format($number, abs($decimals));
		}
	
		// If not an integer, use 1- or 2-digit precision
		if ((($number * 100) % 10) > 0) {
			return number_format($number, 2);
		}
		if ((($number * 10) % 10) > 0) {
			return number_format($number, 1);
		}
	
		// Return value formatted with no precision
		return number_format($number, 0);
	}
	
	/**
	 * 
	 * @param unknown $context
	 * @param array $data
	 * @param unknown $options
	 * @return string
	 */
	public static function doughnut($context, $data = array(), $options = array())
	{
		$html = array();
		
		$html[] = '<script type"=text/javascript">';
		$html[] = 'new Chart(jQuery("' . $context . '"), {';
		$html[] = ' type: "doughnut",';
		$html[] = ' data: ' . json_encode($data) . ',';
		$html[] = ' options: ' .json_encode($options);
		$html[] = '});';
		$html[] = '</script>';
		
		return implode('', $html);
	}
}