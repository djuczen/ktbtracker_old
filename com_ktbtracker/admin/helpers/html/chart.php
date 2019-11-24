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

//jimport('Twilio.Services.Twilio');


/**
 * KTBTracker component helper class for HTML output (Chart.js)
 * 
 * @since	1.0.0
 */
class JHtmlChart
{
	/**
	 * 
	 * @param unknown $context
	 * @param array $data
	 * @param unknown $options
	 * @return string
	 */
	public static function pie($context, $data = array(), $options = array())
	{
		JHtml::script('com_ktbtracker/Chart.min.js', false, true);
		 	
		$html = array();
		
		$html[] = '<script type"=text/javascript">';
		$html[] = 'new Chart(jQuery("' . $context . '"), {';
		$html[] = ' type: "pie",';
		$html[] = ' data: ' . json_encode($data) . ',';
		$html[] = ' options: ' .json_encode($options);
		$html[] = '});';
		$html[] = '</script>';
		
		return implode('', $html);
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
		JHtml::script('com_ktbtracker/Chart.min.js', false, true);
		
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