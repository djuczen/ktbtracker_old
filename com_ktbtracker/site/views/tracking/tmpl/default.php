<?php
/**
 * @package		Joomla.Site
 * @subpackage 	com_ktbtracker
 *
 * @copyright	Copyright (C) 2012-@COPYR_YEAR@ David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 *
 * $Id$
 */

defined('JPATH_PLATFORM') or die;

include_once JPATH_ROOT . '/components/com_jsn/helpers/helper.php';


JHtml::addIncludePath(JPATH_BASE . '/components/com_ktbtracker/helpers/html');

JHtml::_('bootstrap.framework');



//$cycledays = date_diff(date_create($this->cycle->cycle_start), date_create($this->items[0]->tracking_date)); 
$cycleweek = KTBTrackerHelper::getCycleInterval($this->cycle, $this->items[0]->tracking_date, $this->candidate);
$cycle_start = JHtml::_('date', $this->cycle->cycle_start, 'F j, Y', null);
$cycle_finish = JHtml::_('date', $this->cycle->cycle_finish, 'F j, Y', null);
$week_start = JHtml::_('date', $this->items[0]->tracking_date, 'F j, Y', null);
$week_finish = JHtml::_('date', $this->items[6]->tracking_date, 'F j, Y', null);
$cycle_reqmnts = explode(',', $this->cycle->cycle_reqmnts);

?>
<div class="jsn-p">
	<div class="jsn-p-top jsn-p-top-a">
		<div class="jsn-p-avatar">
			<a href="<?php echo $this->candidate->link; ?>">
				<img src="<?php echo $this->candidate->profile->avatar_mini; ?>" alt="<?php echo $this->escape($this->candidate->display_name); ?>"
					class="hasTooltip" title="<?php echo $this->escape($this->candidate->profile->firstname); ?>">
			</a>
		</div>
		<div class="jsn-p-title">
			<h3><?php echo $this->escape($this->candidate->display_name); ?></h3>
			<div data-toggle="tooltip" title="<?php echo ($this->candidate->online) ? JText::_('COM_KTBTRACKER_STATUS_ONLINE') : JText::_('COM_KTBTRACKER_STATUS_OFFLINE'); ?>"  class="status label label-<?php echo ($this->candidate->online) ? 'success' : 'danger'; ?>"></div>
		</div>
		<div class="jsn-p-before-fields">
			<div class="jsn-p-dates">
				<div class="jsn-p-date-reg">
					<b><i class="fa fa-align-justify"></i> <?php echo JText::_('COM_KTBTRACKER_FIELD_TRACT_LABEL'); ?>: </b>
					<?php echo $this->escape($this->candidate->tract_name); ?>
				</div>
			</div>
			<div class="jsn-p-dates">
				&nbsp;
			</div>
		</div>
		<div class="jsn-p-fields">
			<fieldset class="jsn-form-fieldset" id="jsn_default" data-index="0" data-name="details">
				<dl class="dl-horizontal">
					<dt><?php echo JText::_('COM_KTBTRACKER_FIELD_ABOUTME_LABEL'); ?></dt>
					<dd>
						<?php echo array_key_exists('aboutme', $this->candidate->profile->profile) ? $this->escape($this->candidate->profile->profile['aboutme']) : JText::_('COM_KTBTRACKER_NO_INFORMATION_PROVIDED'); ?>
					</dd>
				</dl>
			</fieldset>
		</div>
	</div>
</div>
<div>
	<div class="table-responsive">
		<table class="tracking-table table table-bordered table-condensed">
			<thead>
				<tr>
					<th class="tracking-header text-center" colspan="12">
						<a class="btn btn-lg btn-default active pull-left" href="<?php echo $this->prevLink; ?>">
							<i class="fa fa-chevron-left"></i>
							<i class="fa fa-calendar"></i>
						</a>
						<a class="btn btn-lg btn-default active pull-right" href="<?php echo $this->nextLink; ?>">
							<i class="fa fa-calendar"></i>
							<i class="fa fa-chevron-right"></i>
						</a>
						<h4>&nbsp;&nbsp;<?php echo JText::sprintf('COM_KTBTRACKER_HEADING_CYCLE_TITLE', $this->escape($this->cycle->title), $cycle_start, $cycle_finish); ?>&nbsp;&nbsp;</h4>
						<h3><?php echo JText::sprintf('COM_KTBTRACKER_HEADING_CYCLE_WEEK', $cycleweek->week, $week_start, $week_finish); ?></h3>
					</th>
				</tr>
				<tr>
					<th width="25%"><?php echo JText::_('COM_KTBTRACKER_HEADING_REQUIREMENT'); ?></th>
					<?php foreach ($this->items as $d => $item) : ?>
					<th style="text-align: center;" width="<?php echo round(60/7); ?>%">
						<?php if ($item->editable) : ?>
						<a href="<?php echo $item->editLink; ?>" class="btn btn-default btn-xs hasTip" title="Edit::<?php echo $item->tracking_date; ?>">
							<i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo JText::_('COM_KTBTRACKER_BUTTON_EDIT_LABEL'); ?></a><br />
						<?php endif; ?>							
						<span class="hasTip" title="<?php echo $item->tracking_day; ?>::<?php echo $item->tracking_date; ?>"><?php echo $item->tracking_day; ?><br />
							<?php echo JHtml::_('date', $item->tracking_date, 'm-d', null); ?></span>
					</th>
					<?php endforeach; ?>
					<th width="3%"><?php echo JText::_('COM_KTBTRACKER_HEADING_TOTAL'); ?></th>
					<th width="9%"><?php echo JText::_('COM_KTBTRACKER_HEADING_PROGRESS'); ?></th>
					<th width="3%"><?php echo JText::_('COM_KTBTRACKER_HEADING_GOAL'); ?></th>
					<th width="3%"><?php echo JText::_('COM_KTBTRACKER_HEADING_REMAINING'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				    $label      = JText::_('COM_KTBTRACKER_FIELD_JOURNALS_LABEL');
				    $title      = $this->escape(JText::_('COM_KTBTRACKER_FIELD_JOURNALS_DESC'));
				    $total      = $this->stats->tracking->journals;
				    $progress   = $this->stats->progress->journals;
				?>
				<tr>
					<td style="text-align: left;">
						<span class="hasTooltip" title="<?php echo $title; ?>">	<strong><?php echo $label; ?></strong></span>
					</td>
				<?php foreach ($this->items as $d => $item) : ?>
					<?php if (empty($item->journals)): ?>
					<td style="text-align: center">-</td>
					<?php else: ?>
					<td style="text-align: center;">
						<span class="label label-success"> Y </span>
					</td>
					<?php endif; ?>
				<?php endforeach; ?>
					<td class="text-right" width="3%"><?php echo $this->escape($total); ?></td>
					<td class="text-center" width="9%">
						<div class="progress hasTooltip" data-toggle="tooltip" data-html="true" title="<?php echo $this->escape($progress->help); ?>">
							<div class="progress-bar progress-bar-<?php echo $progress->class; ?>" role="progressbar"
								aria-valuenow="<?php echo number_format($progress->progress, 0); ?>" aria-valuemin="0" aria-valuemax="100" 
								style="width: <?php echo $progress->progress; ?>%">
								<span class="sr-only"><?php echo number_format($progress, 2); ?>%</span>
							</div>
						</div>
					</td>
					<td class="text-right" width="3%">
						<?php echo JHtml::_('ktbformat.number', $progress->goal); ?><br/>
					</td>
					<td class="text-right" width="3%">
						<?php echo JHtml::_('ktbformat.number', $progress->remaining); ?><br/>
					</td>
				</tr>
			<?php foreach (KTBTrackerHelper::getCycleRequirements(true) as $reqmnt) : ?>
				<?php if (!in_array($reqmnt, $cycle_reqmnts)): continue; endif; ?>
				<?php if ($reqmnt == 'journals'): continue; endif; ?>
				<?php 
				    $label      = JText::_('COM_KTBTRACKER_FIELD_' . $reqmnt . '_LABEL');
				    $title      = $this->escape(JText::_('COM_KTBTRACKER_FIELD_' . $reqmnt . '_DESC'));
				    $total      = $this->stats->tracking->$reqmnt;
				    $progress   = $this->stats->progress->$reqmnt;
				    ?>
				<tr>
					<td style="text-align: left;">
						<span class="hasTooltip" title="<?php echo $title; ?>"><strong><?php echo $label; ?></strong></span>
					</td>
				<?php foreach ($this->items as $d => $item) : ?>
					<?php if (empty($item->created_by)) : ?>
					<td style="text-align: center">-</td>
					<?php else: ?>
					<td style="text-align: right;">
						<?php echo JHtml::_('ktbformat.number', $item->$reqmnt); ?>
					</td>
					<?php endif; ?>
				<?php endforeach; ?>
					<?php
					?>
					<td class="text-right" width="3%"><?php echo $this->escape($total); ?></td>
					<td class="text-center" width="9%">
						<div class="progress hasTooltip" data-toggle="tooltip" data-html="true" title="<?php echo $this->escape($progress->help); ?>">
							<div class="progress-bar progress-bar-<?php echo $progress->class; ?>" role="progressbar"
								aria-valuenow="<?php echo number_format($progress->progress, 0); ?>" aria-valuemin="0" aria-valuemax="100" 
								style="width: <?php echo $progress->progress; ?>%">
								<span class="sr-only"><?php echo number_format($progress, 2); ?>%</span>
							</div>
						</div>
					</td>
					<td class="text-right" width="3%">
						<?php echo JHtml::_('ktbformat.number', $progress->goal); ?><br/>
					</td>
					<td class="text-right" width="3%">
						<?php echo JHtml::_('ktbformat.number', $progress->remaining); ?><br/>
					</td>
				
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
