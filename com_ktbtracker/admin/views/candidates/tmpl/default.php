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
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user			= JFactory::getUser();
$today          = new DateTime();
?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker&view=candidates'); ?>" method="post" id="adminForm" name="adminForm">
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
					<th width="25%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_NAME'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CYCLE_NAME'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_TRACT'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_CURRICULUM'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_LAST_TRACKED'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_KTBTRACKER_HEADING_LAST_JOURNALED'); ?>
					</th>
					<th width="6%">
						<?php echo JText::_('Show'); ?>
					</th>
					<th width="2%">
						<?php echo JText::_('JGRID_HEADING_ID')?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) : 
						$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
						$canEdit	= $user->authorise('core.edit',			'com_ktbtracker.candidate.' . $item->id);
						$canEditOwn	= $user->authorise('core.edit.own',		'com_ktbtracker.candidate.' . $item->id) && $item->created_by == $user->id;
						
						if (!empty($item->last_tracked)) {
						    $last_t = $today->diff(new DateTime($item->last_tracked), true)->days;
						    if ($last_t > 5) {
						        $last_t_class = 'exclamation-circle" style="color: #D9534F;';
						    } elseif ($last_t > 3) {
						        $last_t_class = 'exclamation-triangle" style="color: #FFD51D;';
						    } else {
						        $last_t_class = 'check-square" style="color: #81CA0D;';
						    }
						} else {
						    $missed_days += 100;
						    $last_t_class = 'exclamation-circle" style="color: #D9534F';
						}
	
						if (!empty($item->last_journaled)) {
						    $last_j = $today->diff(new DateTime($item->last_journaled), true)->days;
						    if ($last_j > 5) {
						        $last_j_class = 'exclamation-circle" style="color: #D9534F;';
						    } elseif ($last_j > 3) {
						        $last_j_class = 'exclamation-triangle" style="color: #FFD51D;';
						    } else {
						        $last_j_class = 'check-square" style="color: #81CA0D;';
						    }
						} else {
						    $last_j_class = 'exclamation-circle" style="color: #D9534F';
						}	
					?>
				<tr>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'candidates', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<?php echo JHtml::_('ktbtracker.status', $i, $item->status, 'candidates.', ($canEdit || $canEditOwn)); ?>
						<a class="hasToolTip" href="<?php echo JRoute::_('index.php?option=com_ktbtracker&task=candidate.edit&id=' . $item->id); ?>" 
							title="<?php echo JText::_('JGLOBAL_EDIT'); ?>"><?php echo $this->escape($item->user_name); ?></a>
					<?php else : ?>
						<span title="<?php echo $this->escape($item->user_name); ?>"><?php echo $this->escape($item->user_name); ?></span>
					<?php endif; ?>	
					</td>
					<td>
						<?php echo $this->escape($item->cycle_name); ?>
					</td>
					<td>
						<?php echo $this->escape($item->tract_name); ?>
					</td>
					<td>
						<?php echo $this->escape($item->adult_name); ?>
					</td>
					<td>
						<i class="fa fa-<?php echo $last_t_class; ?>"></i>
						<?php echo JHtml::_('ktbformat.date', $item->last_tracked); ?>
					</td>
					<td>
						<i class="fa fa-<?php echo $last_j_class; ?>"></i>
						<?php echo JHtml::_('ktbformat.date', $item->last_journaled); ?>
					</td>
					<td align="center">
						<i class="fa fa-<?php echo ($item->hidden ? 'ban text-error' : 'check text-success'); ?> hasTip"
							title="<?php echo ($item->hidden ? JText::_('COM_KTBTRACKER_HIDDEN') : JText::_('COM_KTBTRACKER_VISIBLE')); ?>"?></i>
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
