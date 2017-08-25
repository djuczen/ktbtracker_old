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


JHtml::addIncludePath(JPATH_BASE . '/components/com_ktbtracker/helpers/html');

KTBTrackerHelper::loadBootstrap();
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


$user			= JFactory::getUser();
$userId			= $user->id;
$eventid 		= $this->state->get('eventid');

?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker'); ?>" method="post" id="adminForm" name="adminForm">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<div class="row-fluid">
			<div class="span4">
				<canvas id="candidates" width="100%" height="100%"></canvas>
			</div>
			<div class="span4">
				<div id="candidates2"></div>
			</div>
			<div class="span4">
				<div id="candidates3"></div>
				<p>1,000</p>
				<p><small>steps</small></p>
			</div>
		</div>
		<?php 
			$data = array(
					"labels" => array("Red", "Blue", "Yellow"),
					"datasets" => array(
								array(
									"data" => array(300, 50, 100),
									"backgroundColor" => array("#FF6384", "#36A2EB", "#FFCE46"),
									"hoverBackroundColor" => array("#ff6384", "#36a2eb", "#ffce46"),
								),
							),
					);
			$options = array("cutoutPercentage" => 75);
			echo JHtml::_('chart.doughnut', '#candidates', $data, $options); 
			echo JHtml::_('progressbar.semicircle', '#candidates2', '', 1);
			echo JHtml::_('progressbar.circle', '#candidates3', '1000', 0.5);
			?>
		<h1>Hi, I'm a dashboard!</h1>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
