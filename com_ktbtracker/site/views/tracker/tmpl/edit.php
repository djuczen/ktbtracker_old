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


JHtml::addIncludePath(JPATH_BASE . '/components/com_ktbtracker/helpers/html');

JHtmL::_('behavior.formvalidation');


$cycle_reqmnts = explode(',', $this->cycle->cycle_reqmnts);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'tracker.cancel' || document.formvalidator.isValid(document.id('tracker-form'))) {
			Joomla.submitform(task, document.getElementById('tracker-form'));
		}
	}
</script>
<div class="ktbtracker" id="ktbtracker">
	<div id="system-message-container">
	</div>
	<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="tracker-form"
		class="form-validate form-horizontal" enctype="multipart/form-data">
		<div class="text-center">
			<h3><?php echo $this->escape($this->candidate->display_name); ?></h3>
			<h4><?php echo JText::sprintf('COM_KTBTRACKER_HEADING_CYCLE_DAY', $this->stats->interval->day, JHtml::_('ktbformat.date', $this->form->getValue('tracking_date'))); ?></h4>
		</div>
		<fieldset>
	<?php foreach ($this->form->getFieldset('requirements') as $field) : ?>
		<?php $fieldname = $field->__get('fieldname'); ?>
		<?php if (in_array($fieldname, $cycle_reqmnts)) :?>
			<?php echo $field->__set('description', $this->stats->progress->$fieldname->help); ?>
			<div class="form-group">
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
				<span>(Daily Goal <?php echo JHtml::_('ktbformat.number', $this->stats->progress->$fieldname->daily); ?>)</span>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
		</fieldset>
		<div class="form-actions">
			<input class="btn btn-success" type="button" onclick="Joomla.submitbutton('tracker.save')" value="<?php echo JText::_('COM_KTBTRACKER_BUTTON_SAVE_LABEL'); ?>">
			<input class="btn btn-danger" type="button" onclick="Joomla.submitbutton('tracker.cancel')" value="<?php echo JText::_('COM_KTBTRACKER_BUTTON_CANCEL_LABEL'); ?>">
		</div>
		<?php echo $this->form->renderField('tracking_date'); ?>
		<?php echo $this->form->renderField('userid'); ?>
		<?php echo $this->form->renderField('cycleid'); ?>
    	<input type="hidden" name="trackingDate" value="<?php echo JFactory::getApplication()->input->getString('trackingDate', ''); ?>" />
    	<input type="hidden" name="trackingUser" value="<?php echo JFactory::getApplication()->input->getInt('trackingUser', ''); ?>" />
    	<input type="hidden" name="cycleid" value="<?php echo JFactory::getApplication()->input->getInt('cycleid', ''); ?>" />
    	<input type="hidden" name="task" value="" />
    	<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
