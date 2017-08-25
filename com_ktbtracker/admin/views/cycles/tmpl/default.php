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

JHtml::_('bootstrap.framework');
//JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user			= JFactory::getUser();
$userId			= $user->id;
$eventid 		= $this->state->get('eventid');

?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker&view=cycles'); ?>" method="post" id="adminForm" name="adminForm">
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
					<th width="50%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_NAME'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_START'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_FINISH'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_PRESTART'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_CUTOFF'); ?>
					</th>
					<th width="6%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CANDIDATES'); ?>
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
						$canEdit	= $user->authorise('core.edit',			'com_ktbtracker.cycle.' . $item->id);
						$canEditOwn	= $user->authorise('core.edit.own',		'com_ktbtracker.cycle.' . $item->id) && $item->created_by == $userId;
						
				?>
				<tr>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'cycles', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a class="hasToolTip" href="<?php echo JRoute::_('index.php?option=com_ktbtracker&task=cycle.edit&id=' . $item->id); ?>" 
							title="<?php echo JText::_('JGLOBAL_EDIT'); ?>"><?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<span title="<?php echo $this->escape($item->title); ?>"><?php echo $this->escape($item->title); ?></span>
					<?php endif; ?>	
					</td>
					<td>
						<?php echo JHtml::_('ktbformat.date', $item->cycle_start); ?>
					</td>
					<td>
						<?php echo JHtml::_('ktbformat.date', $item->cycle_finish); ?>
					</td>
					<td>
						<?php echo JHtml::_('ktbformat.date', $item->cycle_prestart); ?>
					</td>
					<td>
						<?php echo JHtml::_('ktbformat.date', $item->cycle_cutoff); ?>
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
