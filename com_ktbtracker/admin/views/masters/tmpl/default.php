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

// Warning - Joomla! 3.x uses Bootstrap 2.3.2
JHtml::_('bootstrap.framework');
//JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen', 'select');

$user			= JFactory::getUser();
$userId			= $user->id;

?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker&view=masters'); ?>" method="post" id="adminForm" name="adminForm">
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
					<th width="60%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_NAME'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_TRACT'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_JOURNEY_START'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_JOURNEY_FINISH'); ?>
					</th>
					<th width="6%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_HIDDEN'); ?>
					</th>
					<th width="2%">
						<?php echo JText::_('JGRID_HEADING_ID')?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) : 
						$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canEdit	= $user->authorise('core.edit',			'com_ktbtracker.master.' . $item->id);
						$canEditOwn	= $user->authorise('core.edit.own',		'com_ktbtracker.master.' . $item->id) && $item->created_by == $userId;
						
				?>
				<tr>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'masters', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<?php echo JHtml::_('ktbtracker.status', $i, $item->status, 'masters.', ($canEdit || $canEditOwn)); ?>
						<a class="hasToolTip" href="<?php echo JRoute::_('index.php?option=com_ktbtracker&task=master.edit&id=' . $item->id); ?>" 
							title="<?php echo JText::_('JGLOBAL_EDIT'); ?>"><?php echo $this->escape($item->user_name); ?></a>
					<?php else : ?>
						<span title="<?php echo $this->escape($item->user_name); ?>"><?php echo $this->escape($item->user_name); ?></span>
					<?php endif; ?>	
					</td>
					<td>
						<?php echo JText::_('COM_KTBTRACKER_RANK' . $item->tract . '_SHORT'); ?>
					</td>
					<td>
						<?php JHtml::_('date', $item->journey_start); ?>
					</td>
					<td>
						<?php echo JHtml::_('date', $item->journey_finish); ?>
					</td>
					<td align="center">
						<i class="fa fa-<?php echo ($item->hidden ? 'eye-slash' : 'eye'); ?>"></i>
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
