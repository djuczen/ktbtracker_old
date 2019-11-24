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


JHtml::addIncludePath(JPATH_BASE . '/components/com_ktbtracker/helpers/html');

// Warning - Joomla! 3.x uses Bootstrap 2.3.2
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user			= JFactory::getUser();
$userId			= $user->id;

?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker&view=tracking'); ?>" method="post" id="adminForm" name="adminForm">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' => false))); ?>
<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
<?php else : ?>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th width="2%">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="14%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_DATE'); ?>
					</th>
					<th width="35%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_NAME'); ?>
					</th>
					<th width="35%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_NAME'); ?>
					</th>
					<th width="6%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_DAILY_JOURNAL'); ?>
					</th>
					<th width="6%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_DAILY_GOAL'); ?>
					</th>
					<th width="2%">
						<?php echo JText::_('JGRID_HEADING_ID')?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) : 
						$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canEdit	= $user->authorise('core.edit',			'com_ktbtracker.tracker.' . $item->id);
						$canEditOwn	= $user->authorise('core.edit.own',		'com_ktbtracker.tracker.' . $item->id) && $item->created_by == $userId;
						
				?>
				<tr>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'tracking', $canCheckin); ?>
					<?php endif; ?>
						<?php echo JHtml::_('date', $item->tracking_date, JText::_('DATE_FORMAT_LC4')); ?>
					</td>
					<td>
						<?php echo $this->escape($item->user_name); ?>
					</td>
					<td>
						<?php echo (empty($item->cycle_name) ? 'NOT_ON_CYCLE' : $this->escape($item->cycle_name)); ?>
					</td>
					<td>
						<?php echo 0; ?>
					</td>
					<td>
						<?php echo 0; ?>
					</td>
					<td align="center">
						<?php echo $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
<?php endif; ?>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
