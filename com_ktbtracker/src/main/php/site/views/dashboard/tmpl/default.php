<?php
/**
 * @package		Joomla.Site
 * @subpackage 	com_ktbtracker
 *
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 *
 * $Id$
 */

defined('JPATH_PLATFORM') or die;


JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ktbtracker/helpers/html');

KTBTrackerHelper::loadBootstrap();
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


$user			= JFactory::getUser();
$userId			= $user->id;

dump($this->stats, 'stats');
dump($this->cycle, 'cycle');
dump($this->cycles, 'cycles');
KTBTrackerHelper::getPrevDate();
KTBTrackerHelper::getNextDate();
KTBTrackerHelper::getThisWeekDate();
KTBTrackerHelper::getPrevWeekDate();
KTBTrackerHelper::getNextWeekDate();
KTBTrackerHelper::getWeekDates();
KTBTrackerHelper::getWeekDates(KTBTrackerHelper::getPrevWeekDate());
KTBTrackerHelper::getWeekDates(KTBTrackerHelper::getNextWeekDate());
KTBTrackerHelper::getWeekDates('2016-07-20');
dump(KTBTrackerHelper::getWeekDays());

$stats = $this->stats;

?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container">
		<div class="center container">
			<div class="center row">
				<div class="col-xs-2">
					<i class="fa fa-chevron-left"></i>
				</div>
				<div class="col-xs-8">
				<?php if (!empty($stats->candidate)) : ?>
					<span class="boxed">
						<span style="width: 1em; word-wrap: break-word; letter-spacing: 1em;">DAY</span>
						<span><?php echo JHtml::_('ktbtracker.formatted', $stats->interval->day); ?></span>
						<span>$stats->cycle->title</span>
					</span>
				<?php else : ?>
					<span class="boxed">
						<span style="width: 1em; word-wrap: break-word; letter-spacing: 1em;">LIFETIME</span>
					</span>
				<?php endif; ?>
				</div>
				<div class="col-xs-2">
					<i class="fa fa-chevron-right"></i>
				</div>
			</div>
		</div>
		<div class="center container">
			<h3><?php echo JText::_('COM_KTBTRACKER_FIELDSET_PHYSICAL_LABEL'); ?></h3>
			<div class="center row-fluid">
			<?php foreach (KTBTrackerHelper::getPhysicalRequirements() as $requirement => $goal) : ?>
				<?php if (empty($goal)) { continue; } ?>
				<div class="center col-xs-6 col-md-3 col-lg-2">
					<div id="<?php echo $requirement; ?>"></div>
					<span class="progress-label"><?php echo JHtml::_('ktbtracker.formatted', $stats->tracking->$requirement, null, 0); ?><br/>
					<small><?php echo JText::_('COM_KTBTRACKER_FIELD_' . strtoupper($requirement) . '_SHORT'); ?></small></span>
					<?php echo JHtml::_('progressbar.circle', '#' . $requirement, JHtml::_('ktbtracker.formatted', $goal), $stats->progress->$requirement); ?>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
		<div class="center container">
			<h3><?php echo JText::_('COM_KTBTRACKER_FIELDSET_CLASSES_LABEL'); ?></h3>
			<div class="center row-fluid">
			<?php foreach (KTBTrackerHelper::getClassRequirements() as $requirement => $goal) : ?>
				<div class="center col-xs-6 col-md-3 col-lg-2">
					<div id="<?php echo $requirement; ?>"></div>
					<span class="progress-label"><?php echo JHtml::_('ktbtracker.formatted', $stats->tracking->$requirement, null, 0); ?><br/>
					<small><?php echo JText::_('COM_KTBTRACKER_FIELD_' . strtoupper($requirement) . '_SHORT'); ?></small></span>
				</div>
				<?php echo JHtml::_('progressbar.circle', '#' . $requirement, JHtml::_('ktbtracker.formatted', $goal), $stats->progress->$requirement); ?>
			<?php endforeach; ?>
			</div>
		</div>
		<div class="center container">		
			<h3><?php echo JText::_('COM_KTBTRACKER_FIELDSET_OTHER_LABEL'); ?></h3>
			<div class="center row-fluid">
			<?php foreach (KTBTrackerHelper::getOtherRequirements() as $requirement => $goal) : ?>
				<div class="center col-xs-6 col-md-3 col-lg-2">
					<div id="<?php echo $requirement; ?>"></div>
					<span class="progress-label"><?php echo JHtml::_('ktbtracker.formatted', $stats->tracking->$requirement, null, 0); ?><br/>
					<small><?php echo JText::_('COM_KTBTRACKER_FIELD_' . strtoupper($requirement) . '_SHORT'); ?></small></span>
				</div>
				<?php echo JHtml::_('progressbar.circle', '#' . $requirement, JHtml::_('ktbtracker.formatted', $goal), $stats->progress->$requirement); ?>
			<?php endforeach; ?>
			</div>
		</div>
		<div class="container">				
			<h1>Hi, I'm a dashboard!</h1>
		</div>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
