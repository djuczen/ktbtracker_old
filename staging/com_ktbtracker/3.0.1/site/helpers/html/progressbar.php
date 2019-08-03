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
 * KTBTracker component helper class for HTML output (progressbar.js)
 * 
 * @since	1.0.0
 */
class JHtmlProgressBar
{
	public static function mambo($context, $percent = 0, $color = null, $duration = null)
	{
		JHtml::script('com_ktbtracker/jquery.mambo.min.js', false, true);
 		
		$html = array();

		if ($percent < 0 || $percent > 100) {
			$percent = min($value, 0);  // Make sure it's at least 0
			$percent = max($value, 100);  // Make sure its no more than 100
		}
		
		if ($percent < 1) {
			$percent = round($percent * 100);
		}
			
		$html[] = '<script type="text/jvascript">';
		$html[] = 'jQuery(\'' . $context . '\').mambo({';
		$html[] = ' percentage: ' . strval($percent);
		$html[] = '});';
		$html[] = '</script>';

		return implode('', $html);
	}
	
	public static function pure($id, $percent = 0, $color = null, $duration = null)
	{
		JHtml::script('com_ktbtracker/jQuery-plugin-progressbar.js', false, true);
		JHtml::stylesheet('com_ktbtracker/jQuery-plugin-progressbar.css', array(), true);
		JFactory::getDocument()->addScriptDeclaration(
				'jQuery(\'.progress-bar\').loading();');
		
		$html = array();
		
		if ($percent < 0 || $percent > 100) {
			$percent = min($value, 0);  // Make sure it's at least 0
			$percent = max($value, 100);  // Make sure its no more than 100
		}
		
		if ($percent < 1) {
			$percent = round($percent * 100);
		}
			
		$html[] = '<div id="' . $id . '" class="progress-bar"';
		$html[] = ' data-percent="' . strval($percent) . '"';
		if (!is_null($color)) {
			$html[] = ' data-color="' . strval($color) . '"';
		}
		if (!is_null($duration)) {
			$html[] = ' data-duration="' . strval($duration) . '"';
		}
		$html[] = '></div>';
		
		return implode('', $html);
	}
	
	/**
	 * 
	 * @param unknown $context
	 * @param array $data
	 * @param unknown $options
	 * @return string
	 */
	public static function circle($context, $text = '', $value = 0, $color = '#333', $width = 10)
	{
		JHtml::script('com_ktbtracker/progressbar.min.js', false, true);
		
		$html = array();
		
		if (empty($text)) {
			$text = strval(round($value * 100)) . '%';
		}
		
		if ($value < 0 || $value > 1) {
			$value = max($value, 0);  // Make sure it's a least 0.0
			$value = min($value, 1);  // Make sure it's no more than 1.0
		}
		
		$html[] = '<script type"=text/javascript">';
		$html[] = 'var bar = new ProgressBar.Circle(\'' . $context . '\', {';
		$html[] = ' strokeWidth: ' . strval($width) . ',';
		$html[] = ' color: \'' . $color . '\',';
		$html[] = ' trailColor: \'#eee\',';
		$html[] = ' trailWidth: ' . strval($width) . ',';
		$html[] = ' text: {value: \'' . $text . '\'},';
		$html[] = '});';
		$html[] = 'bar.animate(' . $value . ');';
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
	public static function semicircle($context, $text = '', $value = 0, $color = '#333')
	{
		JHtml::script('com_ktbtracker/progressbar.min.js', false, true);
		
		$html = array();
		
		if (empty($text)) {
			$text = strval(round((float) $value * 100));
		}

		if ($value < 0 || $value > 1) {
			$value = max($value, 0);  // Make sure it's a least 0.0
			$value = min($value, 1);  // Make sure it's no more than 1.0
		}
		
		$html[] = '<script type"=text/javascript">';
		$html[] = 'var bar = new ProgressBar.SemiCircle(\'' . $context . '\', {';
		$html[] = ' strokeWidth: 6,';
		$html[] = ' color: \'' . $color . '\',';
		$html[] = ' trailColor: \'#eee\',';
		$html[] = ' trailWidth: 1,';
		$html[] = ' easing: \'easeInOut\',';
		$html[] = ' duration: 1400,';
		$html[] = ' svgStyle: null,';
		$html[] = ' text: {value: \'' . $text . '\', alignToBottom: false},';
		$html[] = '});';
		$html[] = 'bar.text.style.fontFamily = \'"Ralway", Helvetica, sans-serif\';';
		$html[] = 'bar.text.style.fontSize = \'2rem\';';
		$html[] = 'bar.animate(' . $value . ');';
		$html[] = '</script>';
		
		return implode('', $html);
	}
}